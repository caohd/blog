<?php

/**
 * 定义一些函数
 */

require_once('const.php');
//
require_once('config.php');
require_once(APP_DIR.'/core/config.php');

global $sys_config;
global $config;
$_c = array_merge($sys_config, $config);


/**
 * 获得配置项
 * @param string $key
 * @return mixed
 */
function getConfig(string $key)
{
    global $_c;
    return $_c[$key];
}

/**
 * 自动加载函数
 * @author bluedoge
 * @param string $class
 */
function autoload(string $class)
{
    $dirs = getConfig('autoloadDir');
    foreach ($dirs as $dir) {
        $file = $dir.'/'.$class.'.php';
        if (file_exists($file)) {
            include ($file);
            break;
        }
    }
}

/**
 * 权限控制
 * @author
 */
function authentication()
{
    $ref = $_SERVER['HTTP_REFERER'] ?? null;
    if (null == $ref) { // 直接在浏览器中打开.php文件 || cli 模式打开
        header('Location: http://'.DOMAIN_NAME.'/403.html');
        die();
    } else {
        $refInfo = parse_url($ref);
        if ($refInfo['host'] == DOMAIN_NAME) {
            if (hasPermission($refInfo['path'])) {
                // 去吧，勇敢的少年
            } else {
                // 这里不通过设置header来进行重定向是因为这里的请求是通过ajax请求过来的
                // 设置header不起作用
//                echo json_encode([
//                    'status'    => 'error',
//                    'type'      => 403,
//                    'data'      => [],
//                    'msg'       => '权限不足'
//                ]);
                echo packData('fail', 403, [], '权限不足');
                die();
            }
        } else { // 不是来自设定的域名
            // 直接重定向到指定位置
            header('Location: http://'.DOMAIN_NAME.'/403.html');
            die();
        }
    }
}

/**
 * 检查是否有权限继续执行
 * @param string $path 来源的url
 * @return bool
 */
function hasPermission(string $path) : bool
{
    if (isLogin()) {
        if (isSystemManager()) {
            return true;
        } else { // 普通用户
            if (noLogin($path))
                return true;
            if (in_array(Input::getInstance()->post('c').':'.Input::getInstance()->post('a'),
                getConfig('manager'))) {
                return true;
            }
            return false;
        }
    } else {
        return noLogin($path);
    }
}

/**
 * 检查请求的来源是否可以游客浏览
 * @param string $path 来源的url
 * @return bool
 */
