<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['google_id', 'name', 'email','phone_number ', 'api_token','access_token'];

    public function getUserByToken($token) {
        return $this->where('api_token', $token)->first();
    }
}
