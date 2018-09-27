var adminExerciseTable;
$(document).ready(function () {
    adminExerciseTable = $('#adminExercise').DataTable({
        "dom": '<"toolbar_exercise"><"row" <"col-md-12">>flrtp',
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/directive.php",
            "type": "POST",
            "data": { "action": "ListAdminExercise" }
        }, "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false,
                "orderable": false
            },
            {
                "targets": [1],
                "visible": false,
                "searchable": false,
                "orderable": false
            },
            {
                "targets": [6],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-warning' name='edit-exercise'>Edit</button>"
            },
            {
                "targets": [7],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-danger' name='del-exercise'>Delete</button>"
            },
            {
                "targets": [8],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-primary' name='act-exercise'>Activate/Deactivate Exercise</button>"
            }
        ]
    });

    $("div.toolbar_exercise").html('<button type="button" class="btn btn-success" data-toggle="modal" ' +
        'data-target="#addExerciseModal"><i class="fas fa-plus-circle"></i> New Exercise</button>');
});

$("#logout_form").on("submit", function () {
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "logout"
        },
        success: function (data) {
            window.location.replace("/the_judge/login.php");
        }
    });
    return false;
});

$('#addExerciseModal').on('shown.bs.modal', function () {
    $("textarea[name='addExerciseDetail']").summernote();
    $("textarea[name='addExerciseHint']").summernote();
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "ListAdminLesson"
        },
        success: function (data) {
            $('select[name="addExerciseLesson"]').empty();
            JSON.parse(data).data.forEach(function (ex) {
                $('select[name="addExerciseLesson"]').append($('<option>', {
                    text: ex[1],
                    value: ex[0]
                }));
            });
        }
    });
    return false;
});

var input_count = 1;
$('#addTestcase').on('click', function () {
    $('#addTestCaseList > tbody:last-child').append("<tr><td><input name='input" + input_count + "' class='form-control' required /></td>" +
        "<td><input name='output" + input_count + "' class='form-control' required /></td>" +
        "<td><input name='score" + input_count + "' class='form-control' required /></td></tr>");
    input_count++;
    return false;
});

$('#resetAddTestcase').on('click', function () {
    $("#addTestCaseList > tbody").empty();
    input_count = 1;
    return false;
});

$("#addExerciseForm").on("submit", function () {
    var input_data = [];
    for (var i = 1; i < input_count; i++) {
        input_data.push({
            "input": $("input[name='input" + i + "']").val(),
            "output": $("input[name='output" + i + "']").val(),
            "score": $("input[name='score" + i + "']").val()
        });
    }
    var exercise_data = {
        "name": $("input[name='addExerciseName']").val(),
        "lesson": $("select[name='addExerciseLesson'] option:selected").val(),
        "exec_time": $("input[name='addExerciseExecTime']").val(),
        "exec_mem": $("input[name='addExerciseExecMem']").val(),
        "diff": $("input[name='addExerciseDiff']").val(),
        "detail": $("textarea[name='addExerciseDetail']").val(),
        "hint": $("textarea[name='addExerciseHint']").val(),
        "input_data": input_data
    }
    if (input_count > 1) {
        $.ajax({
            type: 'POST',
            url: 'func/directive.php',
            data: {
                "action": "addNewExercise",
                "data": exercise_data
            },
            success: function (data) {
                $(":input, #addExerciseForm").not(':button, :submit, :reset, :hidden')
                    .val('');
                $('#addTestCaseList > tbody').empty();
                input_count = 1;
                $("#addExerciseModal").modal("hide");
                adminExerciseTable.ajax.reload();
            }
        });
    }
    return false;
});

