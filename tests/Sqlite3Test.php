<?php
/**
 * User: nano
 * Datetime: 2022/6/22 10:09 下午
 */

namespace Moon\Db\Tests;

use Moon\Db\SqliteConnection;
use PHPUnit\Framework\TestCase;
use PDO;

final class Sqlite3Test extends TestCase
{
    protected function getConnection()
    {
        $config = [
            'dsn' => 'sqlite:/tmp/test.db', //'sqlite::memory:',
            'username' => 'test',
            'password' => 'test',
            'options' => [PDO::ATTR_PERSISTENT => true]
        ];
        return new SqliteConnection($config);
    }

    public function testConnect()
    {
        $conn = $this->getConnection();
        $this->assertInstanceOf(\PDO::class, $conn->getPdo());
    }

    public function testCreateTable()
    {
        $conn = $this->getConnection();

        $table = $conn->scalar("SELECT name FROM sqlite_master WHERE type='table' AND name='company' limit 1");
        if ($table) {
            $res = $conn->execute("DROP TABLE company");
            $this->assertTrue($res !== false);
        }

        $sql = "
CREATE TABLE company(
    id INT PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    age INT NOT NULL,
    address CHAR(50),
    salary REAL
)";
        $res = $conn->execute($sql);
        //var_dump($res);
        $this->assertTrue($res !== false);
    }

    public function testCRUD()
    {
        $conn = $this->getConnection();

        $table = $conn->scalar("SELECT name FROM sqlite_master WHERE type='table' AND name='user' limit 1");
        if (empty($table)) {
            $sql = "
CREATE TABLE user(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL,
    password CHAR(32) not NULL,
    day date NOT NULL DEFAULT (date('now', 'localtime')),
    create_time timestamp NOT NULL DEFAULT (datetime('now', 'localtime')),
    update_time datetime NOT NULL DEFAULT (datetime('now', 'localtime'))
)";
            $res = $conn->execute($sql);
            $this->assertTrue($res !== false);

            $sql = "CREATE INDEX idx_username ON user(username)";
            $res = $conn->execute($sql);
            $this->assertTrue($res !== false);
        }

//        $res = $conn->execute("DROP TABLE user");
//        $this->assertTrue($res !== false);

        $res = $conn->insert('user', [
            'username' => 'test',
            'password' => md5('test'),
        ]);

        $this->assertTrue($res > 0);

        $res = $conn->update('user', [
            'username' => 'test-'.time(),
            'password' => md5('test'.time()),
        ],"id = ?", [1]);

        $this->assertTrue($res > 0);

        $res = $conn->fetchAll("select * from user where id = ?", [1]);
        var_dump($res);
        $this->assertTrue(count($res) == 1);

        $res = $conn->delete('user', "id = :id", ['id'=>1]);

        $this->assertTrue($res > 0);
    }
}