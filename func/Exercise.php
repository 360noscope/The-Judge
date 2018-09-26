<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Exercise
{
    var $mysql_connection, $db_server, $db_username, $db_password, $db;
    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        global $mysql_server, $username, $password, $database;
        $this->db_server = $mysql_server;
        $this->db_username = $username;
        $this->db_password = $password;
        $this->db = $database;
        $this->mysql_connection = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db);
    }

    public function addExercise($exercise_data)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO exercise (lesson_id, " .
            "exercise_name, " .
            "exercise_detail, " .
            "exec_time, exec_memory, " .
            "hint, " .
            "difficulty) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssss",
            $exercise_data["lesson"],
            $exercise_data["name"],
            $exercise_data["detail"],
            $exercise_data["exec_time"],
            $exercise_data["exec_mem"],
            $exercise_data["hint"],
            $exercise_data["diff"]
        );
        $stmt->execute();
        $exercise_id = $stmt->insert_id;
        $stmt->close();
        return $this->addTestCase($exercise_id, $exercise_data["input_data"]);
    }

    private function addTestCase($exercise_id, $case)
    {
        $stmt = $this->mysql_connection->prepare("INSERT INTO exercise_testcase (exercise_id, score, input, output) " .
            "VALUES (?, ?, ?, ?)");
       foreach($case as $item){
            $stmt->bind_param("ssss", $exercise_id, $item["score"], $item["input"], $item["output"]);
            $stmt->execute();
        }
        $stmt->close();
        return "Done add exercise!" . $exercise_id;
    }

    public function activate_exercise($exercise_id)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET" .
            " exercise_status = IF(exercise_status = 'HIDDEN', 'ACTIVATED', 'HIDDEN') " .
            "WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->close();
        return $exercise_id . " Activated!";
    }
    public function activate_exercise_by_lesson($lesson_id)
    {
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET" .
            " exercise_status = IF(exercise_status = 'HIDDEN', 'ACTIVATED', 'HIDDEN') " .
            "WHERE lesson_id = ?");
        $stmt->bind_param("s", $lesson_id);
        $stmt->execute();
        $stmt->close();
        return $exercise_id . " Activated!";
    }

    public function updateExercise($exercise_data)
    {
        $this->clearExerciseCase($exercise_data["id"]);
        $this->addTestCase($exercise_data["id"], $exercise_data["input_data"]);
        $stmt = $this->mysql_connection->prepare("UPDATE exercise SET exercise_name = ?, exercise_detail = ?, exec_time = ?, " .
            "exec_memory = ?, hint = ?, difficulty = ?, lesson_id = ? WHERE exercise_id = ?");
        $stmt->bind_param(
            "ssssssss",
            $exercise_data["name"],
            $exercise_data["detail"],
            $exercise_data["exec_time"],
            $exercise_data["exec_mem"],
            $exercise_data["hint"],
            $exercise_data["diff"],
            $exercise_data["lesson"],
            $exercise_data["exercise_id"]
        );
        $stmt->execute();
        $stmt->close();
    }

    private function clearExerciseCase($selected_exercise)
    {
        $stmt = $this->mysql_connection->prepare("DELETE FROM exercise_testcase WHERE exercise_id = ?");
        $stmt->bind_param("s", $selected_exercise);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteExercise($exercise_id)
    {
        $stmt = $this->mysql_connection->prepare("DELETE FROM exercise WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->close();

        $this->orderID();
        $this->orderCaseID();
    }

    private function orderID(){
        try {
            $stmt = $this->mysql_connection->prepare("ALTER TABLE exercise AUTO_INCREMENT = 1");
            $stmt->execute();
            $stmt->close();
        } catch (Exception $ex) {
            return $ex;
        }
    }

    private function orderCaseID(){
        try {
            $stmt = $this->mysql_connection->prepare("ALTER TABLE exercise_testcase AUTO_INCREMENT = 1");
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