<?php

namespace Moon\Db;

/**
 * Class Table
 * @package Moon\Db
 */
abstract class Table implements \JsonSerializable, \ArrayAccess
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string|array|null the table's primary key
     */
    protected $primaryKey;

    /**
     * @var array
     */
    protected $attributes = [];

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * @return string|array|null
     */
    public function getPrimaryKey()
    {
//        if (is_null($this->primaryKey)) {
//            $this->primaryKey = $this->getQuery()->getPrimaryKey();
//        }
        //todo query table's primary key
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function jsonSerialize()
    {
        return $this->attributes;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function save()
    {
        $query = $this->getQuery();

        $primaryKey = $this->getPrimaryKey();
        if (is_null($primaryKey)) {  // not has primary key
            return $query->insert($this->tableName, $this->attributes);
        } else if (is_array($primaryKey)) { // primary key is a array
            $where = [];
            $bindVal = [];
            foreach ($primaryKey as $pk) {
                $pkVal = isset($this[$pk]) ? $this[$pk] : null;
                if (!is_null($pkVal)) {
                    $where[] = "`$pk`=:{$pk}";
                    $bindVal[] = $pkVal;
                } else {
                    break;
                }
            }
            if (count($where) == count($primaryKey)) {
                return $query->update($this->tableName, $this->attributes, implode(' and ', $where), $bindVal);
            } else {
                return $query->insert($this->tableName, $this->attributes);
            }
        } else { // primary key is a autoincrement int type //todo test
            $primaryKeyVal = isset($this[$primaryKey]) ? $this[$primaryKey] : null;
            if (is_null($primaryKeyVal)) {
                $res = $query->insert($this->tableName, $this->attributes);
                if ($res) {
                    $this->attributes[$primaryKey] = $query->getDb()->getLastInsertId();
                }
                return $res;
            }
            return $this->getQuery()->update($this->tableName, $this->attributes, "`$primaryKey`=:primaryKey", [':primaryKey' => $primaryKeyVal]);
        }
    }

    //todo
//    public function update(array $setData, $where, array $bindParams = [])
//    {
//        return $this->getQuery()->update($this->tableName, $setData, $where, $bindParams);
//    }
//
//    public function delete($where = '', $bindParams = [])
//    {
//        return $this->getQuery()->delete($this->tableName, $where, $bindParams);
//    }

    /**
     * @return Connection
     */
    abstract public function getDb();

    public function getQuery()
    {
        $query = new QueryBuilder();
        $query->table($this);
        $query->db($this->getDb());
        return $query;
    }

    /**
     * @return QueryBuilder
     */
    public static function find()
    {
        return static::model();
    }

    /**
     * @return QueryBuilder
     */
    public static function model()
    {
        $table = new static();
        return $table->getQuery();
    }
}
