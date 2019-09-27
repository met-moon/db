<?php
/**
 * Date: 2019-09-27
 * Time: 10:23
 */

namespace Moon\Db\tests;


use Moon\Db\Connection;
use Moon\Db\Table;

class User extends Table
{
    protected $tableName = 'user';
    protected $primaryKey = 'id';

    public function getDb(){
        $db = new Connection([
            'dsn'=>'mysql:host=localhost;dbname=test',
            'username'=>'root',
            'password'=>'root123456',
        ]);
        return $db;
    }
}