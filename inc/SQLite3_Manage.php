<?php
include_once __DIR__ . '/../data/config.php';

class SQLite3_Manage
{
    private static ?SQLite3_Manage $instance = null;
    private SQLite3 $conn;

    function __construct()
    {
        $file = __DIR__ . '/../data/' . DB_NAME;
        $this->conn = new SQLite3($file);
    }

    function __destruct()
    {
        //$this->conn = null;
    }

    public static function getInstance(): ?SQLite3_Manage
    {
        if (self::$instance == null) {
            self::$instance = new SQLite3_Manage();
        }
        return self::$instance;
    }

    public function getConn()
    {
        return $this->conn;
    }

    //============start
    //读取所有数据
    function query($sql): bool|SQLite3Result
    {
        return $this->conn->query($sql);
    }

    //读取一行
    function RecordArray($sql): bool|array
    {
        return $this->query($sql)->fetchArray();
    }

    //============end

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

    function exec($sql, $param = null): int
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt)
                return -1;
            if ($param) {
                $data = json_decode($param, true);
                foreach ($data as $key => $value) {
//                if (strstr($key, "edit-"))
//                    $key = str_replace("edit-", "", $key);
                    if (str_contains($key, 'time'))
                        $value = strtotime($value);
                    if (!empty($value)) {
                        $stmt->bindValue(":" . $key, $value, SQLITE3_TEXT);
                        if (!$stmt) return -2;
                    }
                }
                /*       if (is_array($param)) {
                           for ($i = 0; $i < count($param); $i++)
                               $stmt->bindValue($i + 1, $param[$i]);
                       } else {
                           $stmt->bindValue(1, $param);
                       }*/
            }
            $rs = $stmt->execute();
            if ($rs) {
                $rs->finalize();
                $stmt->close();
                return 1;
            } else {
                $rs->finalize();
                $stmt->close();
                return -3;
            }
        } catch (Exceptione $e) {
            $rs->finalize();
            $stmt->close();
            return 0;
        }
    }

    function api_result($code, $msg, $data = ""): bool|string
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        ob_end_clean();
        ob_end_clean();
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}

?>