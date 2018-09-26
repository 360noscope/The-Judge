<?php 
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: /login.php");
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>The Judge Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
        crossorigin="anonymous">
    <link rel="stylesheet" href="css/bsadmin.css">
    <link rel="stylesheet" href="css/common.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand navbar-dark bg-success">
        <a class="sidebar-toggle text-light mr-3">
            <i class="fa fa-bars"></i>
        </a>

        <a class="navbar-brand" href="#">
            <i class="fa fa-code-branch"></i> The Judge Admin</a>

        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profile-link" data-toggle="dropdown">
                        <i class="fa fa-address-card"></i>
                        <?php echo $_SESSION["admin_name"]; ?>
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
                    <a href="admin.php">
                        <i class="fas fa-heartbeat"></i> Dashboard</a>
                </li>
                <li>
                    <a href="admin_lesson.php">
                        <i class="fas fa-book"></i> Lesson Management</a>
                </li>
                <li class="active">
                    <a href="admin_exercise.php">
                        <i class="fas fa-code"></i> Exercise Management</a>
                </li>
                <li>
                    <a href="admin_user.php">
                        <i class="fas fa-graduation-cap"></i> User Management</a>
                </li>
            </ul>
        </nav>
        <div class="content p-4">
            <h1>The Judge: Exercise</h1>
            <div class="row">
                <table id="adminExercise" class="table table-striped table-bordered dt-responsive" width="100%"
                    cellspacing="0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Lesson</th>
                            <th>Total case</th>
                            <th class="text-center">Status</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addExerciseModal" tabindex="-1" role="dialog" aria-labelledby="add_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Exercise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addExerciseForm" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Exercise Name</label>
                                    <input name="addExerciseName" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Lesson</label>
                                    <select name="addExerciseLesson" class="form-control" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Time (SEC)</label>
                                    <input name="addExerciseExecTime" min="1" type="number" class="form-control"
                                        required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Memory (MB)</label>
                                    <input name="addExerciseExecMem" min="1" type="number" class="form-control"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Difficulty</label>
                                    <input name="addExerciseDiff" min="1" max="5" type="number" class="form-control"
                                        required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Detail</label>
                                    <textarea name="addExerciseDetail" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Hint</label>
                                    <textarea name="addExerciseHint" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>
                                    <strong>Test Case</strong>
                                </label>
                                <button class="btn btn-success" id="addTestcase">
                                    <i class="fas fa-plus fa-1x"></i>
                                </button>
                                <button class="btn btn-danger" id="resetAddTestcase">
                                    <i class="fas fa-eraser fa-1x"></i>
                                </button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-lg">
                                <table class="table" id="addTestCaseList">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Input</th>
                                            <th>Output</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editExerciseModal" tabindex="-1" role="dialog" aria-labelledby="edit_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Selected Lesson:
                        <b id="lessonEditName"></b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editExerciseForm" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Exercise Name</label>
                                    <input name="editExerciseName" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Lesson</label>
                                    <select name="editExerciseLesson" class="form-control" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Time (SEC)</label>
                                    <input name="editExerciseExecTime" min="1" type="number" class="form-control"
                                        required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Memory (MB)</label>
                                    <input name="editExerciseExecMem" min="1" type="number" class="form-control"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Difficulty</label>
                                    <input name="editExerciseDiff" min="1" max="5" type="number" class="form-control"
                                        required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Detail</label>
                                    <textarea name="editExerciseDetail" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Hint</label>
                                    <textarea name="editExerciseHint" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>
                                    <strong>Test Case</strong>
                                </label>
                                <button class="btn btn-success" id="addEditTestcase">
                                    <i class="fas fa-plus fa-1x"></i>
                                </button>
                                <button class="btn btn-danger" id="resetEditTestcase">
                                    <i class="fas fa-eraser fa-1x"></i>
                                </button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-lg">
                                <table class="table" id="editTestCaseList">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Input</th>
                                            <th>Output</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="actExerciseModal" tabindex="-1" role="dialog" aria-labelledby="del_exercise_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activate/Deactivate Exercise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="activateExerciseForm" method="POST">
                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lessonCheck" id="lessonCheck1"/>
                            <label class="form-check-label" for="lessonCheck1">
                                Want to activate/deactivate exercise whole lesson
                            </label>
                        </div>
                        <br />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delExerciseModal" tabindex="-1" role="dialog" aria-labelledby="del_exercise_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="del_exercise_label">Deleted Selected Exercise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Do you want to delete below Exercise ?
                    <b>
                        <p id="delExerciseName"></p>
                    </b>
                    <b class="text-danger">This will also delete exercise's exercise data on the System!</b>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Nope!</button>
                    <button type="button" class="btn btn-danger" id="delExerciseConfirm">Confirm!</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script src="js/bsadmin.js"></script>
    <script src="js/admin_exercise.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

</body>

</html>