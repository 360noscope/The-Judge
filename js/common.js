$(document).ready(function () {
    //start - student exercise table part
    $('#exercise').DataTable({
        "processing": true,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_exercise" }
        }
    });
    //end - student exercise table part

    //start - student submit history table part
    $('#submit_history').DataTable({
        "processing": true,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_submit_history" }
        }
    });
    //end - student submit history table part

    //start - student lesson table part
    $('#lesson').DataTable({
        "searching": false,
        "lengthChange": false,
        "processing": true,
        "paging": true,
        "info": false,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_lesson" }
        }
    });
    //end - student lesson table part

    //start - student case result table part
    $('#case_result').DataTable({
        "searching": false,
        "lengthChange": false,
        "paging": true,
        "info": false
    });
    //end - student case result table part

    //start - Admin lesson page part
    $('#admin_Lesson').DataTable({
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_admin_lesson" }
        }
    });

    $('#admin_user').DataTable({
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_admin_user_list" }
        }
    });

    //activate add new lesson form
    $("#new_lesson").click(function () {
        $('#add_lesson_modal').modal('toggle');
    });

    $("#judge-file-submit").click(function () {
        var file = $("input[name='exercise_file']");
        var parts = file.val().split('.');
        var ext = parts[parts.length - 1];
        if (ext === "py") {
            var form = $('#submit_form')[0];
            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: 'func/the_core.php',
                processData: false,
                contentType: false,
                data: formData,
                beforeSend: function () {
                    $('#loading_modal').modal('toggle');
                },
                success: function (data) {
                    $('#loading_modal').modal('hide');
                    window.location.replace("/exercise_result.php");
                },
            });
        } else {
            $("#file_type_warning").html("You can only submit .py file!");
        }
    });

    //activate edit lesson form
    $("#edit_lesson").click(function () {
        if ($('input[name="lesson_id"]:checked').val() != null) {
            var lesson_id = $('input[name="lesson_id"]:checked').val();
            var lesson_name = $('input[name="lesson_sec_name_' + lesson_id + '"]').val();
            $('#lesson_edit_name').text(lesson_name);
            $('#edit_lesson_modal').modal('toggle');
        } else {
            $('#lesson_select_warn').modal('toggle');
        }
    });

    //activate delete lesson form
    $("#delete_lesson").click(function () {
        if ($('input[name="lesson_id"]:checked').val() != null) {
            var lesson_id = $('input[name="lesson_id"]:checked').val();
            var lesson_name = $('input[name="lesson_sec_name_' + lesson_id + '"]').val();
            $('#del_lesson_name').text(lesson_name);
            $('#del_lesson_modal').modal('toggle');
        } else {
            $('#lesson_select_warn').modal('toggle');
        }
    });

    //Add new lesson
    $("#add_lesson_submit").click(function () {
        var lesson_name = $('#add_lesson_name').val();
        var lesson_detail = $("#add_lesson_detail").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "lesson_name": lesson_name,
                "lesson_detail": lesson_detail,
                "action": "add_lesson"
            },
            success: function (data) {
                $('#add_lesson_modal').modal('hide');
                $('#admin_Lesson').DataTable().ajax.reload();
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    //Edit selected lesson
    $("#edit_lesson_submit").click(function () {
        var lesson_name = $('#edit_lesson_name').val();
        var lesson_detail = $("#edit_lesson_detail").val();
        var lesson_id = $('input[name="lesson_id"]:checked').val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "lesson_id": lesson_id,
                "lesson_name": lesson_name,
                "lesson_detail": lesson_detail,
                "action": "edit_lesson"
            },
            success: function (data) {
                $('#edit_lesson_modal').modal('hide');
                $('#admin_Lesson').DataTable().ajax.reload();
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    //Delete selected lesson
    $("#del_lesson_submit").click(function () {
        var lesson_id = $('input[name="lesson_id"]:checked').val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "lesson_id": lesson_id,
                "action": "delete_lesson"
            },
            success: function (data) {
                $('#del_lesson_modal').modal('hide');
                $('#admin_Lesson').DataTable().ajax.reload();
            },
            error: function (data) {
                alert(data);
            }
        });
    });
    //end - Admin lesson page part

    //start - Admin exercise page part
    $('#admin_exercise').DataTable({
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/the_core.php",
            "type": "POST",
            "data": { "action": "get_admin_exercise" }
        }
    });

    $("#exercise_activate").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        if (exercise_id == null) {
            $('#exercise_select_warn').modal('toggle');
        } else {
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "activate_exercise",
                    "id": exercise_id
                },
                success: function (data) {
                    $('#admin_exercise').DataTable().ajax.reload();
                },
                error: function (data) {
                    alert(data);
                }
            });
        }
    });

    //start - admin add exercise part
    $('#add_exercise_modal').on('shown.bs.modal', function () {
        $("textarea[name='add_exercise_detail']").summernote();
        $("textarea[name='add_exercise_hint']").summernote();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "get_admin_lesson_list"
            },
            success: function (data) {
                $("select[name='add_exercise_lesson']").find('option')
                    .remove()
                    .end();
                var lesson_data = jQuery.parseJSON(data);
                for (index = 0; index < lesson_data.length; ++index) {
                    $("select[name='add_exercise_lesson']").
                        prepend($('<option>', {
                            value: lesson_data[index][0],
                            text: lesson_data[index][1]
                        }));
                }
            },
            error: function (data) {
                alert(data);
            }
        });
    });
    var testcase_index = 1;
    $("#add_testcase").click(function () {
        $("#add_testcase_list").prepend(
            "<tr>"
            + "<td><input name='add_input_case" + testcase_index + "' class='form-control' required /></td>"
            + "<td><input name='add_output_case" + testcase_index + "' class='form-control' required /></td>"
            + "<td><input type='number' min='1' name='add_score_case" + testcase_index + "' class='form-control' required /></td>"
            + "</tr>");
        testcase_index += 1;
    });
    $("#reset_add_testcase").click(function () {
        testcase_index = 1;
        $("#add_testcase_list").empty();
    });

    $("#add_exercise_submit").click(function () {
        if (testcase_index - 1 >= 1) {
            var case_data = [];
            for (index = 1; index < testcase_index; index++) {
                var input = $('input[name="add_input_case' + index + '"]').val();
                var output = $('input[name="add_output_case' + index + '"]').val();
                var score = $('input[name="add_score_case' + index + '"]').val();
                case_data[index - 1] = [input, output, score];
            }
            var name = $("input[name='add_exercise_name']").val();
            var detail = $("textarea[name='add_exercise_detail']").val();
            var lesson = $("select[name='add_exercise_lesson']").val();
            var exec_time = $('input[name="add_exercise_exectime"]').val();
            var exec_mem = $('input[name="add_exercise_execmem"]').val();
            var diff = $('input[name="add_exercise_diff"]').val();
            var hint = $("textarea[name='add_exercise_hint']").val();
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "add_exercise",
                    "exercise_name": name,
                    "exercise_detail": detail,
                    "exercise_lesson": lesson,
                    "exercise_exectime": exec_time,
                    "exericise_execmem": exec_mem,
                    "exercise_diff": diff,
                    "exercise_hint": hint,
                    "data": case_data
                },
                success: function (data) {
                    $('#add_exercise_modal').modal('hide');
                    $('#admin_exercise').DataTable().ajax.reload();
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert("Please add test case!");
        }
    });

    $('#add_exercise_modal').on('hidden.bs.modal', function (e) {
        $(this)
            .find("input, select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
        $("textarea[name='add_exercise_detail']")
            .summernote("reset");
        $("textarea[name='add_exercise_hint']")
            .summernote("reset");;
    })
    //end - admin add exercise part

    //start - edit exercise part
    $("#add_file_testcase_exercise").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        if (exercise_id == null) {
            $('#exercise_select_warn').modal('toggle');
        } else {
            $('#add_testcase_file').modal("toggle");
        }
    });

    $('#add_testcase_file').on('hidden.bs.modal', function (e) {
        $("textarea[name='testcase_preview']").text('');
        document.getElementById('json_testcase').value = '';
    })

    $("#preview_json").click(function () {
        var files = document.getElementById('json_testcase').files;
        var file_name = document.getElementById('json_testcase').value;
        var allowed_extensions = new Array("json");
        var file_extension = file_name.split('.').pop().toLowerCase();
        for (var i = 0; i <= allowed_extensions.length; i++) {
            if (allowed_extensions[i] == file_extension) {
                var fr = new FileReader();
                fr.onload = function (e) {
                    var result = JSON.parse(e.target.result);
                    var formatted = JSON.stringify(result, null, 2);
                    if (check_jsoncase_structure(result)) {
                        $("textarea[name='testcase_preview']").text(formatted);
                    } else {
                        $("textarea[name='testcase_preview']").text("Incorrect json structure!");
                    }
                }
                fr.readAsText(files.item(0));
            } else {
                $("textarea[name='testcase_preview']").text("It's not json file!");
            }
        }
    });

    $("#json_testcase_submit").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        var files = document.getElementById('json_testcase').files;
        var fr = new FileReader();
        fr.onload = function (e) {
            var result = JSON.parse(e.target.result);
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "update_json_testcase",
                    "case_data": result,
                    "exercise_id": exercise_id
                },
                success: function (data) {
                    $('#add_testcase_file').modal("hide");
                    $('#admin_exercise').DataTable().ajax.reload();
                },
                error: function (data) {
                    alert(data);
                }
            });
        }
        fr.readAsText(files.item(0));
    });

    $("#edit_admin_exercise").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        if (exercise_id == null) {
            $('#exercise_select_warn').modal('toggle');
        } else {
            $('#edit_exercise_modal').modal("toggle");
        }
    });

    var edit_testcase_index = 1;
    $('#edit_exercise_modal').on('shown.bs.modal', function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        $("textarea[name='edit_exercise_detail']").summernote();
        $("textarea[name='edit_exercise_hint']").summernote();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                action: "get_admin_lesson_list"
            },
            success: function (data) {
                $("select[name='edit_exercise_lesson']").find('option')
                    .remove()
                    .end();
                var lesson_data = jQuery.parseJSON(data);
                for (index = 0; index < lesson_data.length; ++index) {
                    $("select[name='edit_exercise_lesson']").
                        prepend($('<option>', {
                            value: lesson_data[index][0],
                            text: lesson_data[index][1]
                        }));
                }
            },
            error: function (data) {
                alert(data);
            }
        });
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "exercise_id": exercise_id,
                "action": "get_exercise_data"
            },
            success: function (data) {
                var exercise_data = (JSON.parse(data))["exercise_data"][0];
                var testcase_data = (JSON.parse(data))["testcase_data"];
                $("input[name='edit_exercise_name']").val(exercise_data["exer_name"]);
                $("select[name='edit_exercise_lesson']").val(exercise_data["lesson_id"]);
                $("input[name='edit_exercise_exectime']").val(exercise_data["exer_exectime"]);
                $("input[name='edit_exercise_execmem']").val(exercise_data["exer_mem"]);
                $("input[name='edit_exercise_diff']").val(exercise_data["exer_diff"]);
                $("textarea[name='edit_exercise_detail']").summernote("code", exercise_data["exer_detail"]);
                $("textarea[name='edit_exercise_hint']").summernote("code", exercise_data["exer_detail"]);
                $("#edit_testcase_list").empty();
                edit_testcase_index = 1;
                for (index in testcase_data) {
                    $("#edit_testcase_list").prepend(
                        "<tr>"
                        + "<td><input name='edit_input_case" +
                        edit_testcase_index + "' class='form-control' value='"
                        + testcase_data[index]["testcase_input"] + "' required /></td>"
                        + "<td><input name='edit_output_case" +
                        edit_testcase_index + "' class='form-control' value='"
                        + testcase_data[index]["testcase_output"] + "' required /></td>"
                        + "<td><input type='number' min='1' name='edit_score_case"
                        + edit_testcase_index + "' class='form-control' value='"
                        + testcase_data[index]["testcase_score"] + "' required /></td>"
                        + "</tr>");
                    edit_testcase_index += 1;
                }
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    $("#add_edit_testcase").click(function () {
        $("#edit_testcase_list").prepend(
            "<tr>"
            + "<td><input name='edit_input_case"
            + edit_testcase_index + "' class='form-control' required /></td>"
            + "<td><input name='edit_output_case"
            + edit_testcase_index + "' class='form-control' required /></td>"
            + "<td><input type='number' min='1' name='edit_score_case"
            + edit_testcase_index + "' class='form-control' required /></td>"
            + "</tr>");
        edit_testcase_index += 1;
    });

    $("#reset_edit_testcase").click(function () {
        $("#edit_testcase_list").empty();
        edit_testcase_index = 1;
    });

    $("#edit_exercise_submit").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        if (edit_testcase_index - 1 >= 1) {
            var edit_case_data = [];
            for (index = 1; index < edit_testcase_index; index++) {
                var input = $('input[name="edit_input_case' + index + '"]').val();
                var output = $('input[name="edit_output_case' + index + '"]').val();
                var score = $('input[name="edit_score_case' + index + '"]').val();
                edit_case_data[index - 1] = [input, output, score];
            }
            var name = $("input[name='edit_exercise_name']").val(),
                lesson = $("select[name='edit_exercise_lesson']").val(),
                exec_time = $("input[name='edit_exercise_exectime']").val(),
                exec_mem = $("input[name='edit_exercise_execmem']").val(),
                diff = $("input[name='edit_exercise_diff']").val(),
                detail = $("textarea[name='edit_exercise_detail']").val(),
                hint = $("textarea[name='edit_exercise_hint']").val();
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "edit_exercise",
                    "exercise_id": exercise_id,
                    "exercise_name": name,
                    "exercise_detail": detail,
                    "exercise_lesson": lesson,
                    "exercise_exectime": exec_time,
                    "exericise_execmem": exec_mem,
                    "exercise_diff": diff,
                    "exercise_hint": hint,
                    "data": edit_case_data
                },
                success: function (data) {
                    $('#edit_exercise_modal').modal("hide");
                    $('#admin_exercise').DataTable().ajax.reload();
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert("Please add test case!");
        }
    });

    $("#del_admin_exercise").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        if (exercise_id == null) {
            $('#exercise_select_warn').modal('toggle');
        } else {
            $('#del_exercise_modal').modal("toggle");
        }
    });

    $('#del_exercise_modal').on('shown.bs.modal', function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "get_exercise_name",
                "exercise_id": exercise_id
            },
            success: function (data) {
                $('#del_exercise_name').text(data);
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    $("#del_exercise_submit").click(function () {
        var exercise_id = $("input[name='exercise_id']:checked").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "delete_exercise",
                "exercise_id": exercise_id
            },
            success: function (data) {
                $('#del_exercise_modal').modal("hide");
                $('#admin_exercise').DataTable().ajax.reload();
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    $('#act_exercise_lesson_modal').on('shown.bs.modal', function () {
        var request_admin_lesson = $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                action: "get_admin_lesson_list"
            },
            dataType: 'html'
        });
        request_admin_lesson.done(function (msg) {
            $("select[name='act_exercise_lesson']").find('option')
                .remove()
                .end();
            var lesson_data = jQuery.parseJSON(msg);
            for (index = 0; index < lesson_data.length; ++index) {
                $("select[name='act_exercise_lesson']").
                    prepend($('<option>', {
                        value: lesson_data[index][0],
                        text: lesson_data[index][1]
                    }));
            }
        });
        request_admin_lesson.fail(function (msg) {
            console.log(msg);
        });
    });

    $("#act_exercise_lesson_submit").click(function () {
        var lesson = $("select[name='act_exercise_lesson']").val();
        var request_activate_exercise = $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "activate_exercise_by_lesson",
                "lesson_id": lesson
            },
            dataType: 'html'
        });
        request_activate_exercise.done(function (msg) {
            $('#act_exercise_lesson_modal').modal("hide");
            $('#admin_exercise').DataTable().ajax.reload();
        });
    });

    $("#add_user_submit").click(function () {
        var role = $('select[name=add_user_role]').val(),
            username = $('input[name=add_username]').val(),
            password = $('input[name=add_user_password]').val(),
            name = $('input[name=add_user_name]').val();
        if ((name == null || name == "") || (username == null || username == "") ||
            (password == null || password == "") || (name == null || name == "")) {
            alert("You left some field blank!");
        } else {
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "add_new_user",
                    "username": username,
                    "password": password,
                    "name": name,
                    "role": role
                },
                success: function (data) {
                    if (data == "false") {
                        alert("This user is existed!");
                    } else {
                        $('#add_user_modal').modal("hide");
                        $('#admin_user').DataTable().ajax.reload();
                    }
                },
                error: function (data) {
                    alert(data);
                }
            });
        }
    });

    $("#edit_user").click(function () {
        var user_id = $("input[name='user_id']:checked").val();
        if (user_id == null) {
            $('#user_select_warn').modal('toggle');
        } else {
            $('#edit_user_modal').modal("toggle");
        }
    });

    $('#edit_user_modal').on('shown.bs.modal', function () {
        var user_id = $("input[name='user_id']:checked").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "get_user_data",
                "user_id": user_id
            },
            success: function (data) {
                var user_data = JSON.parse(data);
                $('select[name=edit_user_role]').val(user_data["role"]);
                $('input[name=edit_username]').val(user_data["username"]);
                $('input[name=edit_user_name]').val(user_data["name"]);
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    $("#edit_user_submit").click(function () {
        var role = $('select[name=edit_user_role]').val(),
            username = $('input[name=edit_username]').val(),
            password = $('input[name=edit_user_password]').val(),
            name = $('input[name=edit_user_name]').val();
        var user_id = $("input[name='user_id']:checked").val();
        if ((name == null || name == "") || (username == null || username == "") ||
            (password == null || password == "") || (name == null || name == "")) {
            alert("You left some field blank!");
        } else {
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "edit_user_data",
                    "user_id": user_id,
                    "username": username,
                    "password": password,
                    "name": name,
                    "role": role
                },
                success: function (data) {
                    if (data == "false") {
                        alert("This user is existed!");
                    } else {
                        $('#edit_user_modal').modal("hide");
                        $('#admin_user').DataTable().ajax.reload();
                    }
                },
                error: function (data) {
                    alert(data);
                }
            });
        }
    });

    $("#delete_user").click(function () {
        var user_id = $("input[name='user_id']:checked").val();
        if (user_id == null) {
            $('#user_select_warn').modal('toggle');
        } else {
            $.ajax({
                url: "func/the_core.php",
                cache: false,
                type: "post",
                data: {
                    "action": "user_delete_protect",
                    "user_id": user_id
                },
                success: function (data) {
                    if (data == "yah") {
                        $('#del_user_modal').modal("toggle");
                    } else {
                        $('#same_user_warn').modal("toggle");
                    }
                },
                error: function (data) {
                    alert(data);
                }
            });
        }
    });

    $('#del_user_modal').on('shown.bs.modal', function () {
        var user_id = $("input[name='user_id']:checked").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "get_user_data",
                "user_id": user_id
            },
            success: function (data) {
                var user_data = JSON.parse(data);
                $('#del_user_name').text(user_data["name"]);
            },
            error: function (data) {
                alert(data);
            }
        });
    });

    $("#del_user_submit").click(function () {
        var user_id = $("input[name='user_id']:checked").val();
        $.ajax({
            url: "func/the_core.php",
            cache: false,
            type: "post",
            data: {
                "action": "delete_user",
                "user_id": user_id
            },
            success: function (data) {
                $('#del_user_modal').modal("hide");
                $('#admin_user').DataTable().ajax.reload();
            },
            error: function (data) {
                alert(data);
            }
        });
    });
});

