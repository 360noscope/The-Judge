<?php 
session_start();
if (!isset($_SESSION["stu_id"])) {
    header("Location: /login.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>The Judge: Submit History</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <link rel="stylesheet" href="css/bsadmin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
            crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-success">
        <a class="sidebar-toggle text-light mr-3">
            <i class="fa fa-bars"></i>
        </a>

        <a class="navbar-brand" href="#">
            <i class="fa fa-code-branch"></i> The Judge</a>

        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profile-link" data-toggle="dropdown">
                        <i class="fa fa-address-card"></i> <?php echo $_SESSION["stu_name"]; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profile-link">
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="func/logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="d-flex">
        <nav class="sidebar bg-dark">
            <ul class="list-unstyled">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-heartbeat"></i> Dashboard</a>
                </li>
                <li>
                    <a href="lesson.php">
                        <i class="fas fa-book"></i> Judge Lesson</a>
                </li>
                <li>
                    <a href="problem.php">
                        <i class="fas fa-balance-scale"></i> Judge Exercise</a>
                </li>
                <li class="active">
                    <a href="submit_history.php">
                        <i class="fas fa-check-circle"></i> Your Submit History</a>
                </li>
            </ul>
        </nav>
        <div class="content p-4">
            <h1>Your submit</h1>
                <table id="submit_history" class="table table-striped table-bordered dt-responsive" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Problem Name</th>
                                <th>Percent Complete</th>
                                <th>Completed Time</th>
                                <th>Trying Time</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="
        anonymous "></script>
    <script src="js/bsadmin.js"></script>
    <script src="js/common.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q "
        crossorigin="anonymous "></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl "
        crossorigin="anonymous "></script>
    </body>

</html>