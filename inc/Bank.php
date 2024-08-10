<?php
include_once __DIR__ . '/SQLite3_Manage.php';

//账户管理
class Bank
{
    private SQLite3_Manage $sql_mgr;

    public function __construct()
    {
        $this->sql_mgr = SQLite3_Manage::getInstance();
    }
    public function add($data): void
    {
        $date = strtotime("now");
        $sql = "INSERT INTO " . table("bank") . "(bank_name, account, balance_money, add_time, update_time, uid) VALUES(:bank_name, :account, :balance_money, $date, $date, :uid)";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function update($data): void
    {
        $date = strtotime("now");
        $sql = "UPDATE " . table("bank") . " SET bank_name=:bank_name, account=:account, balance_money=:balance_money,update_time=" . $date . " WHERE uid=:uid AND bank_id=:bank_id";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function delete($data): void
    {
        $sql = "DELETE FROM " . table("bank") . " WHERE uid=:uid  AND bank_id=:bank_id";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function get_bank($uid): array
    {
        $sql = "SELECT * FROM " . table("bank") . " WHERE uid = '" . $uid . "' ORDER BY bank_id";
        $result = $this->sql_mgr->query($sql);
        $res = [];
        if ($result) {
            while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
                $res[] = $row_res;
            }
        }
        return $res;
    }


}