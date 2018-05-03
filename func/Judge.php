<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
class Judge
{
    var $student_id, $exercise_id, $exercise_name, $memory_limit, $time_limit, $total_score = 0;
    public function __construct()
    {
        $this->student_id = $_SESSION["stu_id"];
        $this->exercise_id = $_SESSION["selected_exercise"];
        $this->memory_limit = $_SESSION["mem_limit"];
        $this->time_limit = $_SESSION["time_limit"];
        $this->exercise_name = $_SESSION["selected_exercise_name"];
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
                    "output" => $item["output"]
                ));
            } else {
                array_push($testcase_list, array(
                    "input" => "-",
                    "output" => $item["output"]
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
          6. Remove input file if there're any
         */
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
            https://stackoverflow.com/questions/1497885/remove-control-characters-from-php-string
             */
            $result = preg_replace('/[\x00-\x1F\x7F]/', '', $this->startExecuter($exec_id));
            $result = str_replace("Killed","" ,$result);
            if(strlen($result) != 0){
                $converted_res = ($testcase_item["output"] === $result) ? 'true' : 'false';
                array_push($exec_result, array("case_number" => $case_counter, "result" => $converted_res));
            }else{
                array_push($exec_result, array("case_number" => $case_counter, "result" => "T"));
            }
            $case_counter += 1;
        }
        $this->killContainer($container_id);

        //Return result for debug and testing going to submit result page soon!
        return $exec_result;
    }

    private function codeUploader($file_input)
    {
        global $ftp_host, $ftp_port, $ftp_timeout, $ftp_path, $ftp_user, $ftp_pass;
        $file_name = "";
        if ($file_input['name'] <> null) {
            $con = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
            ftp_login($con, $ftp_user, $ftp_pass);
            $file_name = "exercise-" . $this->student_id . "-" . $this->exercise_id . ".py";
            $file_path = $ftp_path . $file_name;
            ftp_put($con, $file_path, $file_input['tmp_name'], FTP_ASCII);
            ftp_close($con);
        } else {
            header("Location: /problem.php");
            die();
        }
        return $file_name;
    }

    private function inputUploader($input_count, $input)
    {
        global $ftp_host, $ftp_port, $ftp_timeout, $ftp_path, $ftp_user, $ftp_pass;
        $file_name = "";
        $con = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
        ftp_login($con, $ftp_user, $ftp_pass);
        $file_name = "testcase-" . $this->student_id . "-" . $this->exercise_id . "-" . $input_count . ".txt";
        $file_path = $ftp_path . $file_name;
        $fp = fopen('php://temp', 'r+');
        fwrite($fp, $input);
        rewind($fp);
        ftp_fput($con, $file_path, $fp, FTP_ASCII);
        ftp_close($con);
        return $file_name;
    }

    private function removeInput($input_list)
    {
        global $ftp_host, $ftp_port, $ftp_timeout, $ftp_path, $ftp_user, $ftp_pass;
        $con = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
        ftp_login($con, $ftp_user, $ftp_pass);
        foreach ($input_list as $file_input) {
            $file_path = $ftp_path . $file_input["file_name"];
            ftp_delete($con, $file_path);
        }
        ftp_close($con);
    }

    private function getCaseInputOutput()
    {
        global $mysql_server, $username, $password, $database;
        $result = array();
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("SELECT input, output FROM exercise_testcase WHERE exercise_id = ?");
        $stmt->bind_param("s", $this->exercise_id);
        $stmt->execute();
        $stmt->bind_result($case_input, $case_output);
        $input_output_counter = 1;
        while ($stmt->fetch()) {
            array_push($result, array("input" => $case_input, "output" => $case_output));
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
                "Memory" => (intval($this->memory_limit) * 1048576),
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
        $execute_cmd = "timeout -t ".$this->time_limit." -s 'SIGKILL' python " . $file_name;
        if ($input == true) {
            $execute_cmd .= " < " . $file_input_name;
        }
        $docker_exec_create = json_encode(array(
            "AttachStdin" => false,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "DetachKeys" => "ctrl-p,ctrl-q",
            "WorkingDir"=>"/python-judge",
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

}
?>