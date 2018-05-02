<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class User
{
    public function add_user($data)
    {
        global $mysql_server, $username, $password, $database, $salt, $iterations, $length, $algor;
        $result = "true";
        if ($this->isUserExists($data["username"], false) == false) {
            $hashed_password = hash_pbkdf2($algor, $data["password"], $salt, $iterations, $length);
            $connection = new mysqli($mysql_server, $username, $password, $database);
            $stmt = $connection->prepare("INSERT INTO users (username, password, role) VALUES(?, ?, ?)");
            $stmt->bind_param("sss", $data["username"], $hashed_password, $data["role"]);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();
            $this->add_user_detail($user_id, $data);
        } else {
            $result = "false";
        }
        return $result;
    }

    public function edit_user($data)
    {
        global $mysql_server, $username, $password, $database, $salt, $iterations, $length, $algor;
        $result = "true";
        if ($this->isUserExists($data["username"], true, $data["user_id"]) == false) {
            $hashed_password = hash_pbkdf2($algor, $data["password"], $salt, $iterations, $length);
            $connection = new mysqli($mysql_server, $username, $password, $database);
            $stmt = $connection->prepare("UPDATE users SET username=?, password=?, role=? WHERE user_id=?");
            $stmt->bind_param("ssss", $data["username"], $hashed_password, $data["role"], $data["user_id"]);
            $stmt->execute();
            $stmt->close();
            $this->update_user_detail($data);
        } else {
            $result = "false";
        }
        return $result;
    }

    public function delete_user($user_id){
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    private function update_user_detail($data)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("UPDATE user_detail SET name=? WHERE user_id = ?");
        $stmt->bind_param("ss", $data["name"], $data["user_id"]);
        $stmt->execute();
        $stmt->close();
    }

    private function isUserExists($user, $isUpdate, $user_id=0)
    {
        global $mysql_server, $username, $password, $database;
        $result = false;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        if ($isUpdate == true) {
            $stmt = $connection->prepare("SELECT username FROM users WHERE username = ? AND user_id != ?");
            $stmt->bind_param("ss", $user, $user_id);
        }else{
            $stmt = $connection->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->bind_param("s", $user);
        }
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        if ($result > 0) {
            $result = true;
        }
        $stmt->close();
        return $result;
    }

    private function add_user_detail($user_id, $data)
    {
        global $mysql_server, $username, $password, $database;
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("INSERT INTO user_detail (user_id, name) VALUES(?, ?)");
        $stmt->bind_param("is", intval($user_id), $data["name"]);
        $stmt->execute();
        $stmt->close();
    }
}
?>