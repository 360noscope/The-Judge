var lessonTable
$(document).ready(function () {
    lessonTable = $('#lesson').DataTable({
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/directive.php",
            "type": "POST",
            "data": {
                "action": "ListUserLesson"
            }
        }, "columnDefs": [
            {
                "targets": [2],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center"
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

function enroll_lesson(id) {
    var enroll_request = $.ajax({
        url: "func/directive.php",
        cache: false,
        type: "post",
        data: {
            lesson_id: id,
            action: "enrollLesson"
        }
    });

    enroll_request.done(function (msg) {
        lessonTable.ajax.reload();
    });

    enroll_request.fail(function (msg) {
        console.log(msg);
    });
}

function unenroll_lesson(id) {
    var unenroll_request = $.ajax({
        url: "func/directive.php",
        cache: false,
        type: "post",
        data: {
            lesson_id: id,
            action: "unenrollLesson"
        }
    });

    unenroll_request.done(function (msg) {
        lessonTable.ajax.reload();
    });

    unenroll_request.fail(function (msg) {
        console.log(msg);
    });
}