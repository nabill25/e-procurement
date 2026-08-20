<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;

    public function dashboard()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'Welcome to eProcurement API',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function health()
    {
        return $this->respond([
            'status' => 'ok',
            'database' => 'connected'
        ]);
    }
}
