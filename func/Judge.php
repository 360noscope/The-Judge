<?php 
include_once("config.php");
class Judge
{
    var $user_id, $mysql_connection, $con, $ftp_path;
    public function __construct()
    {
        global $mysql_server, $username, $password, $database, $ftp_host, $ftp_port, $ftp_timeout,
            $ftp_path, $ftp_user, $ftp_pass;
        $this->user_id = $_SESSION["stu_id"];
        $this->ftp_path = $ftp_path;
        $this->con = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
        ftp_login($this->con, $ftp_user, $ftp_pass);
        ftp_pasv($this->con, true);
        $this->mysql_connection = new mysqli($mysql_server, $username, $password, $database);
    }

    public function judging($file)
    {
        //prepare resource for sandbox to execute on!
        //upload python code file with named : exercise-{studentid}-{exerciseid}.py
        $code_file_name = $this->codeUploader($file);
        $case_data = $this->getCaseInputOutput();
        $testcase_list = array();
        $input_counter = 1;
        foreach ($case_data as $item) {
            //upload input file with named : testcase-{studentid}-{exerciseid}-{testcasecount}.txt
            if ($item["input"] !== "-") {
                array_push($testcase_list, array(
                    "input" => $this->inputUploader($input_counter, $item["input"]),
                    "output" => $item["output"],
                    "score" => $item["score"]
                ));
            } else {
                array_push($testcase_list, array(
                    "input" => "-",
                    "output" => $item["output"],
                    "score" => $item["score"]
                ));
            }
            $input_counter += 1;
        }
        /*Execute uploaded resource
          1. Create Container for execute code and bind container volume to host directory {/python-judge}
          2. Start that container peacefuly
          3. Create excecute cmd instance for run code on started container
          4. Run that execute instance goddamn it!
          5. Kill that container after done
          6. Remove input file if there're any*/

        $exec_result = array();
        $container_id = $this->createCodeContainer();
        $container_start_result = $this->startContainer($container_id);
        $case_counter = 1;
        foreach ($testcase_list as $testcase_item) {
            $exec_id = "AHH";
            if ($testcase_item["input"] !== "-") {
                $exec_id = $this->createExecuter($container_id, $code_file_name, true, $testcase_item["input"]);
            } else {
                $exec_id = $this->createExecuter($container_id, $code_file_name);
            }
            /*Need to use preg_replace() to remove special control char that coming from docker execute api response
            solution from below
            https://stackoverflow.com/questions/1497885/remove-control-characters-from-php-string*/

            $result = preg_replace('/[\x00-\x1F\x7F]/', '', $this->startExecuter($exec_id));
            $result = str_replace("Killed", "", $result);
            if (strlen($result) != 0) {
                $converted_res = ($testcase_item["output"] === $result) ? 'true' : 'false';
                array_push($exec_result, array("case_number" => $case_counter, "result" => $converted_res, "output" => $result, "score" => $testcase_item["score"]));
            } else {
                array_push($exec_result, array("case_number" => $case_counter, "result" => "T", "output" => $result, "score" => $testcase_item["score"]));
            }
            $case_counter += 1;
        }
        $this->removeInput($testcase_list);
        $this->killContainer($container_id);
        $this->recordSession($exec_result, $case_counter);

        //Return result for debug and testing going to submit result page soon!
        return $exec_result;
    }

    private function codeUploader($file_input)
    {
        $file_name = "";
        if ($file_input['name'] <> null) {
            $file_name = "exercise-" . $this->user_id . "-" . $_SESSION["selected_exercise"] . ".py";
            $file_path = $this->ftp_path . $file_name;
            ftp_put($this->con, $file_path, $file_input['tmp_name'], FTP_ASCII);
        } else {
            $file_name = "error";
        }
        return $file_name;
    }

    private function inputUploader($input_count, $input)
    {
        $file_name = "";
        $file_name = "testcase-" . $this->user_id . "-" . $_SESSION["selected_exercise"] . "-" . $input_count . ".txt";
        $file_path = $this->ftp_path . $file_name;
        $fp = fopen('php://temp', 'r+');
        fwrite($fp, $input);
        rewind($fp);
        ftp_fput($this->con, $file_path, $fp, FTP_ASCII);
        return $file_name;
    }

    private function removeInput($input_list)
    {
        foreach ($input_list as $file_input) {
            if ($file_input["input"] !== "-") {
                $file_path = $this->ftp_path . $file_input["input"];
                ftp_delete($this->con, $file_path);
            }
        }
    }

    private function getCaseInputOutput()
    {
        $result = array();
        $stmt = $this->mysql_connection->prepare("SELECT input, output, score FROM exercise_testcase WHERE exercise_id = ?");
        $stmt->bind_param("s", $_SESSION["selected_exercise"]);
        $stmt->execute();
        $stmt->bind_result($case_input, $case_output, $score);
        $input_output_counter = 1;
        while ($stmt->fetch()) {
            array_push($result, array("input" => $case_input, "output" => $case_output, "score" => $score));
            $input_output_counter += 1;
        }
        $stmt->close();
        return $result;
    }

