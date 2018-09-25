$("#login_form").on("submit", function () {
    var username = $("input[name='username']").val(),
        password = $("input[name='password']").val();
    var data = {
        "username": username,
        "password": password
    }
    $.ajax({
        type: 'POST',
        url: 'func/directive.php',
        data: {
            "action": "login",
            "data": data
        },
        success: function (data) {
            var return_data = JSON.parse(data)[0]
            if (return_data.flag == "ok") {
                if (return_data.type == "admin") {
                    window.location.replace("/the_judge/admin.php");
                } else {
                    window.location.replace("/the_judge/dashboard.php");
                }
            } else {
                $('#userCheckAlert').show();
            }
        }
    });
    return false;
});

