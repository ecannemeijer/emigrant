<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'username',
        'action',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'extra_data',
        'created_at',
    ];

    protected $useTimestamps  = false;
    protected $dateFormat     = 'datetime';

    /**
     * Log an action.
     */
    public function log(string $action, string $method, string $url, ?int $userId = null, ?string $username = null, ?string $extraData = null): void
    {
        $request = service('request');

        $this->insert([
            'user_id'    => $userId,
            'username'   => $username,
            'action'     => $action,
            'method'     => $method,
            'url'        => $url,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'extra_data' => $extraData,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get paginated logs with optional user filter.
     */
    public function getLogs(int $perPage = 50, ?int $userId = null): array
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if ($userId !== null) {
            $builder = $builder->where('user_id', $userId);
        }

        return [
            'logs'   => $builder->paginate($perPage),
            'pager'  => $this->pager,
        ];
    }

    /**
     * Get unique users that have log entries.
     */
    public function getLoggedUsers(): array
    {
        return $this->select('user_id, username')
            ->groupBy('user_id')
            ->orderBy('username', 'ASC')
            ->findAll();
    }

    /**
     * Delete logs older than X days.
     */
    public function deleteOlderThan(int $days): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $date)->delete();
    }

    /**
     * Clear all logs.
     */
    public function clearAll(): void
    {
        $this->db->table($this->table)->truncate();
    }
}
