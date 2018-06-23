function formatDate(date, pattern) {
    var dateObj = null;
    var arr = date.split("/", 3);
    if (arr.length == 1) {
        arr = date.split("-", 3);
    }
    switch (pattern) {
        // from standand to d/m/y
        case 'd/m/Y':
            dateObj = arr[2] + "/" + arr[1] + "/" + arr[0];
            break;
        // from d/m/y to standand
        case 'Y/m/d':
            dateObj = arr[2] + "/" + arr[1] + "/" + arr[0];
            break;
    }
    return dateObj;
}

function checkDupeMember(employee_id_selected, employee_name_selected, start_date_process_selected, end_date_process_selected) {
    if ($('#list_add input').hasClass("employee_id")) {
        var employees = $('.employee_id');
        var delete_flags = $('.delete_flag');
        var start_date_processes = $('.start_date_process');
        var end_date_processes = $('.end_date_process');

         start_date_process_selected = new Date(start_date_process_selected);
         end_date_process_selected = new Date(end_date_process_selected);
        if (employees && employees.length) {
            for (var i = 0; i < employees.length; i++) {
                if($(delete_flags[i]).val()!=='0'){
                    continue;
                }
                if ($(employees[i]).val() === employee_id_selected) {
                    var start_date_process = new Date($(start_date_processes[i]).val());
                    var end_date_process = new Date($(end_date_processes[i]).val());
                    if (end_date_process_selected < start_date_process || end_date_process < start_date_process_selected) {
                        continue;
                    }
                    var string_error =
                        lang.getString('error_duplicate_member', [
                            employee_name_selected, employee_id_selected,
                            formatDate($(start_date_processes[i]).val(), 'd/m/Y'),
                            formatDate($(end_date_processes[i]).val(), 'd/m/Y'),
                        ]);
                    $('#list_error').html('');
                    $('#list_error').css('display', 'block');
                    $(document).scrollTop($("#list_error").offset().top);
                    $('#list_error').prepend(string_error);
                    return true;
                }
            }
            return false;
        }
    }
    return false;
}

function checkPOProcess(employee_role_selected, employee_name_selected, employee_id_selected, start_date_process_selected, end_date_process_selected, name_role_po) {
    if ($('#list_add input').hasClass("role_id")) {
        var employees = $('.role_id');
        var delete_flags = $('.delete_flag');
        var start_date_processes = $('.start_date_process');
        var end_date_processes = $('.end_date_process');
        var employee_names = $('.employee_name');
        var role_po = '';
        $('#role option').each(function (key, value) {
            if (value.text === name_role_po) {
                role_po = value.value;
                return false;
            }
        });
        start_date_process_selected = new Date(start_date_process_selected);
        end_date_process_selected = new Date(end_date_process_selected);
        if (employees && employees.length) {
            for (var i = 0; i < employees.length; i++) {
                if (role_po === employee_role_selected) {
                    if($(delete_flags[i]).val()!=='0'){
                        continue;
                    }
                    if ($(employees[i]).val() === employee_role_selected) {
                        var start_date_process = new Date($(start_date_processes[i]).val());
                        var end_date_process = new Date($(end_date_processes[i]).val());
                        if (end_date_process_selected < start_date_process || end_date_process < start_date_process_selected) {
                            continue;
                        }
                        var string_error =
                            lang.getString('error_P0_process', [
                                employee_name_selected, employee_id_selected,
                                formatDate($(start_date_processes[i]).val(), 'd/m/Y'),
                                formatDate($(end_date_processes[i]).val(), 'd/m/Y'),
                                $(employee_names[i]).text()
                            ]);
                        $('#list_error').html('');
                        $('#list_error').css('display', 'block');
                        $(document).scrollTop($("#list_error").offset().top);
                        $('#list_error').prepend(string_error);
                        return true;
                    }
                }
            }
            return false;
        }
    }
    else{
        return false;
    }

}

