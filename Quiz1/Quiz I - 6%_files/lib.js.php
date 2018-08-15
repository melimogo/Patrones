
var udem = {};
udem.proxy_url = 'https://uvirtual.udem.edu.co/mod/quiz/report/discussion/proxy.php';

udem.message_type = { information : 1, error : 2};

udem.print_message = function(text, type) {
    
    var style = 'ui-state-highlight';

    if (type == udem.message_type.error) {
        style = 'ui-state-error';
    }
    
    $('#udem_explanation_message_window').html('<div class="ui-corner-all ' + style + '">' + text + '</div>');
    $('#udem_explanation_message_window').dialog('open');
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

udem.explanation = function(question_id) {
    $('#udem_explanation_window textarea').val('');
    $('#udem_explanation_window').dialog('open');
    udem.active_question = question_id;
};

udem.approve = function(question_id, explanation_id, slot, acepted) {
    if (acepted) {
        $('#udem_approve_confirm_window').dialog('open');
    }
    else {
        $('#udem_unapprove_confirm_window').dialog('open');
        $('#observations_box').val('');
    }
    udem.active_question = question_id;
    udem.active_explanation = explanation_id;
    udem.active_slot = slot;
};

udem.send_explanation = function() {
    $('#udem_explanation_confirm_window').dialog('close');
    $('#udem_explanation_loading_window').show();
    
    var data_values = {};
    data_values.action = 'save_explanation';
    data_values.explanation = $('#udem_explanation_window textarea').val();
    data_values.qid = udem.active_question;
    data_values.attempt = $('input#attempt_id').val();
    
    $.ajax({
        url: udem.proxy_url,
        data: data_values,
        type: 'POST',
        success: function(data) {
            if (data.comment) {
                $('#udem_explanation_window').dialog('close');

                udem.print_message(data.comment, udem.message_type.information);

                $('#explanation_box_' + udem.active_question).html(data_values.explanation);
                $('#explanation_box_' + udem.active_question).removeClass('explanation_box');
                $('#explanation_box_' + udem.active_question).addClass('udem_answer_explanation ui-state-highlight');

                if (data.explanations >= 2) {
                    $('.explanation_box').html('Ya superó el número de revisiones permitidas (2).');
                    $('.explanation_box').addClass('udem_allowed_explanations');
                }
            }
            else {
                udem.print_message(data, udem.message_type.error);
            }
        },
        error: function(data) {
            udem.print_message(data, udem.message_type.error);
        },
        complete: function(data) {
            $('#udem_explanation_loading_window').hide();
        }
    });
};

udem.send_approve = function(acepted) {
    if (acepted) {
        $('#udem_approve_confirm_window').dialog('close');
    }
    else {
        $('#udem_unapprove_confirm_window').dialog('close');
    }

    $('#udem_explanation_loading_window').show();
    
    var data_values = {};
    data_values.action = 'approve_explanation';
    data_values.acepted = acepted ? 1 : 0;
    data_values.eid = udem.active_explanation;
    data_values.slot = udem.active_slot;
    data_values.attempt = $('input#attempt_id').val();
    data_values.observations = $('#observations_box').val();
    
    $.ajax({
        url: udem.proxy_url,
        data: data_values,
        type: 'POST',
        success: function(data) {
            if (data.comment) {
                udem.print_message(data.comment, udem.message_type.information);
                var refresh_link = $('#refresh_url').html();

                var observation = '';
                if (!data_values.acepted) {
                    observation = '<br /><br />' + data_values.observations;
                }
                $('#udem_answer_explanation_state_' + udem.active_question).html(data.comment + refresh_link + observation);
                $('#udem_answer_explanation_state_' + udem.active_question).addClass('ui-state-highlight');
            }
            else {
                udem.print_message(data, udem.message_type.error);
            }
        },
        error: function(data) {
            udem.print_message(data, udem.message_type.error);
        },
        complete: function(data) {
            $('#udem_explanation_loading_window').hide();
        }
    });
};

udem.show_explanation_detail = function(id, attempt, slot) {

    $('#udem_explanation_loading_window').show();
    
    var data_values = {};
    data_values.action = 'get_details';
    data_values.eid = id;
    data_values.attempt = attempt;
    data_values.slot = slot;

    $.ajax({
        url: udem.proxy_url,
        data: data_values,
        type: 'GET',
        success: function(data) {
            $('#udem_details_window').html(data);
            $('#udem_details_window').dialog('open');
        },
        error: function(data) {
            udem.print_message(data, udem.message_type.error);
        },
        complete: function(data) {
            $('#udem_explanation_loading_window').hide();
        }
    });    
}

udem.show_short_explanation_detail = function(id, attempt, slot) {

    $('#udem_explanation_loading_window').show();
    
    var data_values = {};
    data_values.action = 'get_short_details';
    data_values.eid = id;
    data_values.attempt = attempt;
    data_values.slot = slot;

    $.ajax({
        url: udem.proxy_url,
        data: data_values,
        type: 'GET',
        success: function(data) {
            $('#udem_details_window').html(data);
            $('#udem_details_window').dialog('open');
        },
        error: function(data) {
            udem.print_message(data, udem.message_type.error);
        },
        complete: function(data) {
            $('#udem_explanation_loading_window').hide();
        }
    });    
}