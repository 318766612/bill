<?php
header("Content-Type: text/html;charset=utf-8");
if (!is_file("./install/lock") && is_file("./install/index.php")) {
    @header("location:install/index.php");
}
include_once __DIR__ . '/data/config.php';
include_once __DIR__ . '/inc/function.php';
include_once __DIR__ . '/inc/User.php';

$getaction = get("action");//获取参数
//echo "getaction:".$getaction;
if ($getaction == "loginout") {
    unset($_SESSION['uid']);
    unset($_SESSION['email']);
    unset($_SESSION['pageurl']);
    setcookie("userinfo", "", time() - 3600);
    alertgourl("注销成功！", "login.php");
}
if ($getaction == "register" and Multiuser == "1") { // 注册
    $form_name = "reg_form";
    $login_btn = "注册";
    $showlogin_form = showlogin("username") . showlogin("email") . showlogin("password");
    $first_input = "user_name";
} elseif ($getaction == "getpassword") {//找回密码，发邮件
    $form_name = "getpassword_form";
    $login_btn = "发送";
    $showlogin_form = showlogin("email");
    $first_input = "user_email";
} elseif ($getaction == "reset") {//重置密码
    if (empty($_SESSION['email'])) {
        alertgourl("参数非法！", "login.php");
    }
    $form_name = "reset_form";
    $login_btn = "重置";
    $showlogin_form = showlogin("email_session") . showlogin("newpassword");
    $first_input = "newpassword";
} else {//默认
    $form_name = "login_form";
    $login_btn = "登录";
    $getaction = "login";
    $showlogin_form = showlogin("username") . showlogin("password");
    $first_input = "user_name";
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo siteName; ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo SiteURL; ?>img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo SiteURL; ?>img/favicon-32x32.png">
    <script type="text/javascript" src="./js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="./js/load.js" defer></script>
    <link rel="stylesheet" href="css/login.css"/>
</head>

<body>
<div class='login login-itlu-ui'>
    <div id="login">
        <h1><a href="" title="基于PHP多用户记账系统" tabindex="-1">基于PHP多用户记账系统</a></h1>
        <p class="message" style="display:none;">请输入您的用户名或电子邮箱地址。您会收到一封包含创建新密码链接的电子邮件。</p>
        <div id="login_error" style="display:none;"></div>
        <form method="post" name="<?php echo $form_name; ?>" id="<?php echo $form_name; ?>">
            <?php echo $showlogin_form; ?>
            <p class="submit">
                <input type="button" name="itlu-submit" id="itlu-submit" class="button button-primary button-large"
                       value="<?php echo $login_btn; ?>"/>
            </p>
        </form>
    </div>
</div>
<script>
    try {
        document.getElementById('<?php echo $first_input;?>').focus();
    } catch (e) {
    }
    document.onkeydown = function (e) {
        if (!e) e = window.event;
        if ((e.keyCode || e.which) == 13) {
            login_check('#<?php echo $form_name;?>', '<?php echo $getaction;?>');
            return false;
        }
    }

    $("#itlu-submit").click(function () {
        let is_login = login_check('#<?php echo $form_name;?>', '<?php echo $getaction;?>');
        if (is_login) {
            let form_name = document.getElementById('<?php echo $form_name;?>');
            let params = FormatForm(0, form_name);
            Request_Data(GetUrl('User','login'), params, SuccessCallback, FailCallback)
        }
    });

    function SuccessCallback(data) {
        location.href = "add.php";
    }

    function FailCallback(msg) {
        $("#login_error").show();
        $('#login_error').html("<strong>" + msg + "</strong>");
    }

    //登录检查
    function login_check(formname, type) {
        if (type == "login") {
            if (($("#username").val() == "") || ($("#password").val() == "")) {
                alert("用户名、密码不能为空啊！");
                $("#username").focus();
                return false;
            }
            if ($("#password").val().length < 6) {
                alert("密码至少要6位数！");
                $("#password").focus();
                return false;
            }
        }
        if (type == "getpassword") {
            if ($("#user_email").val() == "") {
                alert("邮箱不能为空啊！");
                $("#user_email").focus();
                return false;
            }
        }
        if (type == "register") {
            if (($("#user_name").val() == "") || ($("#user_email").val() == "") || ($("#user_pass").val() == "")) {
                alert("用户名、邮箱、密码不能为空啊！");
                $("#user_name").focus();
                return false;
            }
            if ($("#user_pass").val().length < 6) {
                alert("密码至少要6位数！");
                $("#user_pass").focus();
                return false;
            }
        }
        return true;
    }

</script>
</body>
</html>