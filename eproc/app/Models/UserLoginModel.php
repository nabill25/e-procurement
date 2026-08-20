<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLoginModel extends Model
{
    protected $table            = 'user_login';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false; // Disable temporarily to allow all fields for testing

    // Dates
    protected $useTimestamps = false; // Disable until we confirm table schema
}
