<?php
/**
 * Created by PhpStorm.
 * User: ttt
 * Date: 2019/9/26
 * Time: 21:51
 */

namespace Moon\Db;

use Moon\Controller;
use Moon\Db\tests\User;
use PDO;

class QueryBuilder
{
    /**
     * @var Connection
     */
    protected $db;

    /** @var Table */
    protected $table;

    /**
     * @var string the table's alias
     */
    protected $alias;

    /**
     * @var string fields in select query
     */
    protected $fields = '*';

    /**
     * @var array join
     */
    protected $join = [];

    /**
     * @var array union
     */
    protected $union = [];

    /**
     * @var string where conditions
     */
    protected $where;

    /**
     * @var array where bound params
     */
    protected $bindParams = [];

    /**
     * @var string order by
     */
    protected $order;

    /**
     * @var string group by
     */
    protected $group;

    /**
     * @var string having
     */
    protected $having;

    /**
     * @var int|string limit
     */
    protected $limit;

    /**
     * @var int offset
     */
    protected $offset;

    public function db(Connection $db)
    {
        $this->db = $db;
        return $this;
    }

    public function table(Table $table)
    {
        $this->table = $table;
        $this->db($table->getDb());
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * get table's primary key if exists
     * @param string $tableName
     * @return string|array|bool false
     * @throws Exception
     */
    public function getPrimaryKey($tableName)
    {
        $rows = $this->getDb()->fetchAll("SHOW KEYS FROM {$tableName} WHERE `Key_name`='PRIMARY'");
        $rowsCount = count($rows);
        if ($rowsCount == 0) {
            return false;
        } else if ($rowsCount == 1) {
            return $rows[0]['Column_name'];
        } else {
            return array_column($rows, 'Column_name');
        }
    }

    /**
     * get the db connection
     * @param bool $throwException
     * @return Connection|null
     * @throws Exception
     */
    public function getDb($throwException = true)
    {
        if ($throwException && !$this->db instanceof Connection) {
            throw new Exception('Property `db` is not instanceof class `Moon\Db\Connection`');
        }
        return $this->db;
    }

    /**
     * return last execute sql
     * @return string
     * @throws Exception
     */
    public function getLastSql()
    {
        return $this->getDb()->getLastSql();
    }

    /**
     * insert a row
     * @param string $tableName
     * @param array $insertData
     * @return bool false|int lastInsertId
     * @throws Exception
     */
    public function insert($tableName, array $insertData)
    {
        return $this->getDb()->insert($tableName, $insertData);
    }

    /**
     * update
     * @param string $tableName
     * @param array $setData
     * @param string $where
     * @param array $bindParams
     * @return bool false|int affected rows
     */
    public function update($tableName, array $setData, $where, array $bindParams = [])
    {
        return $this->getDb()->update($tableName, $setData, $where, $bindParams);
    }

    /**
     * delete
     * @param string $tableName
     * @param string $where
     * @param array $bindParams
     * @return bool false|int affected rows
     */
    public function delete($tableName, $where = '', $bindParams = [])
    {
        return $this->getDb()->delete($tableName, $where, $bindParams);
    }

    /**
     * select fields
     * @param string $fields
     * @return $this
     */
    public function select($fields = '*')
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * An alias for the table
     * @param string $alias
     * @return $this
     */
    public function alias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * add join
     * @param string $join
     * @return $this
     */
    public function join($join)
    {
        $this->join[] = $join;
        return $this;
    }

    /**
     * add union
     * @param string $union
     * @return $this
     */
    public function union($union)
    {
        $this->union[] = $union;
        return $this;
    }

    /**
     * where condition
     * @param string $where
     * @param array $bindParams
     * @return $this
     */
    public function where($where, array $bindParams = [])
    {
        $this->where = $where;
        $this->bindParams = $bindParams;
        return $this;
    }

    /**
     * limit
     * @param int|string $limit
     * @param int|string $offset
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        if (!is_null($offset)) {
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * offset
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * group by
     * @param string $group
     * @return $this
     */
    public function group($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * having
     * @param string $having
     * @return $this
     */
    public function having($having)
    {
        $this->having = $having;
        return $this;
    }

    /**
     * order by
     * @param string $order
     * @return $this
     */
    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * combine sql
     * @return string
     */
    protected function combineSql()
    {
        $sql = 'SELECT ';

        if (!empty($this->fields)) {
            $sql .= $this->fields;
        }

        $sql .= ' FROM ' . $this->table->tableName();

        if (!empty($this->alias)) {
            $sql .= ' AS ' . $this->alias;
        }

        if (!empty($this->join)) {
            $joinStr = implode(' ', $this->join);
            $sql .= ' ' . $joinStr;
        }

        if (!empty($this->union)) {
            $sql .= ' UNION';
            $unionStr = implode(' UNION ', $this->union);
            $sql .= ' ' . $unionStr;
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->where;
        }

        if (!empty($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;

            if (!empty($this->having)) {
                $sql .= ' HAVING ' . $this->having;
            }
        }

        if (!empty($this->order)) {
            $sql .= ' ORDER BY ' . $this->order;
        }

        if (isset($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if (isset($this->offset)) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * reset attributes
     */
    protected function resetAttr()
    {
        $this->fields = null;
        $this->alias = null;
        $this->join = [];
        $this->where = null;
        $this->bindParams = [];
        $this->group = null;
        $this->having = null;
        $this->order = null;
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * fetch all rows
     * @param int $fetchStyle
     * @return array|bool|mixed
     */
    public function fetchAll($fetchStyle = PDO::FETCH_ASSOC)
    {
        $sql = $this->combineSql();
        $res = $this->getDb()->fetchAll($sql, $this->bindParams, $fetchStyle);
        $this->resetAttr();
        return $res;
    }

    /**
     * fetch first row
     * @param int $fetchStyle
     * @return array|bool
     */
    public function fetch($fetchStyle = PDO::FETCH_ASSOC)
    {
        $sql = $this->combineSql();
        $res = $this->getDb()->fetch($sql, $this->bindParams, $fetchStyle);
        $this->resetAttr();
        return $res;
    }

    /**
     * scalar
     * @param string|null $column
     * @return mixed
     */
    public function scalar($column = null)
    {
        if (!is_null($column)) {
            $res = $this->select($column)->limit(1)->fetch(PDO::FETCH_NUM);
        } else {
            $res = $this->limit(1)->fetch(PDO::FETCH_NUM);
        }

        if ($res !== false) {
            if (isset($res[0])) {
                return $res[0];
            }
        }
        return false;
    }

    /**
     * rows count
     * @param string $column
     * @return bool|int
     */
    public function count($column = '*')
    {
        $res = $this->select("count($column)")->fetch(PDO::FETCH_NUM);
        if ($res !== false) {
            if (isset($res[0])) {
                return $res[0];
            }
        }
        return false;
    }

    public function first()
    {
        $res = $this->fetch();
        if ($res === false) {
            return null;
        }
        $table = $this->getTable();
        $table->setAttributes($res);
        return $table;
    }

    public function all()
    {
        $list = $this->fetchAll();
        $newList = [];
        foreach ($list as $val) {
            $className = get_class($this->getTable());
            $model = new $className;
            $model->setAttributes($val);
            $newList[] = $model;
        }
        return $newList;
    }
}