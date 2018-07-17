<?php 
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: /login.php");
    die();
}
?>
<DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>The Judge Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
            crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp"
            crossorigin="anonymous">
        <link rel="stylesheet" href="css/bsadmin.css">
        <link rel="stylesheet" href="css/common.css">
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
                            <!--<a class="dropdown-item" href="#">My Profile</a>-->
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
                    <li class="active">
                        <a href="admin_lesson.php">
                            <i class="fas fa-book"></i> Lesson Management</a>
                    </li>
                    <li>
                        <a href="admin_exercise.php">
                            <i class="fas fa-code"></i> Exercise Management</a>
                    </li>
                    <!--<li>
                        <a href="admin_pair.php">
                            <i class="far fa-handshake"></i> Pair Programming</a>
                    </li>
                    <li>
                        <a href="admin_exam.php">
                            <i class="fab fa-accessible-icon"></i> Examination Mode</a>
                    </li>-->
                    <li>
                        <a href="admin_user.php">
                            <i class="fas fa-graduation-cap"></i> User Management</a>
                    </li>
                </ul>
            </nav>
            <div class="content p-4">
                <h1>Total Lesson</h1>
                <form onsubmit="event.preventDefault();">
                    <div class="row">
                        <table id="admin-lesson" class="table table-striped table-borderless dt-responsive" style="width:100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Detail</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <button id="new-lesson" class="btn btn-success btn-md">New Lesson</button>
                            <button id="edit-lesson" class="btn btn-primary btn-md">Edit Lesson</button>
                            <button id="delete-lesson" class="btn btn-danger btn-md">Delete Lesson</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade d-example-modal-lg" id="add-lesson-modal" tabindex="-1" role="dialog" aria-labelledby="add_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add_lesson_label">Add New Lesson</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Lesson Name</label>
                                        <input id="add-lesson-name" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Lesson Detail</label>
                                        <textarea id="add-lesson-detail" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="add-lesson-submit" class="btn btn-primary">Save Lesson</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade d-example-modal-lg" id="edit-lesson-modal" tabindex="-1" role="dialog" aria-labelledby="edit_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_lesson_label">Edit Selected Lesson:
                            <b id="lesson-edit-name"></b>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Lesson Name</label>
                                        <input id="edit-lesson-name" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Lesson Detail</label>
                                        <textarea id="edit-lesson-detail" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="edit-lesson-submit" class="btn btn-primary">Save Lesson</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade d-example-modal-lg" id="del-lesson-modal" tabindex="-1" role="dialog" aria-labelledby="del_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="del_lesson_label">Deleted Selected Lesson</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Do you want to delete below lesson ?
                        <b>
                            <p id="del-lesson-name"></p>
                        </b>
                        <b class="text-danger">This will also delete exercise's lesson data on the System!</b>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Nope!</button>
                        <button type="button" class="btn btn-danger" id="del_lesson_submit">Delete Lesson Now!</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="lesson-select-warn" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p>You didn't select any lesson!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
        <script src="js/bsadmin.js"></script>
        <script src="js/common.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    </body>

    </html>