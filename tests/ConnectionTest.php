<?php
/**
 * Date: 2020/5/14
 * Time: 4:42 下午
 */

namespace Moon\Db\Tests;

use Moon\Db\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @covers
 */
final class ConnectionTest extends TestCase
{
    private $db_pwd = 'your_pwd';

    private function getPwd()
    {
        if (getenv('DB_PWD')) {
            $this->db_pwd = getenv('DB_PWD');
        }
        return $this->db_pwd;
    }

    public function testConnect()
    {
        $conn = new Connection([
            'password' => $this->getPwd()
        ], [[
            'password' => $this->getPwd()
        ]]);

        $this->assertInstanceOf(Connection::class, $conn);
        $this->assertInstanceOf(\PDO::class, $conn->getPdo());
        $this->assertInstanceOf(\PDO::class, $conn->getReadPdo());
    }

    public function testSelect()
    {
        $conn = new Connection([
            'password' => $this->getPwd()
        ], [[
            'password' => $this->getPwd()
        ]]);

        $tables = $conn->fetchAll('show tables');
        //var_dump($tables);
        $this->assertTrue(count($tables) > 0);

        $row = $conn->fetch('select * from user where id=:id', ['id' => 1]);
        $this->assertEquals(1, $row['id']);

        $id = $conn->scalar("select id from user where id > :id limit 1", ['id' => 1]);
        $this->assertTrue($id > 1);

        $rows = $conn->fetchAll('select * from user where id > ?', [1]);
        $this->assertNotEquals(1, $rows[0]['id']);

        $lastSql = $conn->getLastSql();
        //var_dump($lastSql);
        $this->assertEquals("select * from user where id > '1'", $lastSql);
    }

    public function testCUD()
    {
        $conn = new Connection([
            'password' => $this->getPwd()
        ], [[
            'password' => $this->getPwd()
        ]]);

        $now = date('Y-m-d H:i:s');
        $res = $conn->insert('user', ['username' => 'user_test', 'created_at' => $now, 'updated_at' => $now]);
        //var_dump($res);
        $this->assertTrue($res == 1);

        $id = $conn->getLastInsertId();
        //var_dump($id);
        $this->assertTrue(intval($id) > 0);

        $res = $conn->update('user', ['username' => 'test_user'], 'username=?', ['user_test']);
        $this->assertTrue($res >= 1);

        $res = $conn->delete('user', 'username=?', ['test_user']);
        $this->assertTrue($res >= 1);
    }

    public function testDisconnect()
    {
        $conn = new Connection([
            'password' => $this->getPwd()
        ], [[
            'password' => $this->getPwd()
        ]]);

        $pdo = $conn->getPdo();
        $readPdo = $conn->getReadPdo();
        //var_dump($pdo);
        $conn->disconnect();

        $r = new \ReflectionObject($conn);

        $p1 = $r->getProperty('pdo');
        $p1->setAccessible(true);
        $p2 = $r->getProperty('readPdo');
        $p2->setAccessible(true);
        $this->assertNull($p1->getValue($conn));
        $this->assertNull($p2->getValue($conn));

        $this->assertFalse($pdo === $conn->getPdo());
        $this->assertFalse($readPdo === $conn->getReadPdo());
    }
}