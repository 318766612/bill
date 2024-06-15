<?php

class SQLite3_Manage
{
    public $conn;

    function __construct($file)
    {
        if (!file_exists($file)) {
            $this->init();
            return;
        }
        $this->conn = new SQLite3($file);
    }

    function __destruct()
    {
        $this->conn = null;
    }

    function init()
    {
        $this->conn = new SQLite3($file);
        // TODO:
    }

    //============start
    function query($sql)
    {
        return $this->conn->query($sql);
    }

    function Execute($sql)
    {
        return $this->query($sql)->fetch();
    }

    function RecordArray($sql)
    {
        return $this->query($sql)->fetchArray();
    }

    //============end
    function changes()
    {
        return $this->conn->changes();
    }

    function query111($sql, $param = null, $memb = null)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;
        if ($param) {
            if (is_array($param)) {
                for ($i = 0; $i < count($param); $i++)
                    $stmt->bindValue($i + 1, $param[$i]);
            } else {
                $stmt->bindValue(1, $param);
            }
        }
        $rs = $stmt->execute();
        if (!$rs) {
            $stmt->close();
            return false;
        }
        $arr = $rs->fetchArray(SQLITE3_NUM);
        $rs->finalize();
        $stmt->close();
        if (!$arr)
            return null;
        if (!$memb)
            return $arr;
        $res = array();
        for ($i = 0; $i < count($memb); $i++) {
            $res[$memb[$i]] = $arr[$i];
        }
        return $res;
    }

    function queryAll($sql, $param = null, $memb = null)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;
        if ($param) {
            if (is_array($param)) {
                for ($i = 0; $i < count($param); $i++)
                    $stmt->bindValue($i + 1, $param[$i]);
            } else {
                $stmt->bindValue(1, $param);
            }
        }
        $rs = $stmt->execute();
        if (!$rs) {
            $stmt->close();
            return false;
        }

        $res = array();
        while ($arr = $rs->fetchArray(SQLITE3_NUM)) {
            if (!$memb) {
                $res[] = $arr;
                continue;
            }
            if (count($memb) == 1 && $memb[0] == null) {
                $res[] = $arr[0];
                continue;
            }
            $it = array();
            for ($i = 0; $i < count($memb); $i++) {
                $it[$memb[$i]] = $arr[$i];
            }
            $res[] = $it;
        }
        $rs->finalize();
        $stmt->close();

        return $res;
    }

    function querySingle($sql, $param = null)
    {
        $res = $this->conn->querySingle($sql, $param);
        if (!$res)
            return false;
        return $res;
    }

    function querySingleAll($sql, $param = null)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;
        if ($param) {
            if (is_array($param)) {
                for ($i = 0; $i < count($param); $i++)
                    $stmt->bindValue($i + 1, $param[$i]);
            } else {
                $stmt->bindValue(1, $param);
            }
        }
        $rs = $stmt->execute();
        if (!$rs) {
            $stmt->close();
            return false;
        }

        $res = array();
        while ($arr = $rs->fetchArray(SQLITE3_NUM)) {
            $res[] = $arr[0];
        }
        $rs->finalize();
        $stmt->close();

        return $res;
    }

    function exec($sql, $param = null)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;
        if ($param) {
            if (is_array($param)) {
                for ($i = 0; $i < count($param); $i++)
                    $stmt->bindValue($i + 1, $param[$i]);
            } else {
                $stmt->bindValue(1, $param);
            }
        }
        $rs = $stmt->execute();
        if ($rs) {
            $res = true;
            $rs->finalize();
        } else {
            $res = false;
        }
        $stmt->close();
        return $res;
    }

    //年支出收入统计
    function getYearShow($_year)
    {
        $sql = "SELECT zhifu,acmoney,actime FROM jz_account;";
        $stmt = $this->queryAll($sql);
        $array = array();
        foreach ($stmt as $data) {
            $typeStr = $data[0];
            if (!array_key_exists($typeStr, $array))
                $array[$typeStr] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $year = date('Y', $data[2]);
            $mouth = date('n', $data[2]);
            //echo "pay year:" . $year . " mouth:" . $mouth;
            if ($_year === $year) {
                $array[$typeStr][$mouth - 1] += $data[1];
            }
        }
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    //年收入,支出查询 1收入 2支出，收入支出是数据库类别
    function getYearAccount($_year, $ctype)
    {
        $sql = "SELECT jz_account_class.classname,acmoney,actime FROM jz_account LEFT JOIN jz_account_class ON jz_account.acclassid = jz_account_class.classid WHERE jz_account.zhifu = '" . $ctype . "';";
        $stmt = $this->queryAll($sql);
        $array = array();
        foreach ($stmt as $data) {
            $typeStr = $data[0];
            if (!array_key_exists($typeStr, $array))
                $array[$typeStr] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $year = date('Y', $data[2]);
            $mouth = date('n', $data[2]);
            if ($_year === $year) {
                $array[$typeStr][$mouth - 1] += $data[1];
            }
        }
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }


    function begin()
    {
        return $this->exec('BEGIN');
    }

    function rollback()
    {
        return $this->exec('ROLLBACK');
    }

    function commit()
    {
        return $this->exec('COMMIT');
    }

    function escapeString($s)
    {
        return $this->conn->escapeString($s);
    }

    //最新插入的id
    function lastInsertRowID()
    {
        return $this->conn->lastInsertRowID();
    }

    function RecordLastID()
    {
        return $this->conn->lastInsertId();
    }

    function lastErrorMsg()
    {
        return $this->conn->lastErrorMsg();
    }

}

?>