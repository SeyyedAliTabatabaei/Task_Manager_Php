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
if(empty($_REQUEST['reqKey'])){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$reqKey = $_REQUEST['reqKey'];


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `key` = '{$reqKey}' LIMIT 1"));
if(!$request_sender_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          return categories based on user type          //
if ($request_sender_user['type'] === "owner"){
    @$getData = mysqli_fetch_all(mysqli_query($MySQLi, "SELECT * FROM `category`"), MYSQLI_ASSOC);
} else if ($request_sender_user['type'] === "admin"){
    @$getData = mysqli_fetch_all(mysqli_query($MySQLi, "SELECT * FROM `category` WHERE `owner` = '{$request_sender_user['username']}'"), MYSQLI_ASSOC);
} else {
    @$getData = mysqli_fetch_all(mysqli_query($MySQLi, "SELECT * FROM category 
    WHERE category.id in 
    (SELECT DISTINCT tasks.category_id FROM tasks INNER JOIN task_users ON tasks.id = task_users.task_id WHERE task_users.username = '{$request_sender_user['username']}')"), MYSQLI_ASSOC);
}


//          show respons          //
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>