<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Lesson
{
    public function enroll($lesson_id)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("INSERT INTO user_enrollment (user_id, lesson_id) VALUES(?, ?)");
        $stmt->bind_param("ss", $_SESSION["stu_id"], $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done enroll you!";
    }

    public function unenroll($lesson_id)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("DELETE FROM user_enrollment WHERE user_id = ? AND lesson_id = ?");
        $stmt->bind_param("ss", $_SESSION["stu_id"], $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done unenroll you!";
    }

    public function add_lesson($lesson_name, $lesson_detail, $admin_id)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("INSERT INTO lesson (lesson_name, lesson_detail, owner_id) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $admin_id);
        $stmt->execute();
        $stmt->close();
        return "Done add lesson!";
    }

    public function edit_lesson($lesson_id, $lesson_name, $lesson_detail)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("UPDATE lesson SET lesson_name=?, lesson_detail=? WHERE lesson_id=?");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done edit lesson!";
    }

    public function delete_lesson($lesson_id)
    {
        global $mysql_server, $username, $password, $database;
        $problem_list = array();
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("DELETE FROM lesson WHERE lesson_id = ?");
        $stmt->bind_param("s", $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done deleting lesson!";
    }
}
?>