<?php
include_once __DIR__ . '/SQLite3_Manage.php';

//账单管理
class Account
{
    private SQLite3_Manage $sql_mgr;

    public function __construct()
    {
        $this->sql_mgr = SQLite3_Manage::getInstance();
    }

    public function add($data): void
    {
        $sql = "INSERT INTO " . table('account') . " (money, time, remark, type, category_id, bank_id, uid) VALUES(:money, :time, :remark, :type, :category_id, :bank_id, :uid)";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec == 1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function update($data): void
    {
        $sql = "UPDATE " . table('account') . " SET money=:money, time=:time, remark=:remark, type=:type, category_id=:category_id, bank_id=:bank_id, uid=:uid WHERE acid=:acid";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec == 1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function delete($data): void
    {
        $sql = "DELETE FROM " . TABLE . "account WHERE acid=:acid";
        $is_exec = $this->sql_mgr->exec($sql, $data);
        if ($is_exec == 1) {
            echo $this->sql_mgr->api_result(1, '操作成功', '');
        } else {
            echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
        }
    }

    public function get_account($userid, $s_categoryid, $s_bankid, $s_starttime, $s_endtime, $s_remark, $s_page, $page_num = 50): array
    {
        $sql = "SELECT * FROM " . table("account") . " WHERE uid = " . $userid;
        if (!empty($s_categoryid) && $s_categoryid != "all") {
            $sql = $sql . " AND category_id =" . $s_categoryid;
        }
        if (!empty($s_bankid) && $s_bankid != "all") {
            $sql = $sql . " AND bank_id =" . $s_bankid;
        }
        if (!empty($s_remark)) {
            $sql = $sql . " AND remark LIKE '%" . $s_remark . "%'";
        }
        $category = new Category();
        $category_list = $category->user_category($userid);
        $bank = new Bank();
        $bank_list = $bank->get_bank($userid);
        $result = $this->sql_mgr->query($sql);
        $res_all = [];
        $res = [];
        if ($result) {
            while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
                $data = $row_res;
                $data['time'] = date('Y-m-d', $data["time"]);
                if ($data['time'] >= $s_starttime && $data['time'] <= $s_endtime) {
                    //echo $date. "<br />";
                    $category_id = $data["category_id"];
                    $bank_id = $data["bank_id"];

                    foreach ($category_list as $category_row) {
                        if ($category_id == $category_row['category_id']) {
                            $category_name = $category_row['category_name'];
                            $data["category_name"] = $category_name;
                        }
                    }

                    foreach ($bank_list as $bank_row) {
                        if ($bank_id == $bank_row['bank_id']) {
                            $bank_name = $bank_row['bank_name'];
                            $data["bank_name"] = $bank_name;
                        }
                    }
                    $res_all[] = $data;
                }
            }

            //对数据按照时间排序
            usort($res_all, function ($a, $b) {
                return strtotime($a['time']) - strtotime($b['time']);
            });

            //分页的页码和分页数量 数据
            //$resultArray = [];
            $page_len = $s_page * $page_num;//当前页数需要的数量
            $res_len = count($res_all);//一共有多少条数据
            $page_nums = intval($res_len / $page_num);
            $remainder = $res_len % $page_num;
            if ($remainder > 0)
                $page_nums = $page_nums + 1;

            //定义分页数组
            $res_page = [];
            $res_page["all"] = $res_len;//所有数据长度
            $res_page["pages"] = $page_nums;//所有页数
            $res_page["page"] = $s_page;//当前页
            //当前页数量和数据
            if ($res_len > $page_len) {
                $res_page["page_len"] = $page_len;//当前页共有多少数量
                $res["data"] = array_slice($res_all, $s_page * $page_num - $page_num, $page_num);
            } else {
                $res_page["page_len"] = $res_len;//当前页共有多少数量
                $end_length = $res_len - (($s_page - 1) * $page_num);//不够一页时，当前页的数量
                $res["data"] = array_slice($res_all, $s_page * $page_num - $page_num, $end_length);
            }
            $res["page"] = $res_page;
        }
        return $res;
    }

    //代替加密代码state_day 统计收支
    public function statistics($s_starttime, $s_endtime, $userid, $type)
    {
        $sql = "SELECT * FROM jz_account WHERE uid=" . $userid . " AND type=" . $type;
        $result = $this->sql_mgr->query($sql);
        $money = 0;
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $date = date('Y-m-d', $row_res["time"]);
            if ($date >= $s_starttime && $date <= $s_endtime) {
                $money = $money + $row_res["money"];
            }
        }
        return $money;
    }

    //统计一年的收入支出
    public function count_year($data): void
    {
        $data_json = json_decode($data, true);
        $uid = $data_json['uid'];
        $year = $data_json['year'];
        $sql = "SELECT jz_category.category_name,money,time,jz_account.category_id FROM jz_account LEFT JOIN jz_category ON jz_account.category_id = jz_category.category_id WHERE jz_account.uid = '" . $uid . "';";
        $result = $this->sql_mgr->query($sql);
        $array = array();
        $res_year = array();
        $res_year['收入'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $res_year['支出'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $res_income = array();
        $res_pay = array();
        while ($row_data = $result->fetchArray(SQLITE3_ASSOC)) {
            $row_year = date('Y', $row_data['time']);
            $row_mouth = date('n', $row_data['time']);
            if ($row_year === $year) {
                $category_name = $row_data['category_name'];
                //1 收入  2 支出
                if ($row_data['category_id'] === 1) {
                    $res_year['收入'][$row_mouth - 1] += $row_data['money'];
                    if (!array_key_exists($category_name, $res_income))
                        $res_income[$category_name] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
                    $res_income[$category_name][$row_mouth - 1] += $row_data['money'];
                } else {
                    $res_year['支出'][$row_mouth - 1] += $row_data['money'];
                    if (!array_key_exists($category_name, $res_pay))
                        $res_pay[$category_name] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
                    $res_pay[$category_name][$row_mouth - 1] += $row_data['money'];
                }
            }
        }
        $array['year'] = $res_year;
        $array['income'] = $res_income;
        $array['pay'] = $res_pay;
        echo $this->sql_mgr->api_result(1, '返回成功', $array);
    }


    public function count_bank($data): void
    {
        $data_json = json_decode($data, true);
        $uid = $data_json['uid'];
        $bank_id = $data_json['bank_id'];
        $sql = "SELECT * FROM jz_account WHERE uid=" . $uid . " AND bank_id = '" . $bank_id . "';";
        $result = $this->sql_mgr->query($sql);
        $array = array();
        while ($row_data = $result->fetchArray(SQLITE3_ASSOC)) {
            $remark = $row_data['remark'];
            $remark_lable = explode('-', $remark);
            $key = $remark_lable[1];
            $type = $row_data['type'];
            $money = $row_data['money'];
            if (!array_key_exists($key, $array))
                $array[$key] = array($key, 0, 0, 0);

            $array[$key][$type] += $money;
            $array[$key][3] = $array[$key][1] - $array[$key][2];

        }
        echo $this->sql_mgr->api_result(1, '返回成功', $array);
    }
}