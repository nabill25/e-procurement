<?php

namespace App\Libraries;

use phpCAS;

class Cas
{
    public function __construct()
    {
        if (!defined('PHPCAS_AUTOLOAD_PATH')) {
            //$serviceBaseUrl = rtrim(base_url(), '/');
            // Inisialisasi phpCAS
            phpCAS::client(
                CAS_VERSION_2_0, 
                (string)getenv('CAS_HOSTNAME'), 
                (int)getenv('CAS_PORT'), 
                (string)getenv('CAS_URI'),
                base_url(), // Tambahkan parameter kelima di sini
                false 
            );
            
            // Set sertifikat (pilih salah satu)
            // phpCAS::setCasServerCACert('/path/to/cert.pem');
            
            // Tambahkan ini untuk mencegah phpCAS menebak URL sendiri 
            phpCAS::setFixedServiceURL(base_url());
            phpCAS::setNoCasServerValidation(); // Hanya untuk testing, jangan di production!
        }

        // 2. TAMBAHKAN BARIS INI: 
        // Mencegah phpCAS mencoba memulai atau memanipulasi session secara agresif
        \phpCAS::setNoCasServerValidation();

        // 3. Penanganan SSL
        \phpCAS::setNoCasServerValidation();
        
    }

    public function forceAuth()
    {
        phpCAS::forceAuthentication();
    }

    public function getUser()
    {
        return phpCAS::getUser();
    }

    public function getAttributes()
    {
        return phpCAS::getAttributes();
    }

    public function logout(): void
{
    // Cukup teruskan string URL, jangan gunakan array ['service' => ...]
    phpCAS::logoutWithRedirectService(base_url());
}
}