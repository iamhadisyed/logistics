$(document).on('click', '#download_tariff_remotarea_template', function () {
    var tariffId = $('#upload_tariff_id').val();
    var carrierId = $('#upload_carrier_id').val();
    var remoteareaType = $('#upload_remotearea_type').val();
    $('#download_csv_tariffId').val(tariffId);
    $('#download_csv_carrierId').val(carrierId);
    $('#download_csv_remoteareaType').val(remoteareaType);
    $('#download_tariff_remotearea_template_form').submit();
});

$(document).on('click', '#btnSubmitImportRemoteArea', function () {
    var file_data = $('#tariff_upload_remote_file').prop('files')[0];
    var tariffId = $('#upload_tariff_id').val();
    var carrierId = $('#upload_carrier_id').val();
    var remoteareaType = $('#upload_remotearea_type').val();
    var form_data = new FormData();
    form_data.append('csv_file', file_data);
    form_data.append('func', 'upload_csv_file_remotearea');
    form_data.append('tariffId', tariffId);
    form_data.append('remotearea_type', remoteareaType);
    if (tariffId == "" || tariffId == null) {
        swal("error", "Your tariff id not found!", "error");
    } else {
        if (tariffId > 0) {
            $.ajax({
                url: 'tariff_remotearea_charges.php',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    // $('#upload_tariff_remotearea_console_window').append(response.message);
                    // $('#upload_tariff_remotearea_console_window').show();
                    getRemoteAreaTariffCharges(tariffId, carrierId, remoteareaType, response.status, response.message);
                }
            });
        } else {
            $("#btnSubmitImport").show();
            swal("Sorry!", "Please select the user first", "error");
        }
    }
});


function getRemoteAreaTariffCharges(tariffId, carrierId, remoteareaType, sstatus = '', smessage = '') {
    $("#res_message_remoterea_supplier").hide();


    $.ajax({
        url: 'tariff_remotearea_charges.php',
        type: 'POST',
        dataType: "html",
        data: {action: 'get_remotearea_traiff_charges', tariff_id: tariffId, carrier_id: carrierId, remotearea_type: remoteareaType, tstatus: sstatus, tmessage: smessage},
        headers: {
        },
        success: function (data) {
            $("#remotearea_apend_remoterea_supplier").html("");
            $("#remotearea_apend_remoterea_supplier").html(data);
            $(".remotearea_group").select2();
            $('#save_remotearea_charges_for_supplier').attr('data-tariff_id', tariffId);
        },
        error: function (xhr, status, error) {
        }
    });


}

//    Remotearea add more button script code
$(document).on('click', '.repeater_add_more', function () {
    var tariffId = $(this).attr("data-tariff_id");
    var index_of_remotearea = $(".show_remotearea_remove_btn").map(function () {
        return $(this).data('index_of_remotearea_remove');
    }).get();//get all data values in an array
    var highest_index_of_remotearea = Math.max.apply(Math, index_of_remotearea);//find the highest value from them
    highest_index_of_remotearea = parseInt(highest_index_of_remotearea) + 1;
    $("#remotearea_apend_remoterea_supplier .parent_clone_div_remotearea .clone_div_remotearea").children().clone().appendTo("#remotearea_apend_remoterea_supplier .parent_clone_div_remotearea .append_here_remotearea");
    $('#remotearea_apend_remoterea_supplier .append_here_remotearea .row').last().attr('data-index_of_remotearea', highest_index_of_remotearea);
    $('#remotearea_apend_remoterea_supplier .append_here_remotearea .row .show_remotearea_remove_btn').last().attr('data-index_of_remotearea_remove', highest_index_of_remotearea);
    $('#remotearea_apend_remoterea_supplier .append_here_remotearea .row .show_remotearea_remove_btn').show();

    setInputFeildsRemotearea(highest_index_of_remotearea);
});
 

// Set input fields empty Remotearea
function setInputFeildsRemotearea(id) {
    $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] .remotearea_group').next().remove();
    $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] :text').val("");
    var select2Parentid = $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] .remotearea_group').select2();
    select2Parentid.val("").trigger('change');
}
$(document).on('click', '.repeater_add_mores', function () {
    var tariffId = $(this).attr("data-tariff_id");
    var index_of_remotearea = $(".show_remotearea_remove_btn").map(function () {
        return $(this).data('index_of_remotearea_remove');
    }).get();//get all data values in an array
    var highest_index_of_remotearea = Math.max.apply(Math, index_of_remotearea);//find the highest value from them
    highest_index_of_remotearea = parseInt(highest_index_of_remotearea) + 1;
    $("#remotearea_apend_remoterea_suppliers .parent_clone_div_remotearea .clone_div_remotearea").children().clone().appendTo("#remotearea_apend_remoterea_suppliers .parent_clone_div_remotearea .append_here_remotearea");
    $('#remotearea_apend_remoterea_suppliers .append_here_remotearea .row').last().attr('data-index_of_remotearea', highest_index_of_remotearea);
    $('#remotearea_apend_remoterea_suppliers .append_here_remotearea .row .show_remotearea_remove_btn').last().attr('data-index_of_remotearea_remove', highest_index_of_remotearea);
    $('#remotearea_apend_remoterea_suppliers .append_here_remotearea .row .show_remotearea_remove_btn').show();

    setInputFeildsRemotearea(highest_index_of_remotearea);
});
//    Remotearea add more button script code
 