function getEstimateCost(date1, date2, man_power) {
    var salary = 10000000;
    var t1 = new Date(date1);
    var t2 = new Date(date2);
    var dif = t2.getTime() - t1.getTime();
    dif = Math.abs(dif / (1000 * 3600 * 24));
    return dif * man_power * salary;
}

function calculateEstimateCost() {
    var delete_flag = $('.delete_flag');

    var manpowers = $('.man_power').map(function () {
        return $(this).text();
    });
    var start_date_processes = $('.start_date_process').map(function () {
        return $(this).val();
    });
    var end_date_processes = $('.end_date_process').map(function () {
        return $(this).val();
    });
    var total_cost = 0;
    for (var i = 0; i < manpowers.length; i++) {
        if($(delete_flag[i]).val()==='0'){
            total_cost = total_cost + getEstimateCost(end_date_processes[i], start_date_processes[i], manpowers[i]);
        }
    }
    total_cost = total_cost.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,") + " VND";
    return total_cost;
}

function reopenAjax(url, token) {
    var project_id = $('#project_id');
    if(project_id.length>0){
        project_id=project_id.val();
    }else{
        project_id=null;
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            project_id: project_id,
            _token: token,

        },
        success: function (json) {
            if (json.hasOwnProperty('msg_success')) {
                enableAll();
                $('#end_date_project').val('');
                alert(json['msg_success']);
            }
            if (json.hasOwnProperty('msg_fail')) {
                alert(json['msg_fail']);
            }
        },
        error: function (json) {
            if (json.status === 422) {
                var errors = json.responseJSON;
                $.each(json.responseJSON, function (key, value) {
                    $('#list_error').html(value);

                });
            } else {
                // Error
                // Incorrect credentials
                // alert('Incorrect credentials. Please try again.')
            }
        }
    });
}

