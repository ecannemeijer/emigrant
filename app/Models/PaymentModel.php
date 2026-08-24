<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'plan',
        'amount',
        'currency',
        'status',
        'paypal_order_id',
        'paypal_capture_id',
        'payload',
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function findByOrderId(string $orderId): ?array
    {
        return $this->where('paypal_order_id', $orderId)->first();
    }

    public function findByCaptureId(string $captureId): ?array
    {
        return $this->where('paypal_capture_id', $captureId)->first();
    }
}
