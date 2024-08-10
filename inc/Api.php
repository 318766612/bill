<?php
header('Content-type:text/html;charset=utf-8');
include_once __DIR__ . '/function.php';
$act = $_GET['act'];
$mod = $_GET['mod'];
$data = post('data');
//echo $act . "  " . $mod . "   " . $data;
include __DIR__ . '/' . $act . '.php';

try {
    $act_class = new ReflectionClass($act);
    $act_obj = $act_class->newInstanceArgs();
    if (method_exists($act_obj, $mod)) {
        $method = $act_class->getMethod($mod);
        $method->invoke($act_obj, $data);
    } else {
        //请求方法不存在
        echo '请求方法不存在';
    }
} catch (ReflectionException $e) {
    echo $e->getMessage();
}