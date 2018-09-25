<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "Judge.php";
include_once "Fetcher.php";
include_once "Lesson.php";
include_once "Exercise.php";
include_once "Authentication.php";
include_once "User.php";

$fetch = new Fetcher();
$auth = new Authentication();
$lesson = new Lesson();

switch ($_POST["action"]) {
    case "login":
        $login_data = $_POST["data"];
        echo $auth->login($login_data["username"], $login_data["password"]);
        break;
    case "logout":
        $auth->logout();
        break;
    case "ListAdminLesson":
        echo $fetch->fetchAdminLesson($_SESSION["admin_id"]);
        break;
    case "addAdminLesson":
        $lesson_data = $_POST["data"];
        $lesson->addLesson($lesson_data["name"], $lesson_data["detail"], $_SESSION["admin_id"]);
        break;
    case "editAdminLesson":
        $lesson_data = $_POST["data"];
        $lesson->editLesson($lesson_data["id"], $lesson_data["name"], $lesson_data["detail"]);
        break;
    case "deleteAdminLesson":
        $lesson->deleteLesson($_POST["id"]);
        break;
    case "ListAdminExercise":
        break;

}
?>