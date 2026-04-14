<?php namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['s_key', 's_value'];
    protected $useTimestamps = true;

    public function get_value($key)
    {
        $setting = $this->where('s_key', $key)->first();
        return $setting ? $setting['s_value'] : null;
    }

    public function set_value($key, $value)
    {
        $setting = $this->where('s_key', $key)->first();
        if ($setting) {
            return $this->update($setting['id'], ['s_value' => $value]);
        } else {
            return $this->insert(['s_key' => $key, 's_value' => $value]);
        }
    }
}
