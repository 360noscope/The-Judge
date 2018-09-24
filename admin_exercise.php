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
            <h1>Total Exercise</h1>
            <form onsubmit="event.preventDefault();">
                <div class="row">
                    <table id="admin_exercise" class="display" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Lesson</th>
                                <th>Total case</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md">
                        <button data-toggle="modal" data-target="#add_exercise_modal" class="btn btn-success btn-md">New Exercise</button>
                        <button id="edit_admin_exercise" class="btn btn-primary btn-md">Edit Exercise</button>
                        <button id="add_file_testcase_exercise" class="btn btn-primary btn-md">Add Test Case using File (JSON)</button>
                        <button id="del_admin_exercise" class="btn btn-danger btn-md">Delete Exercise</button>
                        <button id="exercise_activate" class="btn btn-primary btn-md">Activate/De-Activate</button>
                        <button data-toggle="modal" data-target="#act_exercise_lesson_modal" class="btn btn-primary btn-md">Activate/De-Activate by Lesson</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade d-example-modal-lg" id="add_exercise_modal" tabindex="-1" role="dialog" aria-labelledby="add_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_exercise_label">Add New Exercise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form onsubmit="event.preventDefault();">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Exercise Name</label>
                                    <input name="add_exercise_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Lesson</label>
                                    <select name="add_exercise_lesson" class="form-control" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Time (SEC)</label>
                                    <input name="add_exercise_exectime" min="1" type="number" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Memory (MB)</label>
                                    <input name="add_exercise_execmem" min="1" type="number" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Difficulty</label>
                                    <input name="add_exercise_diff" min="1" max="5" type="number" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Detail</label>
                                    <textarea name="add_exercise_detail" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Hint</label>
                                    <textarea name="add_exercise_hint" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>
                                    <strong>Test Case</strong>
                                </label>
                                <button class="btn btn-success" id="add_testcase">
                                    <i class="fas fa-plus fa-1x"></i>
                                </button>
                                <button class="btn btn-danger" id="reset_add_testcase">
                                    <i class="fas fa-eraser fa-1x"></i>
                                </button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-lg">
                                <table class="table">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Input</th>
                                            <th>Output</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="add_testcase_list">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="add_exercise_submit" class="btn btn-primary">Save Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade d-example-modal-lg" id="edit_exercise_modal" tabindex="-1" role="dialog" aria-labelledby="edit_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_exercise_label">Edit Selected Lesson:
                        <b id="lesson_edit_name"></b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form onsubmit="event.preventDefault();">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Exercise Name</label>
                                    <input name="edit_exercise_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Lesson</label>
                                    <select name="edit_exercise_lesson" class="form-control" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Time (SEC)</label>
                                    <input name="edit_exercise_exectime" min="1" type="number" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Execution Memory (MB)</label>
                                    <input name="edit_exercise_execmem" min="1" type="number" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Difficulty</label>
                                    <input name="edit_exercise_diff" min="1" max="5" type="number" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Detail</label>
                                    <textarea name="edit_exercise_detail" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="form-group">
                                    <label>Exercise Hint</label>
                                    <textarea name="edit_exercise_hint" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>
                                    <strong>Test Case</strong>
                                </label>
                                <button class="btn btn-success" id="add_edit_testcase">
                                    <i class="fas fa-plus fa-1x"></i>
                                </button>
                                <button class="btn btn-danger" id="reset_edit_testcase">
                                    <i class="fas fa-eraser fa-1x"></i>
                                </button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-lg">
                                <table class="table">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Input</th>
                                            <th>Output</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_testcase_list">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="edit_exercise_submit" class="btn btn-primary">Save Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade d-example-modal-lg" id="add_testcase_file" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_testcase_file">Browsing for your JSON test case file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" onsubmit="event.preventDefault();">
                        <div class="form-group">
                            <input type="file" class="form-control" id="json_testcase" accept="application/json,.json" required/>
                        </div>
                        <div class="form-group">
                            <button id="preview_json" class="btn btn-warning">Preview Test case</button>
                        </div>
                        <div class="form-group">
                            <textarea id="testcase_preview" name="testcase_preview" class="form-control" readonly></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="json_testcase_submit" class="btn btn-success" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade d-example-modal-lg" id="act_exercise_lesson_modal" tabindex="-1" role="dialog" aria-labelledby="del_exercise_lesson_label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="act_exercise_lesson_label">Select lesson that you want to activate/de-activate exercise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form onsubmit="event.preventDefault();">
                        <div class="form-group col-md-6">
                            <label>Lesson</label>
                            <select name="act_exercise_lesson" class="form-control" required>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="act_exercise_lesson_submit">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade d-example-modal-lg" id="del_exercise_modal" tabindex="-1" role="dialog" aria-labelledby="del_exercise_label"
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
                        <p id="del_exercise_name"></p>
                    </b>
                    <b class="text-danger">This will also delete exercise's exercise data on the System!</b>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Nope!</button>
                    <button type="button" class="btn btn-danger" id="del_exercise_submit">Delete Exercise Now!</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exercise_select_warn" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>You didn't select any exercise!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <script src="js/bsadmin.js"></script>
    <script src="js/common.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

</body>

</html>