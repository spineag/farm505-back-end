<?php

class SN_NotificationModel
{

    // $keyAndValue =  array("id_notif"=>"11", "message"=>"ss")
    protected $_tableName = "dict_notification";
    protected $_db;

    function __construct()
    {
        $this->_db = Application::getInstance()->getMainDb();
    }

    /**
      *  save the object
     */
    public function save($keyAndValue)
    {

        $keyArray = array_keys($keyAndValue);
        $valueArray = array_values($keyAndValue);
        $upd_values = '';

        for ($i=1; $i<count($keyArray); $i++)
        {
            $upd_values = $upd_values.$keyArray[$i]."='".$valueArray[$i]."'";
            if ($i+1 == count($keyArray))
            {
                $upd_values.=';';
            }
            else
            {
                $upd_values.=',';
            }
        }

        $query = "INSERT INTO ".$this->_tableName."(".implode(", ", $keyArray).") VALUES ('".implode("', '", $valueArray)."')
                  ON DUPLICATE KEY UPDATE ".$upd_values;
        $this->_db->query($query);
    }

    /**
        * delete object by
        * @param type $primaryKeyValue
        */
    public function delete($primaryKeyValue)
    {

        $query = sprintf("DELETE FROM %s WHERE id_notif = '%d'",
            $this->_tableName, (int)$primaryKeyValue);
        $this->_db->query($query);

    }

    /**
        * get set of objects
        *
        * @param type $offset
        * @param type $rowCount
        */
    public function fetch($offset = 0, $rowCount = 0)
    {
        $query = sprintf("SELECT * FROM %s ORDER BY active DESC, type DESC, date_end DESC", $this->_tableName);

        if ($offset > 0 || $rowCount > 0)
        {
            $query .= sprintf(" LIMIT '%d','%d'", (int)$offset, (int)$rowCount);
        }

        $res = $this->_db->query($query);
        while ($row = $res->fetch())
        {
            $rows[] = $row;
        }

        return $rows;

    }

    public function fetchByKey($primaryKeyValue)
    {
        $query = sprintf("SELECT * FROM %s WHERE id_notif = '%d'",
            $this->_tableName, (int)$primaryKeyValue);
        $res = $this->_db->query($query);
        $row = $res->fetch();

        return $row;
    }

    /**
     * get objects count
       */
    public function getCount()
    {
        $count = 0;
        $res = $this->_db->query("SELECT COUNT(*) as cnt FROM ".$this->_tableName);
        if($row = $res->fetch())
        {
            $count = $row['cnt'];
        }

        return $count;
    }
}
