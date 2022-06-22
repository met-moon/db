<?php
/**
 * Date: 2019-09-27
 * Time: 10:23
 */

namespace Moon\Db\tests;

use Moon\Db\Table;

class User extends Table
{
    protected $primaryKey = 'id';

    public static function getDb()
    {
        global $db;
        return $db;
    }

    public static function tableName()
    {
        return 'users';
    }
}