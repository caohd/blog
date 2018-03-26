<?php
/**
 * 系统默认配置文件
 */
return $sys_config = [
    // 数据库的配置
    'db'            => [
        'dbtype'    => 'mysql',
        'host'      => '127.0.0.1',
        'user'      => 'root',
        'passwd'    => 'caohd',
        'port'      => 3306,
        'dbname'    => 'test'
    ],
    // 自动加载的目录
    'autoloadDir'   => [
        '.',
        'core',
        'private/pages'
    ],
    'smtp'          => [
        'port'          => 25,
        'timeout'       => 100,
        'logfile'       => '/dev/null',
        'smtpserver'   => '',
        'auth'          => true,
        'host'          => '',
        'user'          => '',
        'password'      => '',
    ],
    // 没有登录就可以直接访问reqEntry.php的文件
    'nologin'       => [
    ],
    // 普通管理员可以执行的方法
    'manager'       => [
    ]
];