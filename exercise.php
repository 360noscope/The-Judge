<?php 
session_start();
$exercise;
if (!isset($_SESSION["stu_id"])) {
    header("Location: /the_judge/login.php");
    die();
} else {
    $exercise = json_decode($_POST["data"], true)[0];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>The Judge:
        <?php echo $exercise["exerciseName"]; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
        crossorigin="anonymous">
    <link rel="stylesheet" href="css/bsadmin.css">
    <link rel="stylesheet" href="css/common.css">
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
                        <form id="logout_form" method="POST">
                            <button type="submit" class="dropdown-item">Logout</a>
                        </form>
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
            <h1>Exercise:
                <?php echo $exercise["exerciseName"]; ?>
            </h1>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <h4 class="card-title">Problem Detail</h4>
                                <p class="card-text">
                                    <?php echo $exercise["exerciseDetail"]; ?>
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
                                <?php echo $exercise["exerciseHint"]; ?>
                                <p class="card-text">
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
                                    <span class="badge badge-danger">Execution Time
                                    </span>
                                    <?php echo $exercise["timeLimit"]; ?> Sec
                                </p>
                                <p class="card-text">
                                    <span class="badge badge-success">Memory
                                    </span>
                                    <?php echo $exercise["memoryLimit"]; ?> MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-danger" id="fileTypeWarning"></h5>
                    <form id="pySubmit" enctype="multipart/form-data" method="post">
                        <label class="text-black" for="user-input">Submit your work here :3</label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                        <input type="file" name="exerciseFile" accept=".py" required />
                        <input type="hidden" name="action" value="submitForJudge" />
                        <button type="submit" class="btn btn-success">I regret nothing!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="lds-css ng-scope">
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
                            <img src="/the_judge/img/loading.gif" alt="Loading" title="Loading" />
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
                crossorigin="
        anonymous "></script>
            <script src="js/bsadmin.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
                crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
                crossorigin="anonymous"></script>
            <script src="js/exercise.js"></script>
</body>

</html>