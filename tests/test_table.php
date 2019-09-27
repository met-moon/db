<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../Connection.php';
require_once __DIR__.'/../Table.php';
require_once __DIR__.'/../QueryBuilder.php';
require_once __DIR__.'/../Exception.php';
require_once __DIR__.'/User.php';

$db = new Moon\Db\Connection([
    'dsn'=>'mysql:host=localhost;dbname=test',
    'username'=>'root',
    'password'=>'root123456',
]);

$user = new Moon\Db\tests\User();
$user->username = 'test12312';
$user->created_at = date('Y-m-d H:i:s');
$user->updated_at = date('Y-m-d H:i:s');
$res = $user->save();
var_dump($res, $user);

