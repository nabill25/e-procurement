<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Include phpseclib files
require_once(APPPATH . 'libraries/phpseclib/Net/Net_SFTP.php');

class Sftp
{
    private $sftp;
    private $connected = false;

    /**
     * Koneksi ke server SFTP
     */
    public function connect($host, $username, $password, $port = 22)
    {
        $this->sftp = new Net_SFTP($host, $port);
        if (!$this->sftp->login($username, $password)) {
            return false;
        }

        $this->connected = true;
        return true;
    }

    /**
     * Upload file ke remote
     */
    public function upload($localPath, $remotePath)
    {
        if (!defined('NET_SFTP_LOCAL_FILE')) {
            define('NET_SFTP_LOCAL_FILE', 1);
        }

        if (!$this->connected) {
            return false;
        }

        if (!file_exists($localPath)) {
            log_message('error', 'File tidak ditemukan: ' . $localPath);
            return false;
        }

        return $this->sftp->put($remotePath, $localPath, NET_SFTP_LOCAL_FILE);
    }


    /**
     * Download file dari remote
     */
    public function download($remotePath, $localPath)
    {
        if (!$this->connected) return false;
        return $this->sftp->get($remotePath, $localPath);
    }

    /**
     * Menampilkan list isi direktori
     */
    public function listDir($remotePath = '.')
    {
        if (!$this->connected) return false;
        return $this->sftp->nlist($remotePath);
    }

    /**
     * Membuat direktori baru
     */
    public function makeDir($remotePath)
    {
        if (!$this->connected) return false;
        return $this->sftp->mkdir($remotePath);
    }

    /**
     * Hapus file
     */
    public function delete($remotePath)
    {
        if (!$this->connected) return false;
        return $this->sftp->delete($remotePath);
    }

    /**
     * Memindahkan atau merename file di server SFTP
     */
    public function move($fromPath, $toPath)
    {
        if (!$this->connected) return false;
        return $this->sftp->rename($fromPath, $toPath);
    }
}
