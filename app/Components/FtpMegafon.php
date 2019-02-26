<?php
/**
 * Created by PhpStorm.
 * User: G_Andreev
 * Date: 28.01.2019
 * Time: 9:25
 */

namespace App\Components;


use Carbon\Carbon;

class FtpMegafon
{
    private $username;
    private $password;
    private $host;
    private $connect;

    public function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
    }

    public function connect(){
        $this->connect = ftp_connect($this->host);
        $login_result = ftp_login($this->connect, $this->username, $this->password);
        if (!$login_result) {
            throw new \Exception("Ошибка логина FTP " . $this->host);
        }
        ftp_pasv($this->connect, true);
    }

    public function getList($directory = false) {
        return ftp_nlist($this->connect, '/recordings/' . $directory . '/' );
    }

    public function getListAll() {
        return ftp_nlist($this->connect, '/recordings/');
    }

    public function getFile($data, $url) {
        $h = fopen('php://temp', 'r+');
        ftp_fget($this->connect, $h, '/recordings/' . $data . '/' . $url, FTP_BINARY, 0);
        $fstats = fstat($h);
        $size = $fstats['size'];
        fseek($h, 0);
        $contents = fread($h, $size);
        fclose($h);
        ftp_close($this->connect);
        return ["content" => $contents, "size" => $size];
    }


}