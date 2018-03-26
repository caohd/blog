<?php
/**
 * 在调用php文件之前执行的一些操作
 */

require_once('functions.php');

error_reporting(0);
// 注释自动加载
spl_autoload_register('autoload');
// 权限认证
// 调试阶段可以注释掉
authentication();
// 设置不显示所有的警告|错误
// 调试阶段可以注释掉
//error_reporting(0);