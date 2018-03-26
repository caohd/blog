<?php

class Upload
{
    /**
     * @var array
     */
    private $_files = [];
    private $_dir = '';
    private $_isUni = true;
    private $_exts = [];
    private $_err = [
        UPLOAD_ERR_OK           => '上传成功',
        UPLOAD_ERR_INI_SIZE     => '上传的文件超过了php.ini和uploa_max_filesize的值',
        UPLOAD_ERR_FORM_SIZE    => '上传的文件超过了HTML表单中的MAX_FILE_SIZE的值',
        UPLOAD_ERR_PARTIAL      => '文件只有部分上传',
        UPLOAD_ERR_NO_FILE      => '没有文件背上传',
        UPLOAD_ERR_NO_TMP_DIR   => '找不到临时文件夹',
        UPLOAD_ERR_CANT_WRITE   => '文件写入失败',
        UPLOAD_ERR_EXTENSION    => '上传的文件被PHP扩展程序中断'
    ];
    public function __construct($filename, string $dir = './upload', bool $isUni = true)
    {
        if (is_array($filename)) {
            $this->_files = $filename;
        } else if (is_string($filename)) {
            $this->_files[] = $filename;
        } else {
            //
        }
        $this->setUploadDir($dir);
        $this->setExts();
        $this->setIsUni($isUni);
    }

    /**
     * 设置是否为每个文件设置一个随机的名字
     * @param bool $isUni
     * @param bool $flag 当不生成随机名字的时候是否要覆盖文件
     */
    public function setIsUni(bool $isUni) : void
    {
        $this->_isUni = $isUni;
    }
    /**
     * 设置每个要上传的文件的扩展名
     */
    private function setExts() : void
    {
        foreach ($this->_files as $key => $file) {
            $t = explode('.', $_FILES[$file]['name']);
            $this->_exts[$key] = count($t) >= 2 ? $t[count($t) - 1] : '';
        }
    }
    /**
     * 设置要上传的路径
     * @param string $dir
     */
    public function setUploadDir(string $dir = './upload') : void
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $this->_dir = $dir;
    }
    /**
     *获得上传路径
     * @return string
     */
    public  function getUploadDir():string {
        return $this->_dir;
    }

    /**
     * 检查上传的文件类型
     * @param array $allowMime
     * @param bool $flag 是否继续上传不通过测试的文件
     * @return int 通过测试的个数
     */
    public function checkMime(array $allowMime, bool $flag = true) : int
    {
        $allNum = 0;
        foreach ($this->_files as $key => $file)
        {
            if (!in_array($_FILES[$file]['type'], $allowMime)) {
                if (!$flag) {
                    unset($this->_files[$key]);
                    unset($this->_exts[$key]);
                }
                $allNum ++;
            }
        }
        return $allNum;
    }

    /**
     * 返回上传后的文件的文件名
     * @return array
     */
    public function upl() : array
    {
        $rv = [];
        foreach ($this->_files as $key => $file) {
            if (is_uploaded_file($_FILES[$file]['tmp_name']) && UPLOAD_ERR_OK == $_FILES[$file]['error']) {
                $f = $this->_dir.'/'.($this->_isUni ? $this->getUniName($key).'.'.$this->_exts[$key] : $_FILES[$file]['name']);
                move_uploaded_file($_FILES[$file]['tmp_name'], $f);
                $rv[] = $f;
            }
        }
        return $rv;
    }

    /**
     * @param array $allowExt
     * @param bool $flag 是否继续上传不通过测试的文件
     * @return int 通过测试的个数
     */
    public function checkExt(array $allowExt, bool $flag = true) : int
    {
        $allNum = 0;
        foreach ($this->_files as $key => $file)
        {
            if (!in_array($this->_exts[$key], $allowExt)) {
                if (!$flag) {
                    unset($this->_files[$key]);
                    unset($this->_exts[$key]);
                }
                $allNum ++;
            }
        }
        return $allNum;
    }

    /**
     * 为每一个文件生产一个随机的名字
     * @return string
     */
    public function getUniName(int $index) : string
    {
        $str = substr(md5(microtime(true)), 5, 10);
        if (file_exists($this->_dir.'/'.$str.'.'.$this->_exts[$index]))
            return $this->getUniName($index);
        return $str;
    }

    public function isImage($test = []) : bool
    {
        $rv = true;
        if (empty($test)) {
            $test = $this->_files;
        }
        foreach ($this->_files as $file) {
            if (in_array($file, $test)) {
                $img = getimagesize($_FILES[$file]['tmp_name']);
                if (empty($img) || $img[0] < 0 || $img[1] < 0) {
                    $rv = false;
                }
            }
        }
        return $rv;
    }
}