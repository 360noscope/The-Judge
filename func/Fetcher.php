<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Fetcher
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

    public function fetch_exercise_detail($id)
    {
        $result = array();
        $exercise_done_msg = "";
        $stmt = $this->mysql_connection->prepare("SELECT complete_date FROM exercise_session WHERE " .
            "user_id = ? AND exercise_id = ?");
        $stmt->bind_param("ss", $_SESSION["stu_id"], $id);
        $stmt->execute();
        $stmt->bind_result($complete_date);
        while ($stmt->fetch()) {
            if ($complete_date !== "-") {
                $exercise_done_msg = "<div class='alert alert-success' role='alert'>You completed this exercise! " .
                    "You can still submit your code but <b>your score will be updated with latest judge result</b></div>";
            }
        }
        $stmt->close();

        $this->mysql_connection->connect($this->db_server, $this->db_username, $this->db_password, $this->db);
        $stmt = $this->mysql_connection->prepare("SELECT exercise_name, exercise_detail, difficulty, hint, exec_time, exec_memory, completed_score FROM exercise WHERE " .
            "exercise_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->bind_result($name, $detail, $difficulty, $hint, $exectime, $memory, $completed_score);
        while ($stmt->fetch()) {
            array_push($result, array(
                "exercise_name" => $name,
                "exercise_detail" => $detail,
                "exercise_diff" => $difficulty,
                "exercise_hint" => $hint,
                "time_limit" => $exectime,
                "memory_limit" => $memory,
                "done_msg" => $exercise_done_msg
            ));
        }
        $stmt->close();
        $this->mysql_connection->close();
        $_SESSION["selected_exercise"] = $id;
        $_SESSION["mem_limit"] = $memory;
        $_SESSION["time_limit"] = $exectime;
        $_SESSION["selected_exercise_name"] = $name;
        $_SESSION["completed_score"] = $completed_score;
        return $result;
    }

    public function fetch_lesson()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT @les_id := lesson_id, lesson_name, lesson_detail, " .
            "(SELECT COUNT(*) FROM user_enrollment WHERE user_id = ? AND lesson_id = @les_id) FROM lesson");
        $stmt->bind_param("s", $_SESSION["stu_id"]);
        $stmt->execute();
        $stmt->bind_result($id, $name, $detail, $enrolled);
        while ($stmt->fetch()) {
            $btn_style = "btn btn-success";
            $btn_text = "Enroll me!";
            $btn_call = "enroll_lesson(this.value)";
            if ($enrolled != 0) {
                $btn_style = "btn btn-danger";
                $btn_text = "Unenroll me!";
                $btn_call = "unenroll_lesson(this.value)";
            }
            array_push(
                $result,
                array($name, $detail, "<button type='submit' onclick='" . $btn_call . "' name='lesson_id' value='" . $id . "' class='" . $btn_style . "'>" . $btn_text . "</buton>")
            );
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetchExercise()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT exercise.exercise_id, exercise.exercise_name, " .
            "exercise.difficulty, lesson.lesson_name FROM exercise JOIN user_enrollment ON exercise.lesson_id = user_enrollment.lesson_id " .
            "JOIN lesson ON exercise.lesson_id = lesson.lesson_id " .
            "WHERE user_enrollment.user_id = ? AND exercise.exercise_status = 'ACTIVATED'");
        $stmt->bind_param("s", $_SESSION["stu_id"]);
        $stmt->execute();
        $stmt->bind_result($id, $name, $difficulty, $lesson_name);
        while ($stmt->fetch()) {
            $rate1 = "";
            $rate2 = "";
            $rate3 = "";
            $rate4 = "";
            $rate5 = "";
            switch (intval($difficulty)) {
                case 1:
                    $rate1 = "yellow";
                    break;
                case 2:
                    $rate1 = "yellow";
                    $rate2 = "yellow";
                    break;
                case 3:
                    $rate1 = "yellow";
                    $rate2 = "yellow";
                    $rate3 = "yellow";
                    break;
                case 4:
                    $rate1 = "yellow";
                    $rate2 = "yellow";
                    $rate3 = "yellow";
                    $rate4 = "yellow";
                    break;
                case 5:
                    $rate1 = "yellow";
                    $rate2 = "yellow";
                    $rate3 = "yellow";
                    $rate4 = "yellow";
                    $rate5 = "yellow";
                    break;
            }
            $star = "<div class='row'>" .
                "<div class='col-lg-12'>" .
                "<div class='star-rating'>" .
                "<span class='fa fa-star' style='color: " . $rate1 . ";'></span>" .
                "<span class='fa fa-star' style='color: " . $rate2 . ";'></span>" .
                "<span class='fa fa-star' style='color: " . $rate3 . ";'></span>" .
                "<span class='fa fa-star' style='color: " . $rate4 . ";'></span>" .
                "<span class='fa fa-star' style='color: " . $rate5 . ";'></span>" .
                "</div></div></div>";
            array_push($result, array($name, $lesson_name, $star));
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetch_submit_history()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT  @prob_id := exercise_session.exercise_id, " .
            "exercise.exercise_name, exercise_session.passed_case, " .
            "(SELECT COUNT(*) FROM exercise_testcase WHERE exercise_id = @prob_id), exercise_session.complete_date, " .
            "exercise_session.try_date " .
            "FROM exercise_session " .
            "JOIN exercise ON exercise_session.exercise_id = exercise.exercise_id " .
            "WHERE exercise_session.user_id = ?");
        $stmt->bind_param("s", $_SESSION["stu_id"]);
        $stmt->execute();
        $stmt->bind_result($id, $exercise_name, $passed_case, $total_case, $completed_date, $try_date);
        while ($stmt->fetch()) {
            $percentage = (floatval($passed_case) * 100.00) / floatval($total_case);
            array_push($result, array($exercise_name, $percentage, $completed_date, $try_date));
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetchAdminLesson($admin_id)
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT lesson_id, lesson_name, lesson_detail FROM lesson WHERE owner_id = ?");
        $stmt->bind_param("s", $admin_id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $detail);
        while ($stmt->fetch()) {
            array_push($result, array(
                $id,
                $name,
                $detail
            ));
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetchAdminExercise()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT @prob_id := exercise.exercise_id, exercise.exercise_name, " .
            "lesson.lesson_name, (SELECT COUNT(*) FROM exercise_testcase WHERE exercise_id = @prob_id), exercise.exercise_status FROM lesson " .
            "JOIN exercise ON exercise.lesson_id = lesson.lesson_id");
        $stmt->execute();
        $stmt->bind_result($id, $name, $lesson, $case_count, $status);
        while ($stmt->fetch()) {
            $exercise_status_text = "Not Display";
            $exercise_status_class = "badge badge-danger";
            if ($status == "ACTIVATED") {
                $exercise_status_text = "Displayed";
                $exercise_status_class = "badge badge-success";
            }
            array_push($result, array($id, $name, $lesson, $case_count, "<h5 class='text-center'><span class='" . $exercise_status_class . "'>" . $exercise_status_text . "</span></h5>"
            ));
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetch_admin_exercise_data($exercise_id)
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT exercise_name, lesson_id, " .
            "exercise_detail, exec_time, exec_memory, hint, difficulty " .
            "FROM exercise WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->bind_result($name, $lesson_id, $detail, $exec_time, $memory, $hint, $difficulty);
        while ($stmt->fetch()) {
            array_push(
                $result,
                array(
                    "exer_name" => $name,
                    "lesson_id" => $lesson_id,
                    "exer_detail" => $detail,
                    "exer_exectime" => $exec_time,
                    "exer_mem" => $memory,
                    "exer_hint" => $hint,
                    "exer_diff" => $difficulty,
                    "exercise_id" => $exercise_id
                )
            );
        }
        $stmt->close();
        return json_encode(array("exercise_data" => $result, "testcase_data" => $this->fetch_exercise_testcase($exercise_id)));
    }

    private function fetch_exercise_testcase($exercise_id)
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT testcase_id, score, input, output FROM exercise_testcase " .
            "WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->bind_result($testcase_id, $score, $input, $output);
        while ($stmt->fetch()) {
            array_push($result, array(
                "testcase_score" => $score,
                "testcase_input" => $input,
                "testcase_output" => $output
            ));
        }
        $stmt->close();
        return $result;
    }

    public function fetch_exercise_name($exercise_id)
    {
        $result = "";
        $stmt = $this->mysql_connection->prepare("SELECT exercise_name FROM exercise WHERE exercise_id = ?");
        $stmt->bind_param("s", $exercise_id);
        $stmt->execute();
        $stmt->bind_result($name);
        while ($stmt->fetch()) {
            $result = $name;
        }
        $stmt->close();
        return $result;
    }

    public function fetch_admin_user_list()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT users.user_id, users.role, user_detail.name, user_detail.score " .
            "FROM users JOIN user_detail ON users.user_id = user_detail.user_id");
        $stmt->execute();
        $stmt->bind_result($id, $role, $name, $score);
        while ($stmt->fetch()) {
            array_push($result, array(
                "<input type='radio' name='user_id' value='" . $id . "'>",
                $name, $score, $role
            ));
        }
        $stmt->close();
        return json_encode(array("data" => $result));
    }

    public function fetch_user_data($user_id)
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT users.role, user_detail.name, users.username " .
            "FROM users JOIN user_detail ON users.user_id = user_detail.user_id " .
            "WHERE users.user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($role, $name, $username);
        while ($stmt->fetch()) {
            $result = array("username" => $username, "name" => $name, "role" => $role);
        }
        $stmt->close();
        return json_encode($result);
    }

    public function fetch_total_userstats($user_id)
    {
        //Get user total score
        $total_score = 0;
        $stmt = $this->mysql_connection->prepare("SELECT total_score FROM exercise_session WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($score);
        while ($stmt->fetch()) {
            $total_score += intval($score);
        }
        $stmt->close();
        $this->mysql_connection->close();

        //Get user remaining exercise to do
        $remaining_exercise = 0;
        $this->mysql_connection->connect($this->db_server, $this->db_username, $this->db_password, $this->db);
        $stmt = $this->mysql_connection->prepare("SELECT COUNT(*) FROM exercise " .
            "LEFT JOIN exercise_session ON exercise.exercise_id = exercise_session.exercise_id " .
            "WHERE exercise_session.complete_date = '-' AND exercise_session.user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($exercise_count);
        while ($stmt->fetch()) {
            $remaining_exercise = intval($exercise_count);
        }
        $stmt->close();
        $this->mysql_connection->close();

        //Get total user
        $total_user = 0;
        $this->mysql_connection->connect($this->db_server, $this->db_username, $this->db_password, $this->db);
        $stmt = $this->mysql_connection->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $stmt->bind_result($count);
        while($stmt->fetch()){
            $total_user = $count;
        }
        $stmt->close();

        //Get user rank
        $ranking = 0;
        $this->mysql_connection->connect($this->db_server, $this->db_username, $this->db_password, $this->db);
        $stmt = $this->mysql_connection->prepare("SELECT  @curRank := @curRank + 1 AS rank, user_id AS userid " .
            "FROM users p, (SELECT @curRank := 0) s " .
            "ORDER BY CAST(ifnull((SELECT sum(total_score) FROM exercise_session WHERE user_id = userid), 0) " .
            "AS unsigned) DESC");
        $stmt->execute();
        $stmt->bind_result($rank, $id);
        while ($stmt->fetch()) {
            if ($id == $user_id) {
                $ranking = $rank;
            }
        }
        $stmt->close();
        $this->mysql_connection->close();

        return json_encode(array(
            "total_score" => $total_score,
            "left_exercise" => $remaining_exercise,
            "total_user"=>$total_user,
            "ranking" => $ranking
        ));
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
    }
}
?>