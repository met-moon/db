<?php
/**
 * Date: 2019-09-27
 * Time: 10:23
 */

namespace Moon\Db\tests;


use Moon\Db\Table;

class User extends Table
{
    protected $tableName = 'user';
    protected $primaryKey = 'id';
}