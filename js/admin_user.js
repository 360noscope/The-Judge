var userTable;
$(document).ready(function () {
    userTable = $('#adminUser').DataTable({
        "dom": '<"toolbar_users"><"row" <"col-md-12">>flrtp',
        "processing": true,
        "info": false,
        "autoWidth": true,
        "ajax": {
            "url": "func/directive.php",
            "type": "POST",
            "data": {
                "action": "ListUser"
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
                "defaultContent": "<button class='btn btn-primary' name='edit-user'>Edit</button>"
            },
            {
                "targets": [4],
                "visible": true,
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "defaultContent": "<button class='btn btn-danger' name='del-user'>Delete</button>"
            }
        ]
    });

    $("div.toolbar_users").html('<button type="button" class="btn btn-success" data-toggle="modal" ' +
        'data-target="#addUserModal"><i class="fas fa-plus-circle"></i> New User</button>');
});

$("#addUserForm").on("submit", function () {
    var userData = {
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

var selectedUser;
$(document).on('click', "button[name='edit-user']", function () {
    var userData = userTable.row($(this).parents('tr')).data();
    selectedUser = userData[0];
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "detailUser",
            "data": selectedUser
        },
        success: function (data) {
            var user_return = JSON.parse(data);
            $("input[name='editUsername']").val(user_return.username);
            $("input[name='editUserName']").val(userData[1]);
            $("select[name='editUserRole'] option:selected").val(user_return.role);
        }
    });
    $("#editUserModal").modal("toggle");
    return false;
});

$("#editUserForm").on("submit", function () {
    var userData = {
        "name": $("input[name='editUserName']").val(),
        "password": $("input[name='editUserPassword']").val(),
        "username": $("input[name='editUsername']").val(),
        "role": $("select[name='editUserRole'] option:selected").val()
    }
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "updateUser",
            "data": userData
        },
        success: function (data) {
            userTable.ajax.reload();
            $("#editUserModal").modal("hide");
        }
    });
    return false;
});