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
        /*$file_name = $this->codeUploader($file);
        $container_id = $this->createCodeContainer();
        $container_start_result = $this->startContainer($container_id);
        $exec_id = $this->createExecuter($container_id, $file_name);
        $exec_result = $this->startExecuter($exec_id);

        return $exec_result;*/
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
        $file_name = "testcase-" . $this->student_id . "-" . $this->exercise_id . $input_count . ".txt";
        $file_path = $ftp_path . $file_name;
        $fp = fopen('php://temp', 'r+');
        fwrite($fp, $input);
        rewind($fp);
        ftp_put($con, $file_path, $fp, FTP_ASCII);
        ftp_close($con);
        return $file_name;
    }

    private function startJudge($file)
    {
        global $mysql_server, $username, $password, $database;
        $result = array();
        $connection = new mysqli($mysql_server, $username, $password, $database);
        $stmt = $connection->prepare("SELECT input, output FROM exercise_testcase WHERE exercise_id = ?");
        $stmt->bind_param("s", $this->exercise_id);
        $stmt->execute();
        $stmt->bind_result($case_input, $case_output);
        $case_counter = 1;
        while ($stmt->fetch()) {
            $file_name = $this->codeUploader($file);
            $case_file_name = $this->inputUploader($case_counter, $case_input);
            $container_id = $this->createCodeContainer();
            $this->startContainer($container_id);
            $case_counter++;
        }
        $stmt->close();
    }

    private function createCodeContainer()
    {
        global $sandbox_ip, $sandbox_port;
        $data_create_container = json_encode(array(
            "AttachStdin" => true,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "OpenStdin" => true,
            "Tty" => true,
            "Volumes" => array("/python-judge" => json_decode("{}")),
            "Image" => "python-sandbox",
            "NetworkDisabled" => true,
            "StopSignal" => "SIGKILL",
            "HostConfig" => array("Binds" => array("/python-judge:/python-judge"))
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

    private function createExecuter($container_id, $file_name)
    {
        global $sandbox_ip, $sandbox_port;
        $docker_exec_create = json_encode(array(
            "AttachStdin" => false,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "DetachKeys" => "ctrl-p,ctrl-q",
            "Tty" => false,
            "Cmd" =>
                array("/bin/sh", "-c", "cd python-judge && python " . $file_name)
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

}
?>