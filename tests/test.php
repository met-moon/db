<?php

error_reporting(E_ALL);

require_once '../Connection.php';
require_once '../Table.php';

$config = [
    'dsn'=>'mysql:host=localhost;dbname=test;port=3306;charset=utf8',
    'username'=>'root',
    'password'=>'root'
];
$db = new \coco\db\Connection($config);

dump($db);

//

try{
    $db->getPdo();
}catch (Exception $e){
    echo $e->getMessage();
}



function dump($vars){
    echo '<pre style="font-size: 14px;font-weight: 500;">';
    foreach(func_get_args() as $var){
        var_dump($var);
    }
    echo '</pre>';
}