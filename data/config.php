<?php
ob_start();
session_start();
//基本设置
date_default_timezone_set("Asia/Shanghai");

// 检查PHP版本
if(PHP_VERSION<8.0){
	die("PHP版本小于8.0，请升级！");
}
define("siteName","账单");
define("SiteURL","http://test.com/bill/");
define("Multiuser","1");/*是否开启多用户，1为开启，0为禁用*/
// database
define("DB_NAME","jizhang.db");
define("TABLE","jz_");
define("VERSION","3.0.0");