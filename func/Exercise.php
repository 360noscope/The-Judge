<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Exercise
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

    public function add_exercise($name, $detail, $lesson, $exectime, $execmem, $diff, $hint, $case)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO exercise (lesson_id, " .
            "exercise_name, " .
            "exercise_detail, " .
            "exec_time, exec_memory, " .
            "hint, " .
            "difficulty) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $lesson, $name, $detail, $exectime, $execmem, $hint, $diff);
        $stmt->execute();
        $exercise_id = $stmt->insert_id;
        $stmt->close();
        return $this->add_testcase($exercise_id, $case);
    }

    private function add_testcase($exercise_id, $case)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO exercise_testcase (exercise_id, score, input, output) " .
            "VALUES (?, ?, ?, ?)");
        for ($index = 0; $index < count($case); $index++) {
            $stmt->bind_param("ssss", $exercise_id, $case[$index][2], $case[$index][0], $case[$index][1]);
            $stmt->execute();
        }
        $stmt->close();
        return "Done add exercise!".$exercise_id;
    }

    public function update_testcase_json($case_data, $exercise_id){
        $this->clear_exercise_case($exercise_id);
        $stmt = $this->mysql_connection->prepare("INSERT INTO exercise_testcase (exercise_id, score, input, output) " .
        "VALUES (?, ?, ?, ?)");
        foreach($case_data["test_case"] as $item){
            $stmt->bind_param("ssss", $exercise_id, $item["score"], $item["input"], $item["output"]);
            $stmt->execute();
        }
        $stmt->close();
        return "Done!";
    }

    public function activate_exercise($exercise_id)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET".
        " exercise_status = IF(exercise_status = 'HIDDEN', 'ACTIVATED', 'HIDDEN') ".
        "WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->close();
        return $exercise_id . " Activated!";
    }
    public function activate_exercise_by_lesson($lesson_id)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET".
        " exercise_status = IF(exercise_status = 'HIDDEN', 'ACTIVATED', 'HIDDEN') ".
        "WHERE lesson_id = ?");
        $stmt->bind_param("s", $lesson_id);
        $stmt->execute();
        $stmt->close();
        return $exercise_id . " Activated!";
    }

    public function update_exercise($exercise_data)
    {
        $this->clear_exercise_case($exercise_data["exercise_id"]);
        $this->add_testcase($exercise_data["exercise_id"], $exercise_data["case_data"]);
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET exercise_name = ?, exercise_detail = ?, exec_time = ?, " .
            "exec_memory = ?, hint = ?, difficulty = ?, lesson_id = ? WHERE exercise_id = ?");
        $stmt->bind_param(
            "ssssssss",
            $exercise_data["exercise_name"],
            $exercise_data["exercise_detail"],
            $exercise_data["exercise_exectime"],
            $exercise_data["exericise_execmem"],
            $exercise_data["exercise_hint"],
            $exercise_data["exercise_diff"],
            $exercise_data["exercise_lesson"],
            $exercise_data["exercise_id"]
        );
        $stmt->execute();
        $stmt->close();
    }

    private function clear_exercise_case($selected_exercise){
        $stmt = $this->mysql_connection->prepare("DELETE FROM exercise_testcase WHERE exercise_id = ?");
        $stmt->bind_param("s", $selected_exercise);
        $stmt->execute();
        $stmt->close();
    }

    public function delete_exercise($exercise_id){
        $stmt = $this->mysql_connection->prepare("DELETE FROM exercise WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->close();
    }
    
    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>