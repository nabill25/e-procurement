<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        //die('kok masuk sini?');
        //return view('welcome_message');
        // Karena sudah melewati filter, session pasti sudah ada isinya
        $username = session()->get('username');
        $attributes = session()->get('cas_attributes');
        /*
        print_r($_SESSION);exit;
;        return view('welcome_message', [
            'username' => $username,
            'info'     => $attributes
        ]);
        */

        // Ambil data user & role dari PostgreSQL
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->where('user_login', $username)->first();
        //rint "<pre> ";
        $role = array (1=>'Admin', 2=>'vms');
        //var_dump($userData);exit;
        if ($userData && in_array($userData['user_type_id'], ['1', '2'])) {
            // Simpan session
            session()->set([
                'isLoggedIn' => true,
                'username'   => $username,
                'role'       => $role[$userData['user_type_id']],
                'user_id'    => $userData['user_login_id'],
            ]);
            return redirect()->to('/penyedia');
        } else {
            return redirect()->to('/login');
        }
    }
}
