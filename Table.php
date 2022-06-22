<?php

namespace Moon\Db;

/**
 * Class Table
 * @package Moon\Db
 */
abstract class Table implements \JsonSerializable, \ArrayAccess
{
    /**
     * @var string|array|null the table's primary key
     */
    protected $primaryKey;

    protected $primaryKeyAutoIncrement = true;

    public $isCreated = false;
    public $isDeleted = false;

    protected $attributes = [];

    protected $updateAttributes = [];

    /**
     * @return string
     */
    abstract static public function tableName();

    /**
     * @return Connection
     */
    abstract static public function getDb();

    public function setAttributes($attributes)
    {
        if ($this->isCreated) {
            $this->updateAttributes = array_merge($this->updateAttributes, $attributes);
        }
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    public function setAllAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setAttribute($attribute, $value)
    {
        if ($this->isCreated) {
            $this->updateAttributes[$attribute] = $value;
        }
        $this->attributes[$attribute] = $value;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function __set($name, $value)
    {
        if ($this->isCreated) {
            $this->updateAttributes[$name] = $value;
        }
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->attributes;
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    protected function getUniqueWhereParams($action)
    {
        $primaryKey = $this->getPrimaryKey();
        if (is_null($primaryKey)) {  // not has primary key
            throw new Exception('Unable to ' . $action . ' table "' . static::tableName() . '",because the table does not have a primary key.');   //todo get full table name
        } else if (is_array($primaryKey)) { // primary key is a array
            $where = [];
            $bindVal = [];
            $emptyFields = [];
            foreach ($primaryKey as $pk) {
                $pkVal = isset($this[$pk]) ? $this[$pk] : null;
                if (!is_null($pkVal)) {
                    $where[] = "`$pk`=:{$pk}";
                    $bindVal[] = $pkVal;
                } else {
                    //break;
                    $emptyFields[] = $pk;
                }
            }
            if (!empty($emptyFields)) {
                throw new Exception('Primary key "' . implode('","', $emptyFields) . '" can not be null.');
            }
            return [
                'where' => implode(' AND ', $where),
                'params' => $bindVal
            ];
        } else {
            $primaryKeyVal = isset($this[$primaryKey]) ? $this[$primaryKey] : null;
            if (is_null($primaryKeyVal)) {
                throw new Exception('Primary key "' . $primaryKey . '" can not be null.');
            }
            return [
                'where' => "`$primaryKey`=:primaryKey",
                'params' => [':primaryKey' => $primaryKeyVal]
            ];
        }
    }

    /**
     * save
     * @return int|bool false affected rows
     * @throws Exception
     */
    public function save()
    {
        //var_dump('isCreated.'.var_export($this->isCreated, 1));
        $query = $this->getQuery();

        if ($this->isCreated) {
            $whereParams = $this->getUniqueWhereParams('update');
            $res = $query->update(static::tableName(), $this->updateAttributes, $whereParams['where'], $whereParams['params']);
            if ($res) {
                $this->updateAttributes = [];
            }
            return $res;
        }
        $res = $query->insert(static::tableName(), $this->attributes);
        if ($res) {
            if ($this->primaryKeyAutoIncrement) {
                $autoIncrementId = $query->getDb()->getLastInsertId();
                $this->setAttribute($this->primaryKey, $autoIncrementId);
            }
            $this->isCreated = true;
        }
        return $res;
    }

    /**
     * delete
     * @return int|bool false affected rows
     * @throws Exception
     */
    public function delete()
    {
        $query = $this->getQuery();
        $whereParams = $this->getUniqueWhereParams('delete');
        $res = $query->delete(static::tableName(), $whereParams['where'], $whereParams['params']);
        if ($res) {
            $this->isDeleted = true;
            $this->isCreated = false;
            $this->attributes = $this->updateAttributes = [];
        }
        return $res;
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

    public function getQuery()
    {
        $query = new QueryBuilder();
        $query->table($this);
        $query->db(static::getDb());
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
