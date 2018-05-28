<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Authentication
{
    public function login($username, $password)
    {
        global $mysql_server,
            $username,
            $password,
            $database,
            $salt,
            $iterations,
            $length,
            $algor;
        $login_result = array();
        $hashed_password = hash_pbkdf2($algor, $_POST["password"], $salt, $iterations, $length);
        $connection = new mysqli($mysql_server, $username, $password, "the_judge");
        $stmt = $connection->prepare("SELECT users.password, users.user_id, user_detail.name, ".
        "user_group.group_name, user_group.group_id FROM users ".
        "JOIN user_detail ON users.user_id = user_detail.user_id ".
        "JOIN user_group ON users.group_id = user_group.group_id ".
        "WHERE users.username = ?");
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        $stmt->bind_result($pass, $id, $name, $role, $group_id);
        $user_type = "none";
        $login_flag = "fail";
        while ($stmt->fetch()) {
            if (hash_equals($pass, $hashed_password) && $role === "TEACHER") {
                $_SESSION["admin_id"] = $id;
                $_SESSION["admin_name"] = $name;
                $_SESSION["user_group"] = $group_id;
                $user_type = $role;
                $login_flag = "ok";
            } else if (hash_equals($pass, $hashed_password) && $role !== "TEACHER") {
                $_SESSION["stu_id"] = $id;
                $_SESSION["stu_name"] = $name;
                $_SESSION["user_group"] = $group_id;
                $user_type = $role;
                $login_flag = "ok";
            } 
        }
        array_push($login_result, array("type" => $user_type, "flag" => $login_flag));
        $stmt->close();
        return $login_result;
    }

    public function logout()
    {
        session_start();
        session_destroy();
    }
}
?>