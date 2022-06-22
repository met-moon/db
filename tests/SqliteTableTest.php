<?php
/**
 * Date: 2020/5/14
 * Time: 6:35 下午
 */

namespace Moon\Db\Tests;

use Moon\Db\MysqlConnection;
use Moon\Db\SqliteConnection;
use PDO;
use PHPUnit\Framework\TestCase;

final class SqliteTableTest extends TestCase
{
    private function getConn()
    {
        global $db;
        $config = [
            'dsn' => 'sqlite:/tmp/test.db', //'sqlite::memory:',
            'options' => [PDO::ATTR_PERSISTENT => true]
        ];
        $db = new SqliteConnection($config);
        //var_dump($db);

        $conn = $db;

        $table = $conn->scalar("SELECT name FROM sqlite_master WHERE type='table' AND name='users' limit 1");
        if (empty($table)) {
            $sql = "
CREATE TABLE users(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL,
    password CHAR(32) not NULL,
    day date NOT NULL DEFAULT (date('now', 'localtime')),
    create_time timestamp NOT NULL DEFAULT (datetime('now', 'localtime')),
    update_time datetime NOT NULL DEFAULT (datetime('now', 'localtime'))
)";
            $res = $conn->execute($sql);
            //$this->assertTrue($res !== false);

            //$sql = "CREATE INDEX idx_username ON users(username)";
//            $res = $conn->execute($sql);
            //$this->assertTrue($res !== false);
        }
    }

    public function testCRUD()
    {
        $this->getConn();
        $users = User::find()->where("username =?", ['test2222'])->all();
        var_dump($users);
        if($users){
            $this->assertInstanceOf(User::class, $users[0]);

            $user = User::find()->where("username=?", ['test2222'])->first();
            var_dump($user);
            $this->assertInstanceOf(User::class, $user);
        }

        $user = new User();
        $user->username = 'test12312.' . time();
        $user->password = md5(time());
        $user->create_time = date('Y-m-d H:i:s');
        $user->update_time = date('Y-m-d H:i:s');
        $res = $user->save();
        $this->assertTrue($res == 1);

        $user->username = 'test2222';
        $user->update_time = date('Y-m-d H:i:s');
        $res = $user->save();
        $this->assertTrue($res == 1);

        $res = $user->delete();
        $this->assertTrue($res == 1);
    }
}