<?php
/**
 * Date: 2020/5/14
 * Time: 6:35 下午
 */

namespace Moon\Db\Tests;

use Moon\Db\Connection;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    private $db_pwd = 'your_pwd';

    private function getConn()
    {
        if (getenv('DB_PWD')) {
            $this->db_pwd = getenv('DB_PWD');
        }

        global $db;

        $db = new Connection([
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => $this->db_pwd,
        ]);
    }

    public function testCRUD()
    {
        $this->getConn();
        $users = User::find()->where("username=?", ['test12312'])->all();
        //var_dump($users);
        $this->assertInstanceOf(User::class, $users[0]);

        $user = User::find()->where("username=?", ['test2222'])->first();
        $this->assertInstanceOf(User::class, $user);

        $user = new User();
        $user->username = 'test12312.' . time();
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $res = $user->save();
        $this->assertTrue($res == 1);

        $user->username = 'test2222';
        $user->updated_at = date('Y-m-d H:i:s');
        $res = $user->save();
        $this->assertTrue($res == 1);

        $res = $user->delete();
        $this->assertTrue($res == 1);
    }
}