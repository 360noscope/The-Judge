var lessonTable;
$(document).ready(function () {
    lessonTable = $('#adminLesson').DataTable({
        "dom": '<"toolbar_lesson"><"row" <"col-md-12">>flrtp',
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/directive.php",
            "type": "POST",
            "data": {
                "action": "ListAdminLesson"
            }
        }, "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false,
                "orderable": false
            },
            {
                "targets": [3],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-primary' name='edit-lesson'>Edit</button>"
            },
            {
                "targets": [4],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-danger' name='del-lesson'>Delete</button>"
            }
        ]
    });

    $("div.toolbar_lesson").html('<button type="button" class="btn btn-success" data-toggle="modal" ' +
        'data-target="#addLessonModal"><i class="fas fa-plus-circle"></i> New Lesson</button>');
});

var selectedLesson;

$(document).on('click', "button[name='edit-lesson']", function () {
    var lessonData = lessonTable.row($(this).parents('tr')).data();
    selectedLesson = lessonData[0];
    $("#editLessonName").text(lessonData[1]);
    $("input[name='editLessonName']").val(lessonData[1]);
    $("textarea[name='editLessonDetail']").val(lessonData[2]);
    $("#editLessonModal").modal("toggle");
    return false;
});

$(document).on('click', "button[name='del-lesson']", function () {
    var lessonData = lessonTable.row($(this).parents('tr')).data();
    selectedLesson = lessonData[0];
    $("#deleteLessonName").text(lessonData[1]);
    $("#deleteLessonModal").modal("toggle");
    return false;
});

$(document).on('click', "#deleteLessonBtn", function () {
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "deleteAdminLesson",
            "id": selectedLesson
        },
        success: function (data) {
            lessonTable.ajax.reload();
            $("#deleteLessonModal").modal("hide");
        }
    });
    return false;
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

$("#newLessonForm").on("submit", function () {
    var lessonData = {
        "name": $("input[name='addLessonName']").val(),
        "detail": $("textarea[name='addLessonDetail']").val()
    }
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "addAdminLesson",
            "data": lessonData
        },
        success: function (data) {
            $("input[name='addLessonName']").val("");
            $("textarea[name='addLessonDetail']").val("");
            lessonTable.ajax.reload();
            $("#addLessonModal").modal("hide");
        }
    });
    return false;
});

$("#editLessonForm").on("submit", function () {
    var lessonData = {
        "id": selectedLesson,
        "name": $("input[name='editLessonName']").val(),
        "detail": $("textarea[name='editLessonDetail']").val()
    }
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "editAdminLesson",
            "data": lessonData
        },
        success: function (data) {
            lessonTable.ajax.reload();
            $("#editLessonModal").modal("hide");
        }
    });
    return false;
});