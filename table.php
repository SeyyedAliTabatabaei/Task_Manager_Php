<?php

include './config.php';
include './functions.php';


$MySQLi = new mysqli('localhost',$DB['username'],$DB['password'],$DB['dbname']);
$MySQLi->query("SET NAMES 'utf8'");
$MySQLi->set_charset('utf8mb4');
if ($MySQLi->connect_error){
echo 'Connection failed: ' . $MySQLi->connect_error;
$MySQLi->close();
die;
}


//          user            //
$query = "CREATE TABLE `user` (
`username` VARCHAR(128) PRIMARY KEY,
`password` VARCHAR(128) DEFAULT NULL,
`type` VARCHAR(128) DEFAULT 'user',
`key` VARCHAR(128) DEFAULT NULL
) default charset = utf8mb4";
if($MySQLi->query($query) === false)
echo $MySQLi->error . PHP_EOL;
$key = genKey();
$owner_password = md5($owner_password);
$MySQLi->query("INSERT INTO `user` VALUES ('{$owner_username}', '{$owner_password}', 'owner', '{$key}')");


//          category            //
$query = "CREATE TABLE `category` (
`id` INT(128) PRIMARY KEY AUTO_INCREMENT,
`title` VARCHAR(256) DEFAULT NULL,
`owner` VARCHAR(128) DEFAULT NULL
) default charset = utf8mb4";
if($MySQLi->query($query) === false)
echo $MySQLi->error . PHP_EOL;


//          tasks            //
$query = "CREATE TABLE `tasks` (
`id` INT(128) PRIMARY KEY AUTO_INCREMENT,
`category_id` INT(128) DEFAULT NULL,
`owner` VARCHAR(128) DEFAULT NULL,
`title` VARCHAR(256) DEFAULT NULL,
`descreption` VARCHAR(256) DEFAULT NULL,
`status` VARCHAR(128) DEFAULT NULL,
`create_date` BIGINT(255) DEFAULT NULL,
`ending_date` BIGINT(255) DEFAULT NULL
) default charset = utf8mb4";
if($MySQLi->query($query) === false)
echo $MySQLi->error . PHP_EOL;


//          task_users            //
$query = "CREATE TABLE `task_users` (
`_` BIGINT(255) PRIMARY KEY AUTO_INCREMENT,
`task_id` INT(128) DEFAULT NULL,
`username` VARCHAR(128) DEFAULT NULL
) default charset = utf8mb4";
if($MySQLi->query($query) === false)
echo $MySQLi->error . PHP_EOL;


$MySQLi->close();
die('Done');
?>