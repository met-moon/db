<?php
ini_set('display_errors', 'On');
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
$db = new \Moon\Db\Connection();
$db->dsn = 'mysql:host=localhost;dbname=test';
$db->username = 'root';
$db->password = 'root';

echo '<pre>';

var_dump($db);

//

//try{
//    $db->getPdo();
//}catch (Exception $e){
//    echo $e->getMessage();
//}


//$list = $db->fetch("select * from tt_user where id > :id and username=:name limit 1", ['id'=>1, 'name'=>'1212']);
//dump($list);
//echo $db->scalar("select aaa from tt_user where id > :id and username=:name limit 1", ['id'=>1, 'name'=>'1212']);

$user = new \Moon\Db\Table('user', $db);

var_dump($user->where('id=?', [103])->fetch());