var editInputCount = 1, selectedExercise, selectedExerciseLesson;
$(document).on('click', "button[name='edit-exercise']", function () {
    var exerciseData = adminExerciseTable.row($(this).parents('tr')).data();
    selectedExercise = exerciseData[0];
    $("#lessonEditName").html(exerciseData[2]);
    $("textarea[name='editExerciseDetail']").summernote();
    $("textarea[name='editExerciseHint']").summernote();
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        async: false,
        data: {
            "action": "ListAdminLesson"
        },
        success: function (data) {
            $('select[name="editExerciseLesson"]').empty();
            JSON.parse(data).data.forEach(function (ex) {
                $('select[name="editExerciseLesson"]').append($('<option>', {
                    text: ex[1],
                    value: ex[0]
                }));
            });
        }
    });
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        async: false,
        data: {
            "action": "getAdminExerciseDetail",
            "data": exerciseData[0]
        },
        success: function (data) {
            var exercise_return = JSON.parse(data);
            var exercise_data = exercise_return.exercise_data[0],
                testcase = exercise_return.testcase_data;
            $("input[name='editExerciseName']").val(exerciseData[2]);
            $("select[name='editExerciseLesson'] option:selected").val(exercise_data.lesson_id);
            $("input[name='editExerciseExectime']").val(exercise_data.exer_exectime);
            $("input[name='editExerciseExecMem']").val(exercise_data.exer_mem);
            $("input[name='editExerciseDiff']").val(exercise_data.exer_diff);
            $("textarea[name='editExerciseDetail']").summernote("code", exercise_data.exer_detail);
            $("textarea[name='editExerciseHint']").summernote("code", exercise_data.exer_hint);
            $('#editTestCaseList > tbody:last-child').empty();
            testcase.forEach(function (ex) {
                $('#editTestCaseList > tbody:last-child').append("<tr><td><input name='editInput" + editInputCount + "' class='form-control' value='" + ex.testcase_input + "' required /></td>" +
                    "<td><input name='editOutput" + editInputCount + "' class='form-control' value='" + ex.testcase_output + "' required /></td>" +
                    "<td><input name='editScore" + editInputCount + "' class='form-control' value='" + ex.testcase_score + "' required /></td></tr>");
                editInputCount++;
            });
        }
    });
    $("#editExerciseModal").modal("toggle");
    return false;
});

$('#addEditTestcase').on('click', function () {
    $('#editTestCaseList > tbody:last-child').append("<tr><td><input name='editInput" + editInputCount + "' class='form-control' required /></td>" +
        "<td><input name='editOutput" + editInputCount + "' class='form-control' required /></td>" +
        "<td><input name='editScore" + editInputCount + "' class='form-control' required /></td></tr>");
    editInputCount++;
    return false;
});

$('#resetEditTestcase').on('click', function () {
    $("#editTestCaseList > tbody").empty();
    editInputCount = 1;
    return false;
});

$("#editExerciseForm").on("submit", function () {
    var input_data = [];
    for (var i = 1; i < editInputCount; i++) {
        input_data.push({
            "input": $("input[name='editInput" + i + "']").val(),
            "output": $("input[name='editOutput" + i + "']").val(),
            "score": $("input[name='editScore" + i + "']").val()
        });
    }
    var exercise_data = {
        "id": selectedExercise,
        "name": $("input[name='editExerciseName']").val(),
        "lesson": $("select[name='editExerciseLesson'] option:selected").val(),
        "exec_time": $("input[name='editExerciseExecTime']").val(),
        "exec_mem": $("input[name='editExerciseExecMem']").val(),
        "diff": $("input[name='editExerciseDiff']").val(),
        "detail": $("textarea[name='editExerciseDetail']").val(),
        "hint": $("textarea[name='editExerciseHint']").val(),
        "input_data": input_data
    }
    if (editInputCount > 1) {
        $.ajax({
            type: 'POST',
            url: 'func/directive.php',
            data: {
                "action": "updateAdminExercise",
                "data": exercise_data
            },
            success: function (data) {
                $("#editExerciseModal").modal("hide");
                adminExerciseTable.ajax.reload();
            }
        });
    }
    return false;
});

$(document).on('click', "button[name='del-exercise']", function () {
    var exerciseData = adminExerciseTable.row($(this).parents('tr')).data();
    selectedExercise = exerciseData[0];
    $("#delExerciseName").html(exerciseData[2]);
    $("#delExerciseModal").modal("toggle");
    return false;
});

$('#delExerciseConfirm').on('click', function () {
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "deleteAdminExercise",
            "data": selectedExercise
        },
        success: function (data) {
            $("#delExerciseModal").modal("hide");
            adminExerciseTable.ajax.reload();
        }
    });
    return false;
});

$(document).on('click', "button[name='act-exercise']", function () {
    var exerciseData = adminExerciseTable.row($(this).parents('tr')).data();
    selectedExercise = exerciseData[0];
    selectedExerciseLesson = exerciseData[1];
    $("#actExerciseModal").modal("toggle");
    return false;
});

$("#activateExerciseForm").on("submit", function () {
    var isLesson = "NO";
    if ($("input[name='lessonCheck']").is(':checked') == true) {
        isLesson = "YES";
    }
    var activate_data = {
        "id": selectedExercise,
        "lesson": selectedExerciseLesson,
        "isLesson": isLesson
    }
    console.log(isLesson);
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "activateExercise",
            "data": activate_data
        },
        success: function (data) {
            $("#actExerciseModal").modal("hide");
            adminExerciseTable.ajax.reload();
        }
    });
    return false;
});