    private function createCodeContainer()
    {
        global $sandbox_ip, $sandbox_port;
        $data_create_container = json_encode(array(
            "AttachStdin" => true,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "OpenStdin" => true,
            "Tty" => false,
            "Volumes" => array("/python-judge" => json_decode("{}")),
            "Image" => "python-sandbox",
            "NetworkDisabled" => true,
            "StopSignal" => "SIGKILL",
            "HostConfig" => array(
                "Binds" => array(
                    "/python-judge:/python-judge"
                ),
                "Memory" => (intval($_SESSION["mem_limit"]) * 1048576),
                "OomKillDisable" => false,
                "AutoRemove" => true,
            )
        ), JSON_UNESCAPED_SLASHES);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $sandbox_ip . "/containers/create");
        curl_setopt($ch, CURLOPT_PORT, $sandbox_port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $data_create_container
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return json_decode($server_output, true)["Id"];
    }

    private function startContainer($container_id)
    {
        global $sandbox_ip, $sandbox_port;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $sandbox_ip . "/containers/" . $container_id . "/start");
        curl_setopt($ch, CURLOPT_PORT, $sandbox_port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }

    private function createExecuter($container_id, $file_name, $input = false, $file_input_name = "")
    {
        global $sandbox_ip, $sandbox_port;
        //We used timeout function to kill python process within time limit
        //ref. https://busybox.net/downloads/BusyBox.html
        $execute_cmd = "timeout -t " . $_SESSION["time_limit"] . " -s 'SIGKILL' python " . $file_name;
        if ($input == true) {
            $execute_cmd .= " < " . $file_input_name;
        }
        $docker_exec_create = json_encode(array(
            "AttachStdin" => false,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "DetachKeys" => "ctrl-p,ctrl-q",
            "WorkingDir" => "/python-judge",
            "Tty" => false,
            "Cmd" =>
                array("/bin/sh", "-c", $execute_cmd)
        ), JSON_UNESCAPED_SLASHES);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $sandbox_ip . "/containers/" . $container_id . "/exec");
        curl_setopt($ch, CURLOPT_PORT, $sandbox_port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $docker_exec_create
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return json_decode($server_output, true)["Id"];
    }

    private function startExecuter($exec_id)
    {
        global $sandbox_ip, $sandbox_port;
        $docker_exec_start = json_encode(array(
            "Detach" => false,
            "Tty" => false
        ), JSON_UNESCAPED_SLASHES);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $sandbox_ip . "/exec/" . $exec_id . "/start");
        curl_setopt($ch, CURLOPT_PORT, $sandbox_port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $docker_exec_start
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }

    private function killContainer($container_id)
    {
        global $sandbox_ip, $sandbox_port;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $sandbox_ip . "/containers/" . $container_id . "/kill");
        curl_setopt($ch, CURLOPT_PORT, $sandbox_port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $server_output;
    }

    private function updatingUserScore($score)
    {
        $error;
        try {
            $stmt = $this->mysql_connection->prepare("UPDATE user_detail SET score + ? WHERE user_id =?");
            $stmt->bind_param("is", $score, $this->user_id);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $ex) {
            $error = $ex;
        }
        return $error;
    }

    private function recordSession($case_result, $total_case)
    {
        $passed_counter = 0;
        $is_session_exist = false;
        $total_score = 0;
        date_default_timezone_set('Asia/Bangkok');
        $date = new DateTime();
        $date_str = $date->format('d/m/Y H:i:s A');
        foreach ($case_result as $result_item) {
            if ($result_item["result"] === "true") {
                $passed_counter += 1;
                $total_score += intval($result_item["score"]);
            }
        }
        $stmt = $this->mysql_connection->prepare("SELECT COUNT(*) FROM exercise_session WHERE " .
            "user_id = ? AND exercise_id = ?");
        $stmt->bind_param("ss", $this->user_id, $_SESSION["selected_exercise"]);
        $stmt->execute();
        $stmt->bind_result($countt);
        while ($stmt->fetch()) {
            if ($countt > 0) {
                $is_session_exist = true;
            }
        }
        $stmt->close();

        $completed_total_score = $total_score + intval($_SESSION["completed_score"]);
        if ($is_session_exist === false) {
            $stmt = $this->mysql_connection->prepare("INSERT INTO exercise_session " .
                "(user_id, exercise_id, passed_case, complete_date, try_date, total_score) VALUES(?, ?, ?, ?, ?, ?)");
            if ($passed_counter === ($total_case - 1)) {
                $stmt->bind_param(
                    "ssssss",
                    $this->user_id,
                    $_SESSION["selected_exercise"],
                    $passed_counter,
                    $date_str,
                    $date_str,
                    $completed_total_score
                );
            } else {
                $datty = "-";
                $stmt->bind_param("ssssss", $this->user_id, $_SESSION["selected_exercise"], $passed_counter, $datty, $date_str, $total_score);
            }
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $this->mysql_connection->prepare("UPDATE exercise_session SET " .
                "try_date=?, complete_date=?, passed_case=?, total_score=? WHERE exercise_id = ? AND user_id = ?");
            if ($passed_counter === ($total_case - 1)) {
                $stmt->bind_param("ssssss", $date_str, $date_str, $passed_counter, $completed_total_score, $_SESSION["selected_exercise"], $this->user_id);
            } else {
                //$test = $total_score;
                $datty = "-";
                $stmt->bind_param("ssssss", $date_str, $datty, $passed_counter, $total_score, $_SESSION["selected_exercise"], $this->user_id);
            }
            $stmt->execute();
            $stmt->close();
            //$this->updatingUserScore($completed_score);
        }
    }

    public function __destruct()
    {
        $this->mysql_connection->close();
        ftp_close($this->con);
    }
}
?>