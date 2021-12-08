<?php namespace App\Models;

use App\Libraries\Device;
use CodeIgniter\Model;

class DeviceModel extends Model
{

    protected $table = 'oauth_device';
    protected $primaryKey = 'device_id';
    protected $allowedFields = ['device_client_id', 'device_uuid', 'device_name', 'device_is_virtual', 'device_manufacturer', 'device_model', 'device_os', 'device_platform', 'device_agent', 'device_webview_version', 'device_app_name', 'device_app_build', 'device_app_id', 'device_app_version', 'device_key', 'device_private_key', 'device_firebase_token', 'device_status', 'device_server_token'];

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data)
    {
        $this->deviceLib = new Device();
        $data = $this->generateDeviceKey($data);
        $data = $this->generateDevicePrivateKey($data);
        $data = $this->generateDeviceServerToken($data);
        //var_dump($data);
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    protected function generateDeviceKey(array $data)
    {

        if (!isset($data['data']['device_key']))
            $data['data']['device_key'] = $this->deviceLib->generateDeviceKey();

        return $data;
    }

    protected function generateDeviceServerToken(array $data)
    {
        if (!isset($data['data']['device_server_token']))
            $data['data']['device_server_token'] = $this->deviceLib->generateDeviceServerToken();

        return $data;
    }

    protected function generateDevicePrivateKey(array $data)
    {
        if (!isset($data['data']['device_private_key']))
            $data['data']['device_private_key'] = $this->deviceLib->generateDevicePrivateKey();

        return $data;
    }

}
