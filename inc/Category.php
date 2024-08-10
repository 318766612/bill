<?php
include_once __DIR__ . '/SQLite3_Manage.php';

//分类管理
class Category
{
    private SQLite3_Manage $sql_mgr;

    public function __construct()
    {
        $this->sql_mgr = SQLite3_Manage::getInstance();
    }

    public function add($data): void
    {
        $sql = "INSERT INTO " . table("category") . "(category_name, type, uid) VALUES(:category_name, :type, :uid)";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }
    public function update($data): void
    {
        $sql = "UPDATE " . table("category")  . " SET category_name=:category_name, type=:type WHERE category_id=:category_id";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function delete($data): void
    {
        $sql = "DELETE FROM " .  table("category")  . " WHERE category_id=:category_id";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec==1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }


    public function user_category($uid): array
    {
        $res = [];
        $sql = "SELECT * FROM " . table("category") . " WHERE uid=".$uid;
        $result = $this->sql_mgr->query($sql);
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
        return $res;
    }

    //根据支付类型获取分类信息
    public function type_category($uid, $type): array
    {
        $sql = "SELECT * FROM " . table("category") . " WHERE type=".$type." AND uid=".$uid;
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