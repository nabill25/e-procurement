<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserLoginModel;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $userModel = new UserLoginModel();
        $user = $userModel->where('user_login', $username)->first();

        if (!$user) {
            return $this->failUnauthorized('Username atau password salah');
        }

        if (!password_verify($password, $user['user_password'])) {
            return $this->failUnauthorized('Username atau password salah');
        }

        if ($user['user_aktif'] !== '1') {
            return $this->failUnauthorized('Akun Anda belum aktif');
        }

        // Set session
        $session = session();
        $session->set([
            'user_id'    => $user['user_login_id'],
            'username'   => $user['user_login'],
            'user_nama'  => $user['user_nama'],
            'user_type'  => $user['user_type_id'],
            'isLoggedIn' => true
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'user'    => [
                'id'       => $user['user_login_id'],
                'username' => $user['user_login'],
                'nama'     => $user['user_nama'],
                'tipe'     => $user['user_type_id']
            ]
        ]);
    }

    public function me()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->failUnauthorized('Not logged in');
        }

        return $this->respond([
            'status' => 'success',
            'user'   => [
                'id'       => $session->get('user_id'),
                'username' => $session->get('username'),
                'nama'     => $session->get('user_nama'),
                'tipe'     => $session->get('user_type')
            ]
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        return $this->respond([
            'status'  => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
