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
$login_check = isset($_SESSION["admin_id"]) || isset($_SESSION["stu_id"]);
$request_check = isset($_POST["action"]) || isset($_GET["action"]);
if ($login_check && $request_check) {
    $action = "";
    $fetcher = new Fetcher();
    $lessoner = new Lesson();
    $exerciser = new Exercise();
    $userer = new User();
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
    } else {
        $action = $_GET["action"];
    }
    switch ($action) {
        case "judging":
            $judger = new Judge();
            $exercise_result = $judger->judging($_FILES['exercise_file']);
            $_SESSION["exercise_result"] = $exercise_result;
            header("Location: /exercise_result.php");
            die();
            break;
        case "get_user_totaldata":
            echo $fetcher->fetch_total_userstats($_SESSION["stu_id"]);
            break;
        case "get_lesson":
            echo $fetcher->fetch_lesson();
            break;
        case "get_exercise":
            echo $fetcher->fetch_exercise();
            break;
        case "get_exercise_detail":
            $_SESSION["exercise_data"] = $fetcher->fetch_exercise_detail($_POST["exercise_id"])[0];
            header("Location: /exercise.php");
            die();
            break;
        case "get_submit_history":
            echo $fetcher->fetch_submit_history();
            break;
        case "unenroll_lesson":
            echo $lessoner->unenroll($_POST["lesson_id"]);;
            break;
        case "enroll_lesson":
            echo $lessoner->enroll($_POST["lesson_id"]);
            break;
        case "get_admin_lesson":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->fetch_admin_lesson($_SESSION["admin_id"]);
            }
            break;
        case "get_admin_lesson_list":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->get_admin_lesson_list($_SESSION["admin_id"]);
            }
            break;
        case "add_lesson":
            if ($_SESSION["admin_id"] != null) {
                echo $lessoner->add_lesson($_POST["lesson_name"], $_POST["lesson_detail"], $_SESSION["admin_id"]);
            }
            break;
        case "edit_lesson":
            if ($_SESSION["admin_id"] != null) {
                echo $lessoner->edit_lesson($_POST["lesson_id"], $_POST["lesson_name"], $_POST["lesson_detail"]);
            }
            break;
        case "delete_lesson":
            if ($_SESSION["admin_id"] != null) {
                echo $lessoner->delete_lesson($_POST["lesson_id"]);
            }
            break;
        case "get_admin_exercise":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->fetch_admin_exercise();
            }
            break;
        case "activate_exercise":
            if ($_SESSION["admin_id"] != null) {
                echo $exerciser->activate_exercise($_POST["id"]);
            }
            break;
        case "activate_exercise_by_lesson":
            if ($_SESSION["admin_id"] != null) {
                echo $exerciser->activate_exercise_by_lesson($_POST["lesson_id"]);
            }
            break;
        case "add_exercise":
            if ($_SESSION["admin_id"] != null) {
                echo $exerciser->add_exercise(
                    $_POST["exercise_name"],
                    $_POST["exercise_detail"],
                    $_POST["exercise_lesson"],
                    $_POST["exercise_exectime"],
                    $_POST["exericise_execmem"],
                    $_POST["exercise_diff"],
                    $_POST["exercise_hint"],
                    $_POST["data"]
                );
            }
            break;
        case "get_exercise_data":
            if ($_SESSION["admin_id"] != null) {
                $data = $fetcher->fetch_admin_exercise_data($_POST["exercise_id"]);
                echo $data;
            }
            break;
        case "edit_exercise":
            if ($_SESSION["admin_id"] != null) {
                $exercise_data = array(
                    "exercise_id" => $_POST["exercise_id"],
                    "exercise_name" => $_POST["exercise_name"],
                    "exercise_detail" => $_POST["exercise_detail"],
                    "exercise_lesson" => $_POST["exercise_lesson"],
                    "exercise_exectime" => $_POST["exercise_exectime"],
                    "exericise_execmem" => $_POST["exericise_execmem"],
                    "exercise_diff" => $_POST["exercise_diff"],
                    "exercise_hint" => $_POST["exercise_hint"],
                    "case_data" => $_POST["data"]
                );
                $exerciser->update_exercise($exercise_data);
            }
            break;
        case "update_json_testcase":
            if ($_SESSION["admin_id"] != null) {
                $case_data = $_POST["case_data"];
                echo $exerciser->update_testcase_json($case_data, $_POST["exercise_id"]);
            }
            break;
        case "get_exercise_name":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->fetch_exercise_name($_POST["exercise_id"]);
            }
            break;
        case "delete_exercise":
            if ($_SESSION["admin_id"] != null) {
                $exerciser->delete_exercise($_POST["exercise_id"]);
                echo "done delete exercise!";
            }
            break;
        case "get_admin_user_list":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->fetch_admin_user_list();
            }
            break;
        case "add_new_user":
            if ($_SESSION["admin_id"] != null) {
                $user_data = array(
                    "username" => $_POST["username"],
                    "name" => $_POST["name"],
                    "password" => $_POST["password"],
                    "role" => $_POST["role"]
                );
                echo $userer->add_user($user_data);
            }
            break;
        case "get_user_data":
            if ($_SESSION["admin_id"] != null) {
                echo $fetcher->fetch_user_data($_POST["user_id"]);
            }
            break;
        case "edit_user_data":
            if ($_SESSION["admin_id"] != null) {
                $user_data = array(
                    "username" => $_POST["username"],
                    "name" => $_POST["name"],
                    "password" => $_POST["password"],
                    "role" => $_POST["role"],
                    "user_id" => $_POST["user_id"]
                );
                echo $userer->edit_user($user_data);
            }
            break;
        case "user_delete_protect":
            if ($_SESSION["admin_id"] == $_POST["user_id"]) {
                echo "nope";
            } else {
                echo "yah";
            }
            break;
        case "delete_user":
            if ($_SESSION["admin_id"] != null) {
                $userer->delete_user($_POST["user_id"]);
            }
            break;
    }
} else if (isset($_POST["username"]) && isset($_POST["password"])) {
    $authener = new Authentication();
    $result = $authener->login($_POST["username"], $_POST["password"]);
    if ($result[0]["flag"] == "ok") {
        if ($result[0]["type"] == "ADMIN") {
            header("Location: /admin.php");
            die();
        } else {
            header("Location: /dashboard.php");
            die();
        }
    } else {
        header("Location: /login.php");
        die();
    }
} else {
    echo '<script language="javascript">';
    echo 'alert("You are lil monkey eh?");';
    echo 'window.location = "/login.php";';
    echo '</script>';
}
