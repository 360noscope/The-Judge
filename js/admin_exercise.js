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
                "targets": [5],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-primary' name='edit-exercise'>Edit</button>"
            },
            {
                "targets": [6],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-danger' name='del-exercise>Delete</button>"
            },
            {
                "targets": [7],
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
    if (input_count < 1) {
        $.ajax({
            type: 'POST',
            url: 'func/directive.php',
            data: {
                "action": "addNewExercise",
                "data": exercise_data
            },
            success: function (data) {
                adminExerciseTable.ajax.reload();
            }
        });
    }
    return false;
});

