<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class libldap {

    private $conn;
    private $ldap_host;
    private $ldap_port;
    private $ldap_base_dn;

    public function __construct() {
        $this->load->config('ldap'); // Muat konfigurasi dari file ldap.php
        $this->ldap_host = $this->config->item('ldap_host');
        $this->ldap_port = $this->config->item('ldap_port');
        $this->ldap_base_dn = $this->config->item('ldap_base_dn');
    }

    public function connect() {
        $conn = ldap_connect($this->ldap_host, $this->ldap_port);
        if ($conn) {
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            return $conn;
        }
        return false;
    }

    public function bind($conn, $username, $password) {
        $user_dn = "uid=" . $username . "," . $this->ldap_base_dn;
        if (@ldap_bind($conn, $user_dn, $password)) {
            return true;
        }
        return false;
    }

    public function search($conn, $filter, $attributes = array('cn')) {
        $result = ldap_search($conn, $this->ldap_base_dn, $filter, $attributes);
        if ($result) {
            return ldap_get_entries($conn, $result);
        }
        return false;
    }

    public function close($conn) {
        ldap_close($conn);
    }

    public function __get($var) {
        return get_instance()->$var;
    }
}


/***
CONTOH PENGGUNAAN
~~~~~~~~~~~~~~~~~

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('ldap_auth'); // Muat library LDAP
        $this->load->library('session'); // Muat library session
    }

    public function login() {
        // Tampilkan form login jika belum ada post request
        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user_data = $this->ldap_auth->authenticate($username, $password);

            if ($user_data) {
                // Autentikasi berhasil
                // Set data sesi dan arahkan ke halaman dashboard
                $session_data = array(
                    'username' => $username,
                    'logged_in' => TRUE,
                    // Anda bisa menyimpan data lain dari $user_data di sesi
                );
                $this->session->set_userdata($session_data);
                redirect('dashboard');
            } else {
                // Autentikasi gagal
                $data['error'] = "Username atau password salah.";
                $this->load->view('login_form', $data);
            }
        } else {
            $this->load->view('login_form');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}

**/
