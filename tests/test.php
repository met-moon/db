<?php

error_reporting(E_ALL);

require_once '../Connection.php';
require_once '../Table.php';
require_once '../Exception.php';

//method 1
/*$config = [
    'dsn'=>'mysql:host=localhost;dbname=test',
    'username'=>'root',
    'password'=>'root'
];
$db = new \coco\db\Connection($config);*/

//method 2
$db = new \coco\db\Connection();
$db->dsn = 'mysql:host=localhost;dbname=blog';
$db->username = 'root';
$db->password = 'root';

//dump($db);

//

//try{
//    $db->getPdo();
//}catch (Exception $e){
//    echo $e->getMessage();
//}


//$list = $db->fetch("select * from tt_user where id > :id and username=:name limit 1", ['id'=>1, 'name'=>'1212']);
//dump($list);
//echo $db->scalar("select aaa from tt_user where id > :id and username=:name limit 1", ['id'=>1, 'name'=>'1212']);

$user = new \coco\db\Table('tt_user', $db);

dump($user->where('id=?', [103])->fetch());


function dump($vars){
    echo '<pre style="font-size: 14px;font-weight: 500;">';
    foreach(func_get_args() as $var){
        var_dump($var);
    }
    echo '</pre>';
}