<?php
/**
 * 到时候最外层除了这个php文件之外不保存其他的php文件
 * 作为单一的请求入口
 */

include_once ('private/before.php');

$input = Input::getInstance();
/**
 * $input->post('a') action代表要执行的方法
 * $input->post('c') class代表要执行的方法所在的类
 */
/**
 * eg.
 * $input->post('a')=='read' && $input->post('c') == 'Read';
 * 那么会执行Read::read()
 */
if (null !== ($c = $input->post('c'))) {
    $a = $input->post('a') ?? 'index';
    (new $c())->$a();
}

// 返回的数据格式
//$arr = [
//    'status'    => 'fail|success',
//    'code'      => '0|1|2',
//    'data'      => [],
//    'msg'       => ''
//];