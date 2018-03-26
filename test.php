<?php

require_once('private/functions.php');
// 注释自动加载
spl_autoload_register('autoload');
$db = DB::getInstance(getConfig('db'));
//$db->prepareAndExec("SELECT * FROM b_user WHERE username=? LIMIT 1", [$user]);
$db->prepareAndExec('show tables', []);
$res = $db->getResultSet();
print_r($res);
for ($i = 0; $i < count($res); $i ++)
$db->prepareAndExec("DELETE FROM `{$res[$i]['Tables_in_p_blog']}` WHERE 1=1", []);
echo $db->getEffectRow();
print_r($db->getResultSet()[0] ?? []);

//$s1 = 'article2';
//echo substr($s1, strlen('article'));