<?php 
session_start();
if (!isset($_SESSION["stu_id"])) {
    header("Location: /login.php");
    die();
} else {
    $exercise_result = $_SESSION["exercise_data"];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>The Judge:
        <?php echo $exercise_result["exercise_name"]; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <link rel="stylesheet" href="css/bsadmin.css">
    <link rel="stylesheet" href="css/common.css">
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
                        <i class="fa fa-address-card"></i>
                        <?php echo $_SESSION["stu_name"]; ?>
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
                    <a href="/the_judge/dashboard.php">
                        <i class="fas fa-heartbeat"></i> Dashboard</a>
                </li>
                <li>
                    <a href="/the_judge/lesson.php">
                        <i class="fas fa-book"></i> Judge Lesson</a>
                </li>
                <li>
                    <a href="/the_judge/problem.php">
                        <i class="fas fa-balance-scale"></i> Judge Exercise</a>
                </li>
                <li>
                    <a href="/the_judge/submit_history.php">
                        <i class="fas fa-check-circle"></i> Your Submit History</a>
                </li>
            </ul>
        </nav>
        <div class="content p-4">
            <?php echo $exercise_result["done_msg"]; ?>
            <h1>Exercise:
                <?php echo $exercise_result["exercise_name"] ?>
            </h1>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <h4 class="card-title">Problem Detail</h4>
                                <p class="card-text">
                                    <?php echo $exercise_result["exercise_detail"]; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <h4 class="card-title">Hint</h4>
                                <p class="card-text">
                                    <?php echo $exercise_result["exercise_hint"]; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <h4 class="card-title">Rule</h4>
                                <p class="card-text">
                                    <span class="badge badge-danger">Execution Time</span>
                                    <?php echo $exercise_result["time_limit"]; ?> Sec</p>
                                <p class="card-text">
                                    <span class="badge badge-success">Memory</span>
                                    <?php echo $exercise_result["memory_limit"]; ?> MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-danger" id="file_type_warning"></h5>
                    <form id="submit_form" enctype="multipart/form-data" onsubmit="event.preventDefault();" method="post" class="form-control">
                        <label class="text-black" for="user-input">Submit your work here :3</label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                        <input type="hidden" name="action" value="judging" />
                        <input type="file" name="exercise_file" accept=".py" required />
                        <button type="submit" id="judge-file-submit" class="btn btn-success">I regret nothing!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="loading_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="lds-css ng-scope" >
                                <div class="lds-pacman">
                                    <div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                    <div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <img src="/img/loading.gif" alt="Loading" title="Loading" />
                        </div>
                    </div>
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