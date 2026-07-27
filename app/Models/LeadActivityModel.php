<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadActivityModel extends Model
{
    protected $table            = 'lead_activities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'activity_type',
        'description',
        'page_url',
        'ip_address',
        'device_type',
        'user_agent',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    public function logActivity(int $userId, string $type, string $description = '', ?string $pageUrl = null)
    {
        $request = service('request');
        return $this->insert([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'page_url' => $pageUrl ?? current_url(),
            'ip_address' => $request->getIPAddress(),
            'device_type' => $this->detectDeviceType($request->getUserAgent()->getAgentString()),
            'user_agent' => substr($request->getUserAgent()->getAgentString(), 0, 500),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getActivitiesByUser(int $userId, int $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    protected function detectDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/', $userAgent)) {
            return 'Mobile';
        }
        if (preg_match('/tablet|ipad/', $userAgent)) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}
