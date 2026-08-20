<?php

namespace App\Controllers;

use App\Models\UserLoginModel;
use CodeIgniter\Controller;

class TestDB extends Controller
{
    public function index()
    {
        $model = new UserLoginModel();
        
        try {
            $users = $model->findAll(5);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Database connection successful!',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
