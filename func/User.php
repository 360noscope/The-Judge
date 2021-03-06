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

    public function addUser($data)
    {
        $result = "true";
        if ($this->isUserExists($data["username"], false) == false) {
            $hashed_password = hash_pbkdf2($this->algor, $data["password"], $this->salt, $this->iterations, $this->length);
            $stmt = $this->mysql_connection->prepare("INSERT INTO users (username, password, role) VALUES(?, ?, ?)");
            $stmt->bind_param("sss", $data["username"], $hashed_password, $data["role"]);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();
            $this->addUserDetail($user_id, $data);
        } else {
            $result = "false";
        }
        return $result;
    }

    private function addUserDetail($user_id, $data)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO user_detail (user_id, name) VALUES(?, ?)");
        $stmt->bind_param("ss", $user_id, $data["name"]);
        $stmt->execute();
        $stmt->close();
    }

    public function updateUser($data)
    {
        $hashed_password = hash_pbkdf2($this->algor, $data["password"], $this->salt, $this->iterations, $this->length);
        $stmt = $this->mysql_connection->prepare("UPDATE users SET username=?, password=?, role=? WHERE user_id=?");
        $stmt->bind_param("ssss", $data["username"], $hashed_password, $data["role"], $data["id"]);
        $stmt->execute();
        $stmt->close();
        $this->updateUserDetail($data);
    }

    private function updateUserDetail($data)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE user_detail SET name=? WHERE user_id = ?");
        $stmt->bind_param("ss", $data["name"], $data["id"]);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteUser($user_id)
    {
        $stmt = $this->mysql_connection->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();

        $this->orderID();
    }

    private function isUserExists($user)
    {
        $result = false;
        try {
            $stmt = $this->mysql_connection->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->bind_result($user_count);
            while ($stmt->fetch()) {
                if ($user_count > 0) {
                    $result = true;
                }
            }
        } catch (Exception $ex) {
            return $ex;
        }
        return $result;
    }

    private function orderID()
    {
        try {
            $stmt = $this->mysql_connection->prepare("ALTER TABLE users AUTO_INCREMENT = 1");
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