function requestAjax(url, token) {
    var project_id = $('#project_id');
    if(project_id.length>0){
        project_id=project_id.val();
    }else{
        project_id=null;
    }
    var employee_id = $('#employee_id :selected').val();
    var employee_name = $('#employee_id :selected').text();
    var estimate_start_date = $('#estimate_start_date').val();
    var estimate_end_date = $('#estimate_end_date').val();
    var start_date_project = $('#start_date_project').val();
    var end_date_project = $('#end_date_project').val();
    var end_date_process = $('#end_date_process').val();
    var start_date_process = $('#start_date_process').val();
    var man_power = $('#man_power').val();
    var role = $('#role').val();
    var role_name = $('#role :selected').text();

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            project_id: project_id,
            employee_id: employee_id,
            estimate_start_date: estimate_start_date,
            estimate_end_date: estimate_end_date,
            start_date_project: start_date_project,
            end_date_project: end_date_project,
            end_date_process: end_date_process,
            start_date_process: start_date_process,
            man_power: man_power,
            role_id: role,
            _token: token,

        },
        success: function (json) {
                $('#table_add tr').css('background', 'none');
                if (json.hasOwnProperty('available_processes')) {
                    $(document).scrollTop($("#list_error").offset().top);
                    var errors = '';
                    console.log(json);
                    $('#list_error').html('');
                    $.each(json[0], function (key, value) {
                            $.each(value,function (key1,value1) {
                                if (value1 && value1.length) {
                                    errors = errors + "<strong>"+lang.getString('error')+"!</strong> " + value1 + "<br>";
                                }
                            })
                    });
                    if (json['available_processes'] && json['available_processes'].length) {
                        var string_available_processes = '';
                        $.each(json['available_processes'], function (key, value) {
                            string_available_processes +=
                                lang.getString("id")+": " + value['project_id'] + ", " +
                                lang.getString("man_power")+": " + value['man_power'] + ", " +
                                lang.getString("process_start_date")+": " + formatDate(value['start_date'], 'd/m/Y') + ", " +
                                lang.getString("process_end_date")+": " + formatDate(value['end_date'], 'd/m/Y') + "<br>";
                        });
                        var string_total = lang.getString("error_available_processes")+" : <br>" + string_available_processes;
                        $('#list_error').prepend(string_total);
                    }
                    $('#list_error').css('display', 'block');
                    $('#list_error').prepend(errors);
                }

            if (json.hasOwnProperty('msg_success')) {
                $('#list_error').html('');
                $('#list_error').css('display', 'none');
                alert(json['msg_success']);
                var id_member = $('#employee_id :selected').val();
                var classRole = "";
                if (role_name.trim() == "PO") {
                    classRole = 'label label-primary';
                } else if (role_name.trim() == "Dev") {
                    classRole = 'label label-success';
                } else if (role_name.trim() == "BA") {
                    classRole = 'label label-info';
                    ;
                } else if (role_name.trim() == "ScrumMaster") {
                    classRole = 'label label-warning';
                }

                var element =
                    "<tr id=\"tr_member_" + id_member + "_" + end_date_process +"\">" +
                    "<td class=\"employee_name\" style=\"width: 17%;\" >" + employee_name + "</td>" +
                    "<td class=\"man_power\" style=\"width: 17%;\" ><span class=\"badge\">" + man_power + "</span></td>" +
                    "<td class=\"roles\" style=\"width: 17%;\" ><span class='" + classRole + "'>" + role_name + "</span></td>" +
                    "<td  style=\"width: 27%;\" >" + formatDate(start_date_process, 'd/m/Y') + "</td>" +
                    "<td  >" + formatDate(end_date_process, 'd/m/Y') + "</td>" +
                    "<td> <a>" +
                    "<i name=\"" + employee_name + "\" id=\"" + id_member + "\" class=\"fa fa-remove remove_employee\"></i>" +
                    "</a> </td>" +
                    "<input class=\"process_id\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][id]\"/>" +
                    "<input class=\"delete_flag\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][delete_flag]\" value=\"0\"/>" +
                    "<input class=\"employee_id\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process + "][employee_id]\" value=\"" + id_member + "\"/>" +
                    "<input class=\"role_id\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][role_id]\" value=\"" + role + "\"/>" +
                    "<input class=\"start_date_process\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][start_date_process]\" value=\"" + start_date_process + "\"/>" +
                    "<input class=\"end_date_process\" type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][end_date_process]\" value=\"" + end_date_process + "\"/>" +
                    "<input type=\"hidden\" name=\"processes[" + id_member + "_" + end_date_process +"][man_power]\" value=\"" + man_power + "\"/>" +

                    "</tr>"

                $('#table_add').css('display', 'block');
                $('#list_add').prepend(element);
                $('#estimate_cost').val(calculateEstimateCost());

            }
            if (json.hasOwnProperty('msg_fail')) {
                alert(json['msg_fail'])
            }
        },
        error: function (json) {
            if (json.status === 422) {
                var errors = json.responseJSON;
                $.each(json.responseJSON, function (key, value) {
                    $('#list_error').html(value);

                });
            } else {
                // Error
                // Incorrect credentials
                // alert('Incorrect credentials. Please try again.')
            }
        }
    });
}


// function removeAjax(id, target, url, token) {
//     var object_this = target;
//     $.ajax({
//         url: url,
//         type: 'POST',
//         data: {
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             _token: token,
//             id: id,
//         },
//         success: function (json) {
//             if (json.hasOwnProperty('msg_success')) {
//                 alert(json['msg_success']);
//                 object_this.remove();
//                 $('#estimate_cost').val(calculateEstimateCost());
//                 $('#member_' + id).prop('disabled', false);
//                 $('#employee_id').select2();
//             }
//             if (json.hasOwnProperty('msg_fail')) {
//                 alert(json['msg_fail'])
//             }
//
//         },
//         error: function (json) {
//             if (json.status === 422) {
//                 var errors = json.responseJSON;
//                 $.each(json.responseJSON, function (key, value) {
//                     $('#list_error').html(value);
//                 });
//             } else {
//                 // Error
//                 // Incorrect credentials
//                 // alert('Incorrect credentials. Please try again.')
//             }
//         }
//     });
// }

