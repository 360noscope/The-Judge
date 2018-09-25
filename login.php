<?php 
session_start();
if(isset($_SESSION["stu_id"])){
    header("Location: /the_judge/dashboard.php"); 
    die();
}else if(isset($_SESSION["admin_id"])){
    header("Location: /the_judge/admin.php"); 
    die();
} ?>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to The Judge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <link rel="stylesheet" href="css/login.css" />
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
</head>

<body>
    <div class="container">
        <br />
        <div class="alert alert-danger" id="userCheckAlert" style="display:none;" role="alert" data-dismiss="alert">
            <strong>Wrong username or password!</strong>
        </div>
        <div class="row">
            <div class="col-md-5 mx-auto">
                <div class="card avoid-top">
                    <img class="card-img-top mx-auto" style="width: 60%" src="css/img/logo.png" alt="Card image cap">
                    <div class="card-body">
                        <form class="container" method="post" id="login_form">
                            <div class="form-group">
                                <label class="text-black" for="user-input">Username</label>
                                <input class="form-control" id="user-input" name="username" required />
                            </div>
                            <div class="form-group">
                                <label class="text-black" for="pass-input">Password</label>
                                <input type="password" class="form-control" id="pass-input" name="password" required />
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <button type="submit" class="btn btn-success">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>
    <script src="js/login.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>

</html>