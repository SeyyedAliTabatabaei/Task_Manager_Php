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
if(empty($_REQUEST['category_id']) or empty($_REQUEST['title']) or empty($_REQUEST['descreption']) 
    or empty($_REQUEST['status']) or empty($_REQUEST['ending_time_stamp']) 
        or empty($_REQUEST['users']) or empty($_REQUEST['reqKey']))
        {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
            $MySQLi->close();
            die();
        }
$category_id = $_REQUEST['category_id'];
$title = $_REQUEST['title'];
$descreption = $_REQUEST['descreption'];
$status = $_REQUEST['status'];
$ending_time_stamp = $_REQUEST['ending_time_stamp'];
$users = json_decode($_REQUEST['users']);
$reqKey = $_REQUEST['reqKey'];


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `key` = '{$reqKey}' LIMIT 1"));
if(!$request_sender_user or $request_sender_user['type'] == 'user'){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$owner = $request_sender_user['username'];


//          check category availability          //
@$isSet = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `category` WHERE `id` = '{$category_id}' LIMIT 1"));
if(!$isSet){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'دسته بندی موردنظر یافت نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$create_date = time();

//          insert task into DB          //
$MySQLi->query("INSERT INTO `tasks` (`category_id`,`owner`,`title`,`descreption`,`status`,`create_date`,`ending_date`) 
    VALUES ('{$category_id}','{$owner}','{$title}','{$descreption}','{$status}','{$create_date}','{$ending_time_stamp}')");
$task_id = mysqli_insert_id($MySQLi);


//          import users into task         //
foreach($users as $user)
    $MySQLi->query("INSERT INTO `task_users` (`task_id`,`username`) VALUES ('{$task_id}','{$user}')");


//          show respons          //
@$getData = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `tasks` WHERE `id` = '{$task_id}' LIMIT 1"));
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>