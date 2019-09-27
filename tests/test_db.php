<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../Connection.php';
require_once __DIR__.'/../Table.php';
require_once __DIR__.'/../Exception.php';

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
$db->password = 'root123456';

var_dump($db->getPdo());

$list = $db->fetchAll("select id,title from article where id > :id and status=:status limit 5", [':id'=>100, ':status'=>'publish']);
var_dump($list);

$row = $db->fetch("select * from article where id > :id and status=:status limit 1", ['id'=>100, 'status'=>'publish']);
var_dump($row);

$res = $db->scalar("select title from article where id > :id and status=:status limit 1", ['id'=>100, 'status'=>'publish']);
var_dump($res);


//$user = new \Moon\Db\Table('user', $db);
//
//var_dump($user->where('id=?', [103])->fetch());