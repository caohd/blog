<?php

class WS {
    private static $ser;
    private static $users;
    private static $mysqlPool = [];
    private static $poolLength = 128;
    public function __construct(string $host = '127.0.0.1', int $port = 1380) {
        // create swoole table
        self::$users = new swoole_table(1024);
        self::$users->column('fd', swoole_table::TYPE_INT, 4);
        self::$users->column('username', swoole_table::TYPE_STRING, 32);
        self::$users->column('uid', swoole_table::TYPE_INT, 4);
        self::$users->create();

        // register websocket callback functions
        self::$ser = new swoole_websocket_server($host, $port);
        self::$ser->on('open', array('WS', 'open'));
        self::$ser->on('message', array('WS', 'message'));
        self::$ser->on('close', array('WS', 'close'));
        self::$ser->on('request', array('WS', 'request'));

        // create mysql pool
        for ($i = 0; $i < self::$poolLength; $i ++) {
            self::$mysqlPool[$i] = [0, new MySQLi('120.24.214.209', 'blog', 'blog', 'p_blog')];
            self::$mysqlPool[$i][1]->query('set names utf8');
        }
        echo 'start success';
        self::$ser->start();
    }
    private static function getFreeMySQL($fd)
    {
        for ($i = 0; $i < self::$poolLength; $i ++) {
            if (0 === self::$mysqlPool[$i][0]) {
                self::$mysqlPool[$i][0] = $fd;
                return;
            }
        }
        for ($i = self::$poolLength; $i < 2 * self::$poolLength; $i ++) {
            self::$mysqlPool[$i] = [0, new MySQLi('120.24.214.209', 'blog', 'blog', 'p_blog')];
        }
        self::$mysqlPool[self::$poolLength][0] = $fd;
        self::$poolLength += self::$poolLength;
    }
    private static function freeMySQL($fd) {
        for ($i = 0; $i < self::$poolLength; $i ++) {
            if (self::$mysqlPool[$i][0] === $fd) {
                self::$mysqlPool[$i][0] = 0;
            }
        }
    }
    private static function getMySQLByFd($fd) {
        for ($i = 0; $i < self::$poolLength; $i ++) {
            if (self::$mysqlPool[$i][0] === $fd) {
                return self::$mysqlPool[$i][1];
            }
        }
        return false;
    }
    private static function query($fd, $sql) {
        if ($mysql = self::getMySQLByFd($fd)) {
            $stmt = $mysql->query($sql);
            var_dump($stmt);
        }
    }
    public static function open(swoole_websocket_server $server, swoole_http_request $request)
    {

    }
    public static function message(swoole_websocket_server $server, $frame)
    {
        $rec = json_decode($frame->data, true);
        if (0 === $rec['type']) { // 身份确认
//            self::$users[] = ['fd' => $frame->fd, 'username'=>$rec['username'], 'uid'=>$rec['id']];
            self::$users->set($rec['username'], ['fd' => $frame->fd, 'username'=>$rec['username'], 'uid'=>$rec['id']]);
            self::getFreeMySQL($frame->fd);
//            self::$ser->push($frame->fd, 'connect success, you are'.$rec['username']);
        } else {
            /**
             * $rec = [
             *      'type'      => 1,
             *      'fromid'    => id,
             *      'from'      => username,
             *      'toid'      => id,
             *      'to'        => username,
             *      'content'   => content,
             *      'time'      => timestamp
             * ]
             */
            if (-1 === ($tofd = self::getFd($rec['to']))) {
                // 用户不在线了
                $sql =
                    <<<SQL
                INSERT INTO b_chat (fromid, belongid, toid, content) VALUES 
                ({$rec['fromid']}, {$rec['fromid']}, {$rec['toid']}, '{$rec['content']}'),
                ({$rec['fromid']}, {$rec['toid']}, {$rec['toid']}, '{$rec['content']}')
SQL;
                echo $sql;
                self::query($frame->fd, $sql);


            } else {
                $sql =
                    <<<SQL
                INSERT INTO b_chat (fromid, belongid, toid, content) VALUES 
                ({$rec['fromid']}, {$rec['fromid']}, {$rec['toid']}, '{$rec['content']}'),
                ({$rec['fromid']}, {$rec['toid']}, {$rec['toid']}, '{$rec['content']}')
SQL;
                self::query($frame->fd, $sql);
                echo $sql;
                self::$ser->push($tofd, self::pack($rec['from'], $rec['content']));
            }
        }
    }
    private static function pack($from, $data) {
        return json_encode([
            'from' => $from,
            'data' => $data
        ]);
    }
    public static function close(swoole_websocket_server $server, $fd)
    {
        self::freeMySQL($fd);
    }
    public static function request(swoole_http_request $request, swoole_http_response $response)
    {

    }
    public static function getFd($username)
    {
        $row = self::$users->get($username);
        if ($row)
            return $row['fd'];
        return -1;
    }
    public function __destruct()
    {
        for ($i = 0; $i < self::$poolLength; $i ++) {
            unset(self::$mysqlPool[$i][1]);
        }
    }
}
new WS('0.0.0.0');