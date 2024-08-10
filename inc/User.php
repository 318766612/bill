<?php
include_once __DIR__ . '/SQLite3_Manage.php';

//用户管理
class User
{
    private SQLite3_Manage $sql_mgr;

    public function __construct()
    {
        $this->sql_mgr = SQLite3_Manage::getInstance();
    }

    public function get_users($uid)
    {
        $res = [];
        $sql = "SELECT * FROM " . table("user") . " WHERE uid = '" . $uid . "'";
        $result = $this->sql_mgr->query($sql);
        while ($row_res = $result->fetchArray(SQLITE3_ASSOC)) {
            $res[] = $row_res;
        }
        return $res;
    }

    public function login($data): void
    {
        $data_json = json_decode($data, true);
        $sql = "SELECT * FROM " . table("user") . " WHERE username = '" . $data_json['username'] . "'";
        echo $sql;
        $row = $this->sql_mgr->RecordArray($sql);
        if ($row) {
            $salt = $row['salt'];
            $password = hash_md5($data_json['password'], $salt);
            if ($row['password'] == $password) {
                if ($row['Isallow'] == "1") {
                    echo $this->sql_mgr->api_result(0, "您的帐号被禁止登录，请联系管理员！");
                } else {
                    $_SESSION['uid'] = $row['uid'];
                    $_SESSION['error_times'] = 0;
                    $userinfo = array("userid" => "$row[uid]", "username" => "$row[username]", "email" => "$row[email]", "add_time" => "$row[add_time]", "update_time" => "$row[update_time]", "isadmin" => "$row[Isadmin]");
                    $userinfo = AES::encrypt($userinfo, sys_key);
                    setcookie("userinfo", $userinfo, time() + 86400 * 3, '/');
//                    $success = "1";
//                    $error_code = "登录成功！";
//                    $gotourl = "add.php";
                    //echo 'uid:'.$_SESSION['uid'];
                    //echo 'userinfo:'.$_COOKIE['userinfo'];
                    echo $this->sql_mgr->api_result(1, "登录成功！", "");
                    //exit();
                }
            } else {
                echo $this->sql_mgr->api_result(0, "用户名或密码错误！");
            }
        } else {
            echo $this->sql_mgr->api_result(0, "用户名或密码错误！");
        }
    }


    public function Update($data): void
    {
        $data_json = json_decode($data, true);
        $uid = $data_json['uid'];
        $sql = "SELECT * FROM " . table("user") . " WHERE uid = '" . $uid . "'";
        $user_data = $this->sql_mgr->RecordArray($sql);
        $salt = $user_data['salt'];
        $password = hash_md5($data_json['password'], $salt);
        if ($user_data['password'] == $password) {
            $newpassword = hash_md5($data_json['newpassword'], $salt);
            $username = $data_json['username'];
            $email = $data_json['email'];
            $update_time = date('now');
            $sql = "UPDATE " . table("user") . " SET username='" . $username . "', password='" . $newpassword . "', email='" . $email . "',update_time='" . $update_time . "' WHERE uid='" . $uid . "';";
            $is_exec = $this->sql_mgr->exec($sql);
            if ($is_exec == 1) {
                echo $this->sql_mgr->api_result(1, '操作成功', '');
                unset($_SESSION['uid']);
                unset($_SESSION['email']);
                unset($_SESSION['pageurl']);
            } else {
                echo $this->sql_mgr->api_result(0, '操作失败', $is_exec);
            }
        }
    }
}