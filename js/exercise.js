$(document).ready(function () {

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

$("#pySubmit").on("submit", function (e) {
    /*var userData = {
        "name": $("input[name='addUserName']").val(),
        "password": $("input[name='addUserPassword']").val(),
        "username": $("input[name='addUsername']").val(),
        "role": $("select[name='addUserRole'] option:selected").val()
    }
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "addUser",
            "data": userData
        },
        success: function (data) {
            if (data == "false") {
                alert("มี Username อยู่ในระบบแล้ว");
            }
            else {
                userTable.ajax.reload();
                $(":input, #addUserForm").not(':button, :submit, :reset, :hidden')
                    .val('');
                $("#addUserModal").modal("hide");
            }
        }
    });*/

    var filename = $("input[name='exerciseFile']").val();
    var extension = filename.replace(/^.*\./, '');
    if (extension == filename) {
        extension = '';
    } else {
        extension = extension.toLowerCase();
    }
    switch (extension) {
        case 'py':
            var form = $('#pySubmit')[0];
            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: 'func/directive.php',
                processData: false,
                contentType: false,
                data: formData,
                beforeSend: function () {
                    $('#loadingModal').modal('toggle');
                },
                success: function (data) {
                    $('#loadingModal').modal('hide');
                    console.log(data);
                },
            });
            break;
        default:
            $("#fileTypeWarning").html("It's not a .PY file!");
            e.preventDefault();
            break;
    }
    return false;
});