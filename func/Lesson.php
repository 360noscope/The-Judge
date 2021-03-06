<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Lesson
{
    var $mysql_connection, $db_server, $db_username, $db_password, $db;
    public function __construct()
    {
        global $mysql_server, $username, $password, $database;
        $this->db_server = $mysql_server;
        $this->db_username = $username;
        $this->db_password = $password;
        $this->db = $database;
        $this->mysql_connection = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db);
    }

    public function enroll($lesson_id)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO user_enrollment (user_id, lesson_id) VALUES(?, ?)");
        $stmt->bind_param("ss", $_SESSION["stu_id"], $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done enroll you!";
    }

    public function unenroll($lesson_id)
    {
        $stmt = $this->mysql_connection->prepare("DELETE FROM user_enrollment WHERE user_id = ? AND lesson_id = ?");
        $stmt->bind_param("ss", $_SESSION["stu_id"], $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done unenroll you!";
    }

    public function addLesson($lesson_name, $lesson_detail, $admin_id)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO lesson (lesson_name, lesson_detail, owner_id) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $admin_id);
        $stmt->execute();
        $stmt->close();
        return "Done add lesson!";
    }

    public function editLesson($lesson_id, $lesson_name, $lesson_detail)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE lesson SET lesson_name=?, lesson_detail=? WHERE lesson_id=?");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done edit lesson!";
    }

    public function deleteLesson($lesson_id)
    {
        $problem_list = array();
        $stmt = $this->mysql_connection->prepare("DELETE FROM lesson WHERE lesson_id = ?");
        $stmt->bind_param("s", $lesson_id);
        $stmt->execute();
        $stmt->close();
        $this->orderID();
        return "Done deleting lesson!";
    }

    private function orderID(){
        try {
            $stmt = $this->mysql_connection->prepare("ALTER TABLE lesson AUTO_INCREMENT = 1");
            $stmt->execute();
            $stmt->close();
        } catch (Exception $ex) {
            return $ex;
        }
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>