//    Remotearea remove button script code    
$(document).on('click', '.show_remotearea_remove_btn', function () {
    var current_index = $(this).attr('data-index_of_remotearea_remove');
    var myThis = $(this);
    swal({
        title: "Are you sure you want to remove this?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
            function (isConfirm) {
                if (isConfirm) {
                    myThis.parent(".row").remove();
                }
            });
});

 

//Save remotearea functionality
$(document).on('click', '.save_remotearea_tariff_data', function () {
    var myThis = $(this);
    var $nonempty = $('.validate_check').filter(function () {
        if (!$(this).val()) {
            $(this).parents(".input-group").css('border', '1px solid red');
        } else {
            $(this).parents(".input-group").css('border', '0px');
        }
        return !$(this).val();
    });
    if ($nonempty.length == 0) {
        var mayTariffs = false;
        var tariffId = $(this).attr("data-tariff_id");
        var remoteareaType = $('#upload_remotearea_type').val();
        var userAccountId = $("#id").val();
        var group = $('.remotearea_group').serialize();
        var fromWeight = $('.remotearea_from_weight').serialize();
        var toWeight = $('.remotearea_to_weight').serialize();
        var charges = $('.remotearea_charges').serialize();
        var formula = $('.remotearea_formula').serialize();
        var check = true;
        if (remoteareaType == "ON_PIECE") {
            check = checkit();
        }
        // var check = checkit();

        if (check == true) {
            $.ajax({
                url: 'tariff_remotearea_charges.php',
                type: 'POST',
                dataType: "json",
                data: {action: 'save_remotearea_charges', tariff_id: tariffId, remotearea_type: remoteareaType, group: group, from_weight: fromWeight, to_weight: toWeight, charges: charges, formula: formula, user_account_id: userAccountId},
                headers: {
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $("#res_message_remoterea_supplier").addClass('alert-success').removeClass('alert-danger');
                        $("#res_message_remoterea_supplier").html("");
                        $("#res_message_remoterea_supplier").html(data.message);
                        $("#res_message_remoterea_supplier").show();
                        myThis.attr('data-remotearea-saved', 'ok');
                         swal("Success!", "Remote Area Tariff Saved Successfully", "success");
                    }else{
                        swal("Error!", "Something went wrong! RemoteArea not saved", "error");
                    }
                },
                error: function (xhr, status, error) {
                    $("#res_message_remoterea_supplier").html("Please check data your are trying to save.");
                    $("#res_message_remoterea_supplier").show();
                }
            });
        }

    } else {
        swal("", "Please fill the required fields!", "error");
    }

});

//Save remotearea functionality
$(document).on('click', '.save_remoteareas_tariff_data', function () {
    var myThis = $(this);
    var $nonempty = $('.validate_checks').filter(function () {
        if (!$(this).val()) {
            $(this).parents(".input-group").css('border', '1px solid red');
        } else {
            $(this).parents(".input-group").css('border', '0px');
        }
        return !$(this).val();
    });
    if ($nonempty.length == 0) {
        var mayTariffs = true;
        var remoteareaType = $('#upload_remotearea_type').val();
        var userAccountId = $("#id").val();
        var group = $('.remotearea_group').serialize();
        var fromWeight = $('.remotearea_from_weight').serialize();
        var toWeight = $('.remotearea_to_weight').serialize();
        var charges = $('.remotearea_charges').serialize();
        var formula = $('.remotearea_formula').serialize();
        var check = true;
        if (remoteareaType == "ON_PIECE") {
            check = checkit();
        }
        // var check = checkit();

        if (check == true) {
            $.ajax({
                url: 'tariff_remotearea_charges.php',
                type: 'POST',
                dataType: "json",
                data: {action: 'save_remotearea_charges', mayTariffs, remotearea_type: remoteareaType, group: group, from_weight: fromWeight, to_weight: toWeight, charges: charges, formula: formula, user_account_id: userAccountId},
                headers: {
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $("#res_message_remoterea_suppliers").addClass('alert-success').removeClass('alert-danger');
                        $("#res_message_remoterea_suppliers").html("");
                        $("#res_message_remoterea_suppliers").html(data.message);
                        $("#res_message_remoterea_suppliers").show();
                        myThis.attr('data-remotearea-saved', 'ok');
                        swal("Success!", "Remote Area Tariff Saved Successfully", "success");
                    }else{
                        swal("Error!", data.message, "error");
                    }
                },
                error: function (xhr, status, error) {
                    $("#res_message_remoterea_suppliers").html("Please check data your are trying to save.");
                    $("#res_message_remoterea_suppliers").show();
                }
            });
        }

    } else {
        swal("", "Please fill the required fields!", "error");
    }

});

 
//Check if remoteareas assigned duplicate group values
function checkit() {
    var checker = [];
    var is_ok = true;
    $(".remotearea_group").each(function () {
        var selection = $(this).val();
        if (checker[selection]) {
            //if the property is defined, then we've already encountered this value
            swal("", "Can't duplicate groups", "error");
            is_ok = false;
            return;
        } else {
            checker[selection] = true;
        }
    });
    return is_ok;
}

 