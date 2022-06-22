<?php
/**
 * User: nano
 * Datetime: 2022/6/23 12:27 上午
 */

namespace Moon\Db;

use PDO;

interface Connection
{
    public function getPdo();
    public function disconnect();
    public function execute($sql, array $bindParams = []);
    public function fetchAll($sql, array $bindParams = [], $fetchStyle = PDO::FETCH_ASSOC);
    public function fetch($sql, array $bindParams = [], $fetchStyle = PDO::FETCH_ASSOC);
    public function scalar($sql, array $bindParams = []);
    public function insert($tableName, array $insertData = []);
    public function delete($tableName, $where = '', $bindParams = []);
    public function update($tableName, $setData, $where = '', $bindParams = []);
}