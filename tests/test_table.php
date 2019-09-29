<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../Connection.php';
require_once __DIR__.'/../Table.php';
require_once __DIR__.'/../QueryBuilder.php';
require_once __DIR__.'/../Exception.php';
require_once __DIR__.'/User.php';

$users = \Moon\Db\tests\User::find()->where("username=?", ['test12312'])->all();
var_dump($users);

$user = \Moon\Db\tests\User::find()->where("username=?", ['test2222'])->first();
var_dump($user);

$user = new Moon\Db\tests\User();
$user->username = 'test12312.'.time();
$user->created_at = date('Y-m-d H:i:s');
$user->updated_at = date('Y-m-d H:i:s');
$res = $user->save();
var_dump($res, $user);

$user->username = 'test2222';
$user->updated_at = date('Y-m-d H:i:s');
$res = $user->save();
var_dump($res, $user);

$res = $user->delete();
var_dump($res, $user);





