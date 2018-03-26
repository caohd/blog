<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/8 0008
 * Time: 16:02
 */
require_once('core/Input.php');
require_once('core/DB.php');
require_once ('private/config.php');
require_once ('private/functions.php');

/*$input=Input::getInstance();


$page=$input->get('page');//当前页数
$pagesize=2;//每页显示几条记录
$offset=$pagesize*($page-1);//偏移量 已显示了几条信息*/

/*
 * 查询表中的所有数据 获取表中的记录数$num
 */
/*$db=DB::getInstance(getConfig('db'));
$sqlserch='SELECT COUNT(id) AS countid FROM b_article';
$db->prepareAndExec($sqlserch,[]);
$num=$db->getResultSet()[0]['countid'];*/

/*
 * 获取最终可拆分成多少页
 */
/*$pages=intval($num/$pagesize);
if($num%$pagesize)
    $pages++;*/

/*
 * 根据偏移量获取查看更多后得到的数据
 */
//$sqlselect="SELECT content AS contents FROM b_article LIMIT $offset,$pagesize";
//$db->prepare($sqlselect)->execute();
//$result=$db->getResultSet();


/*
 *测试
 */

$result=separatePages('title','b_article');
$counts=count($result);
$resulttest='';
if($counts>0) {
    for ($i = 0; $i < $counts; $i++) {
        $resulttest .= $result[$i]['contents'];
    }
}
echo json_encode([
    'status'  => 'success',
    'code'    => 200,
    'data'   => ['result'=>$counts,'num'=>$resulttest],
    'msg'     =>'no msg'
]);

