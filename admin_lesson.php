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
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
            integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
            crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
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
                    <li class="active">
                        <a href="admin_lesson.php">
                            <i class="fas fa-book"></i> Lesson Management</a>
                    </li>
                    <li>
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
                <h1>The Judge: Lesson</h1>
                <div class="row">
                    <table id="adminLesson" class="table table-striped table-bordered dt-responsive" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Detail</th>
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

        <div class="modal fade" id="addLessonModal" tabindex="-1" role="dialog" aria-labelledby="add_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Lesson</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="newLessonForm" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Lesson Name</label>
                                        <input name="addLessonName" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Lesson Detail</label>
                                        <textarea name="addLessonDetail" class="form-control"></textarea>
                                    </div>
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
        <div class="modal fade" id="editLessonModal" tabindex="-1" role="dialog" aria-labelledby="edit_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_lesson_label">Edit Selected Lesson:
                            <b id="editLessonName"></b>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editLessonForm" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Lesson Name</label>
                                        <input name="editLessonName" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Lesson Detail</label>
                                        <textarea name="editLessonDetail" class="form-control"></textarea>
                                    </div>
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
        <div class="modal fade" id="deleteLessonModal" tabindex="-1" role="dialog" aria-labelledby="del_lesson_label"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deleted Selected Lesson</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Do you want to delete below lesson ?
                        <b>
                            <p id="deleteLessonName"></p>
                        </b>
                        <b class="text-danger">This will also delete exercise's lesson data on the System!</b>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Nope!</button>
                        <button type="button" id="deleteLessonBtn" class="btn btn-danger">Confirm!</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="js/bsadmin.js"></script>
        <script src="js/admin_lesson.js"></script>
    </body>

    </html>