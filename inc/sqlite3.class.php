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

    function RecordArray($sql)
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
        $res = false;
        if ($rs) {
            $res = true;
            $rs->finalize();
        } else {
            $res = false;
        }
        $stmt->close();
        return $res;
    }

    //代替加密代码db_list
    function db_list($table_name, $where_str, $order_str)
    {
        $sql = "SELECT * FROM " . TABLE . $table_name . " " . $where_str . " " . $order_str . ";";
        $result = $this->query($sql);//带表头
        $res = [];
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
        return $res;
    }

    //代替加密代码show_type 获取分类列表
    function show_type($pay_type, $userid)
    {
        $sql = "SELECT * FROM " . TABLE . "account_class WHERE classtype=" . $pay_type . " AND ufid=" . $userid . ";";
        $result = $this->query($sql);
        $res = [];
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
        return $res;
    }

    //代替加密代码state_day 统计收支
    function statistics($s_starttime, $s_endtime, $userid, $type)
    {
        $sql = "SELECT * FROM jz_account WHERE jiid=" . $userid . " AND zhifu=" . $type;
        $result = $this->query($sql);
        $money = 0;
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $date = date('Y-m-d', $row_res["actime"]);
            if ($date >= $s_starttime && $date <= $s_endtime) {
                $money = $money + $row_res["acmoney"];
            }
        }
        return $money;
    }


    //代替加密代码itlu_page_search
    function get_page_bill($userid, $s_classid, $s_bankid, $s_starttime, $s_endtime, $s_remark, $s_page, $page_num = 50): array
    {
        $sql = "SELECT * FROM " . TABLE . "account WHERE jiid = " . $userid;
        if (!empty($s_classid) && $s_classid != "all") {
            $sql = $sql . " AND acclassid =" . $s_classid;
        }
        if (!empty($s_bankid) && $s_bankid != "all") {
            $sql = $sql . " AND bankid =" . $s_bankid;
        }
        if (!empty($s_remark)) {
            $sql = $sql . " AND acremark LIKE '%" . $s_remark . "%'";
        }
        //$sql = $sql . " LIMIT " . $page_num . " OFFSET(" . $s_page . "-1)*" . $page_num;
        //echo $sql . "<br />";
        //echo $s_page . "  " . $s_starttime . "  " . $s_endtime . "<br />";
        $result = $this->query($sql);
        $res = [];
        $types = $this->get_account_type();//账单分类列表
        $banks = $this->get_bank();//账户列表

        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row_res;
            $date = date('Y-m-d', $data["actime"]);
            if ($date >= $s_starttime && $date <= $s_endtime) {
                //echo $date. "<br />";
                $type_id = $data["acclassid"];
                $bank_id = $data["bankid"];
                foreach ($types as $value) {
                    $types_id = $value["classid"];
                    if ($type_id == $types_id) {
                        $data["classname"] = $value["classname"];
                    }
                }
                foreach ($banks as $value) {
                    $banks_id = $value["bankid"];
                    if ($bank_id == $banks_id) {
                        $data["bankname"] = $value["bankname"];
                    }
                }
                $res[] = $data;
            }
        }
        $resultArray = [];
        $res_len = count($res);
        $page_len = $s_page * $page_num;//页数需要的数量
        //
        $res_page = [];
        $res_page["all"] = $res_len;//所有数据长度
        $page_nums = intval($res_len / $page_num);
        $remainder = $res_len % $page_num;
        if ($remainder > 0)
            $page_nums = $page_nums + 1;
        $res_page["pages"] = $page_nums;//所有页数
        $res_page["page"] = $s_page;//当前页

        if ($res_len > $page_len) {
            $res_page["page_len"] = $page_len;//当前页共有多少数量
            $resultArray["data"] = array_slice($res, $s_page * $page_num - $page_num, $page_num);
        } else {
            $res_page["page_len"] = $res_len;//当前页共有多少数量
            $end_length = $res_len - (($s_page - 1) * $page_num);//不够一页时，当前页的数量
            $resultArray["data"] = array_slice($res, $s_page * $page_num - $page_num, $end_length);
        }
        $resultArray["page"] = $res_page;
        //var_dump($resultArray);
        return $resultArray;
    }

    //获取账单分类列表
    function get_account_type(): array
    {
        $sql = "SELECT * FROM jz_account_class";
        $result = $this->query($sql);
        $res = [];
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
        return $res;
    }

    //获取账户列表
    function get_bank(): array
    {
        $sql = "SELECT * FROM jz_bank";
        $result = $this->query($sql);
        $res = [];
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
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