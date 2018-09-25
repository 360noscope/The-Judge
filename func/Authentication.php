<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Authentication
{
    var $mysql_connection;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        global $mysql_server,
            $username,
            $password,
            $database;
        $this->mysql_connection = new mysqli($mysql_server, $username, $password, $database);
        $this->mysql_connection->set_charset("utf8");
    }

    public function login($username, $password)
    {
        global
            $salt,
            $iterations,
            $length,
            $algor;
        $login_result = array();
        $hashed_password = hash_pbkdf2($algor, $password, $salt, $iterations, $length);
        $stmt = $this->mysql_connection->prepare("SELECT users.password, users.user_id, user_detail.name, users.role FROM users JOIN user_detail " .
            "ON users.user_id = user_detail.user_id WHERE users.username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($pass, $id, $name, $role);
        while ($stmt->fetch()) {
            if (hash_equals($pass, $hashed_password) && $role == "STUDENT") {
                $_SESSION["stu_id"] = $id;
                $_SESSION["stu_name"] = $name;
                array_push($login_result, array("type" => "student", "flag" => "ok"));
            } else if (hash_equals($pass, $hashed_password) && $role == "ADMIN") {
                $_SESSION["admin_id"] = $id;
                $_SESSION["admin_name"] = $name;
                array_push($login_result, array("type" => "admin", "flag" => "ok"));
            } else {
                array_push($login_result, array("flag" => "fail"));
            }
        }
        $stmt->close();
        return json_encode($login_result);
    }

    public function logout()
    {
        session_start();
        session_destroy();
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>