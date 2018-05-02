<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
$data_create_container = json_encode(array(
        "AttachStdin" => true,
        "AttachStdout" => true,
        "AttachStderr" => true,
        "OpenStdin" => true,
        "Tty" => true,
        "Cmd" => array("/bin/sh"),
        "Volumes" => array("/python-judge" => json_decode("{}")),
        "Image" => "python-sandbox",
        "NetworkDisabled" => true,
        "StopSignal" => "SIGKILL",
        "HostConfig" => array("Binds" => array("/home/kopai/python-judge:/python-judge"))
), JSON_UNESCAPED_SLASHES);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.6/containers/create");
curl_setopt($ch, CURLOPT_PORT, 2375);
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
$container_id = json_decode($server_output, true)["Id"];
echo "Created this container => " . $container_id . " :3";

$docker_exec_create = json_encode(array(
        "AttachStdin" => false,
        "AttachStdout" => true,
        "AttachStderr" => true,
        "DetachKeys" => "ctrl-p,ctrl-q",
        "Tty" => false,
        "Cmd" =>
                array("/bin/sh; cd python-judge; python test.py")
), JSON_UNESCAPED_SLASHES);
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, "http://192.168.1.6/containers/" . $container_id . "/exec");
curl_setopt($ch2, CURLOPT_PORT, 2375);
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt(
        $ch2,
        CURLOPT_POSTFIELDS,
        $docker_exec_create
);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$server_output2 = curl_exec($ch2);
curl_close($ch2);
print_r($server_output2);
?>