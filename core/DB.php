<?php

/*
 * @author caohd
 */
class DB
{
    const ERR_NEW_INSTANCE = 1;
    const ERR_EXEC_TRAN = 2;
    private static $_instance = null;

    private $_config = [
        'dbtype'    => 'mysql',
        'host'      => '127.0.0.1',
        'user'      => 'root',
        'passwd'    => '',
        'port'      => 3306,
        'dbname'    => ''
    ];
    /**
     * @var null | PDO
     */
    private $_pdo = null;
    /**
     * @var null | PDOStatement
     */
    private $_pdoStat = null;
    /**
     * 这个只有在getInstance()的时候才会被设置
     * @var null | PDOException
     */
    private $_err = null;
    /**
     * @var int
     * 0 正常
     * 1 实例化PDO失败
     * 2 执行事物失败
     */
    private $_errCode = 0;
    /**
     *
     * @var array
     */
    private $_resultSet = [];
    private $_transactEffectRows = [];
    private function __construct($config = [])
    {
        $this->_errCode = 0;
        $this->_config = array_merge($this->_config, $config);
        $dsn = "{$this->_config['dbtype']}:host={$this->_config['host']};dbname={$this->_config['dbname']}";
        try {
            $this->_pdo = new PDO($dsn, $this->_config['user'], $this->_config['passwd']);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $this->_pdo->query('set names utf8');
        } catch (PDOException $e) {
            $this->_errCode = DB::ERR_NEW_INSTANCE;
            $this->_err = $e;
        }
    }

    /**
     * 获得数据库单例
     * @param array $config 数据库的配置数组
     * @return DB DB类的单例
     */
    public static function getInstance(array $config = []) : DB
    {
        if (null === self::$_instance)
            self::$_instance = new self($config);

        return self::$_instance;
    }

    /**
     * 预执行一条SQL语句
     * @param string $sql
     * @return DB
     */
    public function prepare(string $sql) : DB
    {
        // PDO对象prepare()失败会返回false，但是调用者不应该产生false
        $this->_pdoStat = $this->_pdo->prepare($sql);
        return $this;
    }

    /**
     * 执行一个SQL语句
     * @param array $params
     */
    public function execute(array $params = [])
    {
        // 清除上一次的查询结果
        $this->freeLastResultSet();
        $this->_pdoStat->execute($params);
    }

    /**
     * 准备并执行
     * @param string $sql
     * @param array $params
     */
    public function prepareAndExec(string $sql, array $params)
    {
        $this->prepare($sql)->execute($params);
    }

    /**
     * 获得上一次select|show查询的结果集
     * @return array
     */
    public function getResultSet() : array
    {
        if (empty($this->_resultSet))
            while ($row = $this->_pdoStat->fetch(PDO::FETCH_ASSOC)) {
                $this->_resultSet[] = $row;
            }
        return $this->_resultSet;
    }

    /**
     * 在连续执行了查询的时候，为了不让后来的结果影响到当前的结果集，应该先执行这个函数
     */
    private function freeLastResultSet()
    {
        $this->_resultSet = [];
    }
    /**
     * 执行一个事务
     * @param array $sqls 要执行的SQL语句s
     * @return bool
     */
    public function execTransaction(array $sqls) : bool
    {
        $this->_transactEffectRows = [];

        $this->_pdo->beginTransaction();
        foreach ($sqls as &$sql) {
            $execRst = $this->_pdo->exec($sql);
            if (false === $execRst) { // exec error
                $this->_errCode = DB::ERR_EXEC_TRAN;
                $this->_pdo->rollBack();
                return false;
            } else { // exec success
                $this->_transactEffectRows[] = $execRst;
            }
        }
        $this->_pdo->commit();
        return true;
    }


    /**
     * 返回上一个事务中每一条SQL语句的受影响条数
     * @return array
     */
    public function getTransactEffectRows(): array
    {
        return $this->_transactEffectRows;
    }

    /**
     * @return PDOException | array
     */
    public function getErrInfo()
    {
        if (null === $this->_err)
            return $this->_pdo->errorInfo();
        return $this->_err;
    }

    /**
     * 获得上一个插入操作的最后的id
     * @return string
     */
    public function lastInsertId()
    {
        return $this->_pdo->lastInsertId();
    }

    /**
     * 获得受影响的条数
     * @return int 受影响的条数
     */
    public function getEffectRow() {
        return $this->_pdoStat->rowCount();
    }
    /**
     * @return int
     */
    public function getErrCode()
    {
        return $this->_errCode;
    }
}
