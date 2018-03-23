<?php
/**
 * Created by PhpStorm.
 * User: ttt
 * Date: 2018/3/23
 * Time: 11:12
 */
require_once '../Connection.php';
require_once '../Table.php';
require_once '../Exception.php';

$db = new \Moon\Db\Connection([
    'dsn'=>'mysql:host=localhost;dbname=test',
    'username'=>'root',
    'password'=>'root',
    'tablePrefix'=>'z_'
]);


$row = $db->fetchAll('select * from {{area}} where area_id > ? limit 2', [3]);
var_dump($row);
echo $db->getLastSql();