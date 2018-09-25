<?php 
//DB Config
$mysql_server = "localhost";
$username = "phai";
$password = "P@ssw0rd";
$database = "the_judge";

//Hash Algo config
$salt = "BC3AD9FC78473071321E5A2E55A5BA0C9516B82754C05AEA1AE3EE8C01C2D12B";
$iterations = 1000;
$length = 32;
$algor = "sha256";

//FTP Config
$ftp_host = "172.31.22.247";
$ftp_user = "judge";
$ftp_pass = "P@ssw0rd";
$ftp_timeout = 20000;
$ftp_port = 21;
$ftp_path = "/python-judge/";

//Sandbox server setting
$sandbox_ip = "172.31.22.247";
$sandbox_port = 2375;

?>