<?php

include '../../config.php';
include '../../functions.php';


$MySQLi = new mysqli('localhost',$DB['username'],$DB['password'],$DB['dbname']);
$MySQLi->query("SET NAMES 'utf8'");
$MySQLi->set_charset('utf8mb4');
if ($MySQLi->connect_error){
    echo 'Connection failed: ' . $MySQLi->connect_error;
    $MySQLi->close();
    die;
}


//          post method denied          //
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(304);
    echo json_encode(['status' => 'error', 'message' => 'درخواست شما باید از طریق POST ارسال شود.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check all parameters          //
if(empty($_REQUEST['task_id']) or empty($_REQUEST['reqKey'])){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$task_id = $_REQUEST['task_id'];
$reqKey = $_REQUEST['reqKey'];


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `key` = '{$reqKey}' LIMIT 1"));
if(!$request_sender_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check task availability          //
@$getTask = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `tasks` WHERE `id` = '{$task_id}' LIMIT 1"));
if(!$getTask){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'فعالیت موردنظر یافت نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check that this user is in task          //
if(!mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `task_users` WHERE `username` = '{$request_sender_user['username']}' AND `task_id` = '{$task_id}' LIMIT 1"))){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'شما دسترسی لازم برای این کار را ندارید.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check descreption editing parameter          //
if(!empty($_REQUEST['descreption'])){
    $MySQLi->query("UPDATE `tasks` SET `descreption` = '{$_REQUEST['descreption']}' WHERE `id` = '{$task_id}' LIMIT 1");
}


//          check status editing parameter          //
if(!empty($_REQUEST['status'])){
    $MySQLi->query("UPDATE `tasks` SET `status` = '{$_REQUEST['status']}' WHERE `id` = '{$task_id}' LIMIT 1");
}


//          show respons          //
@$getData = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `tasks` WHERE `id` = '{$task_id}' LIMIT 1"));
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>