function check_jsoncase_structure(json_string) {
    var result = true;
    if (!json_string.hasOwnProperty('test_case')) {
        result = false;
    } else {
        for (var i = 0; i < json_string["test_case"].length; i++) {
            if (!json_string["test_case"][i].hasOwnProperty('input')) {
                result = false;
            } else {
                if (!json_string["test_case"][i].hasOwnProperty('output')) {
                    result = false;
                } else {
                    if (!json_string["test_case"][i].hasOwnProperty('score')) {
                        result = false;
                    }
                }
            }
        }
    }
    return result;
}
function get_userstats() {
    $.ajax({
        url: "func/the_core.php",
        dataType: "json",
        cache: false,
        type: "post",
        data: {
            "action": "get_user_totaldata"
        },
        success: function (data) {
            $("#total_score").html(data["total_score"]);
            $("#rem_exercise").html(data["left_exercise"]);
            $("#ranking").html(data["ranking"] + " from " + data["total_user"]);
        },
        error: function (data) {
            alert(data);
        }
    });
}

function unenroll_lesson(id) {
    var unenroll_request = $.ajax({
        url: "func/the_core.php",
        cache: false,
        type: "post",
        data: {
            lesson_id: id,
            action: "unenroll_lesson"
        },
        dataType: 'html'
    });

    unenroll_request.done(function (msg) {
        $('#lesson').DataTable().ajax.reload();
    });

    unenroll_request.fail(function (msg) {
        console.log(msg);
    });
}

function enroll_lesson(id) {
    var enroll_request = $.ajax({
        url: "func/the_core.php",
        cache: false,
        type: "post",
        data: {
            lesson_id: id,
            action: "enroll_lesson"
        },
        dataType: 'html'
    });

    enroll_request.done(function (msg) {
        $('#lesson').DataTable().ajax.reload();
    });

    enroll_request.fail(function (msg) {
        console.log(msg);
    });
}