<?php namespace App\Models;

use App\Libraries\Device;
use CodeIgniter\Model;

class ClientModel extends Model
{

    protected $table = 'oauth_clients';
    protected $primaryKey = 'client_id';
    protected $allowedFields = ['client_id', 'client_secret', 'redirect_uri', 'grant_types', 'scope', 'user_id'];
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data)
    {
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }


}
