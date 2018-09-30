var problemTable
$(document).ready(function () {
    problemTable = $('#exercise').DataTable({
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/directive.php",
            "type": "POST",
            "data": {
                "action": "fetchExerciseList"
            }
        }, "columnDefs": [
            {
                "targets": [4],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-danger' name='gotoExercise'>Go!</button>"
            },
            {
                "targets": [0],
                "visible": false,
                "searchable": false,
                "orderable": false
            }
        ]

    });
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

$(document).on('click', "button[name='gotoExercise']", function () {
    var exerciseData = problemTable.row($(this).parents('tr')).data();
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action":"fetchExerciseDetail",
            "data": exerciseData[0]
        },
        success: function (data) {
            $.redirect("exercise.php", {'data': data});
        }
    });
    return false;
});