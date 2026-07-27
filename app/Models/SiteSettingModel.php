<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['setting_key', 'setting_value', 'setting_group'];
    protected $useTimestamps = true;

    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function setSetting($key, $value, $group = 'general')
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        }
        return $this->insert(['setting_key' => $key, 'setting_value' => $value, 'setting_group' => $group]);
    }

    public function getSettingsByGroup($group)
    {
        $settings = $this->where('setting_group', $group)->findAll();
        $result = [];
        foreach ($settings as $s) {
            $result[$s['setting_key']] = $s['setting_value'];
        }
        return $result;
    }

    public function getAllSettings()
    {
        $settings = $this->findAll();
        $result = [];
        foreach ($settings as $s) {
            $result[$s['setting_key']] = $s['setting_value'];
        }
        return $result;
    }
}
