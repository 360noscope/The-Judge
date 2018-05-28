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

    public function add_lesson($lesson_name, $lesson_detail, $admin_id)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO lesson (lesson_name, lesson_detail, owner_id) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $admin_id);
        $stmt->execute();
        $stmt->close();
        return "Done add lesson!";
    }

    public function edit_lesson($lesson_id, $lesson_name, $lesson_detail)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE lesson SET lesson_name=?, lesson_detail=? WHERE lesson_id=?");
        $stmt->bind_param("sss", $lesson_name, $lesson_detail, $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done edit lesson!";
    }

    public function delete_lesson($lesson_id)
    {
        $problem_list = array();
        $stmt = $this->mysql_connection->prepare("DELETE FROM lesson WHERE lesson_id = ?");
        $stmt->bind_param("s", $lesson_id);
        $stmt->execute();
        $stmt->close();
        return "Done deleting lesson!";
    }

    public function activate_exam_mode($owner_id, $lesson_name, $user_group)
    {
        $today = $date = date('d/m/Y h:i:s a');
        $exam_status = "PROCESSING";
        $stmt = $this->mysql_connection->prepare("INSERT INTO examination (exam_name, " .
            "exam_owner, user_group, exam_time, exam_status) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $lesson_name, $owner_id, $user_group, $today, $exam_status);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->mysql_connection->prepare("INSERT INTO lesson (owner_id, lesson_name, ".
        "lesson_detail, is_exam) VALUES(?, ?, 'EXAM!', 'YES')");
        $stmt->bind_param("ss", $owner_id, $lesson_name);
        $stmt->execute();
        $stmt->close();
    }

    public function deactivate_exam_mode($owner_id, $exam_id)
    {
        $lesson_id = "";
        $stmt = $this->mysql_connection->prepare("SELECT exam_lesson FROM examination WHERE exam_id = ?");
        $stmt->bind_param("s", $exam_id);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
            $lesson_id = $id;
        }
        $stmt->close();

        $stmt = $this->mysql_connection->prepare("DELETE FROM examination WHERE owner_id = ? AND exam_id = ?");
        $stmt->bind_param("ss", $owner_id, $exam_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->mysql_connection->prepare("DELETE FROM lesson WHERE owner_id = ? AND lesson_id = ?");
        $stmt->bind_param("ss", $owner_id, $lesson_id);
        $stmt->execute();
        $stmt->close();
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>