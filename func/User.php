<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class User
{
    var $mysql_connection, $db_server, $db_username, $db_password, $db, $salt, $iterations, $length, $algor;
    public function __construct()
    {
        global $mysql_server, $username, $password, $database, $salt, $iterations, $length, $algor;
        $this->db_server = $mysql_server;
        $this->db_username = $username;
        $this->db_password = $password;
        $this->db = $database;
        $this->salt = $salt;
        $this->iterations = $iterations;
        $this->length = $length;
        $this->algor = $algor;
        $this->mysql_connection = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db);
    }

    public function add_user($data)
    {
        $result = "true";
        if ($this->isUserExists($data["username"], false) == false) {
            $hashed_password = hash_pbkdf2($this->algor, $data["password"], $this->salt, $this->iterations, $this->length);
            $stmt = $this->mysql_connection->prepare("INSERT INTO users (username, password, role) VALUES(?, ?, ?)");
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
        $result = "true";
        if ($this->isUserExists($data["username"], true, $data["user_id"]) == false) {
            $hashed_password = hash_pbkdf2($this->algor, $data["password"], $this->salt, $this->iterations, $this->length);
            $stmt = $this->mysql_connection->prepare("UPDATE users SET username=?, password=?, role=? WHERE user_id=?");
            $stmt->bind_param("ssss", $data["username"], $hashed_password, $data["role"], $data["user_id"]);
            $stmt->execute();
            $stmt->close();
            $this->update_user_detail($data);
        } else {
            $result = "false";
        }
        return $result;
    }

    public function delete_user($user_id)
    {
        $stmt = $this->mysql_connection->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    private function update_user_detail($data)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE user_detail SET name=? WHERE user_id = ?");
        $stmt->bind_param("ss", $data["name"], $data["user_id"]);
        $stmt->execute();
        $stmt->close();
    }

    private function isUserExists($user, $isUpdate, $user_id = 0)
    {
        $result = false;
        if ($isUpdate == true) {
            $stmt = $this->mysql_connection->prepare("SELECT username FROM users WHERE username = ? AND user_id != ?");
            $stmt->bind_param("ss", $user, $user_id);
        } else {
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
        $stmt = $this->mysql_connection->prepare("INSERT INTO user_detail (user_id, name) VALUES(?, ?)");
        $stmt->bind_param("is", intval($user_id), $data["name"]);
        $stmt->execute();
        $stmt->close();
    }

    public function examination_check($user_group)
    {
        $result = false;
        $stmt = $this->mysql_connection->prepare("SELECT COUNT(*) FROM examination WHERE user_group = ?");
        $stmt->bind_param("s", $user_group);
        $stmt->execute();
        $stmt->bind_result($exam_count);
        while ($stmt->fetch()) {
            if ($exam_count > 0) {
                $result = true;
            }
        }
        return $result;
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>