function resetFormAddProject() {
    $('#list_add').empty();
    $("#list_error").empty();
    $("#list_error").css('display', 'none');
    $("#id").val('');
    $("#name").val('');
    $("#estimate_start_date").val('');
    $("#estimate_end_date").val('');
    $("#start_date_project").val('');
    $("#end_date_project").val('');
    $("#employee_id").val('').change();
    $("#man_power").val('').change();
    $("#role").val('').change();
    $("#start_date_process").val('').change();
    $("#end_date_process").val('').change();
    $("#income").val('');
    $("#real_cost").val('');
    $("#description").val('');
    $("#status").val('').change();
    $(document).scrollTop($("#list_error").offset().top);
}

function removeEmployee(employee_id, target_tr, target_input) {

    if (target_input && (target_input.val())) {
        target_tr.find("input.delete_flag").attr('value','1');
        target_tr.css('display', 'none');

    } else {
        target_tr.remove();
    }
    $('#estimate_cost').val(calculateEstimateCost());
    // $('#member_' + employee_id).prop('disabled', false);
    // $('#employee_id').select2();
}

// function checkDuplicate(id, index){
//     var listCurrentEmployee = [];
//     var i = 0;
//     var arr = [];
//     var item = "";
//     $('#list_add tr').each(function() {
//         item = $(this).closest("tr").attr('id');
//         arr = item.split("_", 3);
//         listCurrentEmployee[i++] = arr[index];
//     });
//     for (var i = 0; i < listCurrentEmployee.length; i++){
//         if (id == listCurrentEmployee[i]){
//             return true;
//         }
//     }
//     return false;
// }

// function fillError() {
//     alert("Member in process can't duplicate.");
// }
// Member in process can't duplicate. Project can't has over one PO .

// function isDuplicate(id){
//     var arrayAll = new Array();
//     $('#list_add tr').each(function() {
//         item = $(this).closest("tr").attr('id');
//         arr = item.split("_", 3);
//         listCurrentEmployee[i++] = arr[index];
//     });
// }

function disableAll() {
    $("#id").attr("disabled","disabled");
    $("#name").attr("disabled","disabled");
    $("#estimate_start_date").attr("disabled","disabled");
    $("#estimate_end_date").attr("disabled","disabled");
    $("#start_date_project").attr("disabled","disabled");
    $("#end_date_project").attr("disabled","disabled");
    $("#btn_add_process").attr("disabled","disabled");
    $(".remove_employee").css("display","none");
    $("#income").attr("disabled","disabled");
    $("#real_cost").attr("disabled","disabled");
    $("#description").attr("disabled","disabled");
    $("#status").attr("disabled","disabled");
    $("#btn_reset_form_project").attr("disabled","disabled");
    $("#btn_submit_form_edit_project").attr("disabled","disabled");
    $("#employee_id").attr("disabled","disabled");
    $("#man_power").attr("disabled","disabled");
    $("#role").attr("disabled","disabled");
    $("#start_date_process").attr("disabled","disabled");
    $("#end_date_process").attr("disabled","disabled");
}

function enableAll() {
    $("#id").removeAttr("disabled");
    $("#name").removeAttr("disabled");
    $("#estimate_start_date").removeAttr("disabled");
    $("#estimate_end_date").removeAttr("disabled");
    $("#start_date_project").removeAttr("disabled");
    $("#end_date_project").removeAttr("disabled");
    $("#btn_add_process").removeAttr("disabled");
    $(".remove_employee").css("display","block");
    $("#income").removeAttr("disabled");
    $("#real_cost").removeAttr("disabled");
    $("#description").removeAttr("disabled");
    $("#status").removeAttr("disabled");
    $("#status").val('');
    $("#btn_reset_form_project").removeAttr("disabled");
    $("#btn_submit_form_edit_project").removeAttr("disabled");
    $('#warning-message').css('display','none');
    $('#btn_reopen_project').parent().closest('div').remove();
    $("#employee_id").removeAttr("disabled");
    $("#man_power").removeAttr("disabled");
    $("#role").removeAttr("disabled");
    $("#start_date_process").removeAttr("disabled");
    $("#end_date_process").removeAttr("disabled");
}