function noLogin(string $path)
{
    // 存在nologin配置项里面的或者路径里面没有.html的
    if (in_array($path, getConfig('nologin')) || !preg_match('/\.html/', $path)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 用户是否登录
 * @return bool
 */
function isLogin() : bool
{
    $input = Input::getInstance();
    $user = $input->session('user');
    return !!$user;
}

/**
 * 是否系统管理员
 * @return bool
 */
function isSystemManager() : bool
{
    return User::getCurUser()['type'] === 1;
}



/**
 * @param string $status
 * @param int $code
 * @param array $data
 * @param string $msg
 * @return string
 */
function packData(string $status, int $code, $data = [], $msg = '') :string
{
    return json_encode(
        [
            'status'  => $status,
            'code'    => $code,
            'data'   => $data,
            'msg'     => $msg

        ]);
}



/**
 * 根据id获得某表中某字段的值
 * @param string $id
 * @param string $table
 * @param string $list
 * @return string
 */
function getInfoById($id,$table,$list):string {
    $name='';
    if(!empty($id) and !empty($table) and !empty($list)){
        $db=DB::getInstance(getConfig('db'));
        $db->prepareAndExec("SELECT $list FROM $table WHERE id=:id LIMIT 1",[':id'=>$id]);
        $num=count($result=$db->getResultSet());
        if($num>0){
            $name=$result[0][$list];
        }
    }
    return $name;
}

/**
 * 删除文件
 * @param string $filename 文件名加后缀
 */
function deleteFile($filename)
{
    $_dir='./upload/';
    $file=$_dir.$filename;
    if(file_exists($file)){
        unlink($file);
    }
}

/**
 * 更改用户信息
 * example:
 * if(isLogin){
 *  updateUserBasicInfo();
 * }
 * @return bool
 */
function updateUserBasicInfo():bool {
    $db=DB::getInstance(getConfig('db'));
    $input=Input::getInstance();
    $logo='';//头像
    $upload=new Upload("logo");
    if($upload->isImage()) {
        $logoarr = $upload->upl();//头像
    }
    if(is_array($logoarr)){
        $temp=explode('/',$logoarr[0]);
        for($i=0;$i<count($temp);$i++){
            $logo=$temp[$i];
        }
    }

    $resultcount=0;
    $userid=$input->session('userid');

    $sex=$input->post('sex');//性别
    $brief=$input->post('brief');//个性签名
    $mail=$input->post('mail');//邮箱
    $locationid=$input->post('location');//所在城市id
    $date=$input->post('birthyear').'-'.$input->post('birthmonth').'-'.$input->post('birthday');//生日
    $password=$input->post('password');//密码
    $nickname=$input->post('nickname');//昵称

    if( !empty($date)){
        $db->prepare('UPDATE b_user SET birthday=:birthday WHERE b_user.id=:id')->execute([':birthday'=>$date,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($logo)){
        $filename=getInfoById($userid,"b_user","logo");
        deleteFile($filename);
        $db->prepare('UPDATE b_user SET logo=:logo WHERE b_user.id=:id')->execute([':logo'=>$logo,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($sex)){
        $db->prepare('UPDATE b_user SET sex=:sex WHERE b_user.id=:id')->execute([':sex'=>$sex,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($brief)){
        $db->prepare('UPDATE b_user SET brief=:brief WHERE b_user.id=:id')->execute([':brief'=>$brief,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($mail)){
        $db->prepare('UPDATE b_user SET mail=:mail WHERE b_user.id=:id')->execute([':mail'=>$mail,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($locationid)){
        $db->prepare('UPDATE b_user SET locationid=:locationid WHERE b_user.id=:id')->execute([':locationid'=>$locationid,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($password)){
        $db->prepare('UPDATE b_user SET password=:password WHERE b_user.id=:id')->execute([':password'=>$password,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }

    if(!empty($nickname)){
        $db->prepare('UPDATE b_user SET nickname=:nickname WHERE b_user.id=:id')->execute([':nickname'=>$nickname,':id'=>$userid]);
        $resultcount +=$db->getEffectRow();
    }
    return !!$resultcount;

}
/**
 * 根据字段或表获取信息
 * 若二者为空 返回空数组
 * 若只有表 则返回表的信息数组
 * 若有表和字段 返回该字段信息数组
 * @param string $list  字段
 * @param string $table 表
 * @return array
 */
function separatePages($list='',$table=''):array
{

    $result = [];
    if (!empty($table)) {
        $input = Input::getInstance();
        $page = $input->get('page');//当前页数
        $pagesize = 2;//每页显示几条记录
        $offset = $pagesize * ($page - 1);//偏移量 已显示了几条信息

        /*
         * 查询表中的所有数据 获取表中的记录数$num
         */
        $db = DB::getInstance(getConfig('db'));
        $sqlserch = "SELECT COUNT(id) AS countid FROM $table";
        $db->prepareAndExec($sqlserch, []);
        $num = $db->getResultSet()[0]['countid'];
        /*
         * 获取最终可拆分成多少页
         */
        $pages = intval($num / $pagesize);
        if ($num % $pagesize)
            $pages++;
        if (!empty($list)) {
            $sqlselect = "SELECT $list AS contents FROM $table LIMIT $offset,$pagesize";
        } else {
            $sqlselect = "SELECT * AS contents FROM $table LIMIT $offset,$pagesize";
        }
        $db->prepare($sqlselect)->execute();
        $result = $db->getResultSet();
    }
    return $result;
}