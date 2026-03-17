<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'status',
        'total_amount',
        'notes',
        'paid_at',
        'paid_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'paid_at'      => 'datetime',
    ];

    const STATUS_EN_ATTENTE     = 'en_attente';
    const STATUS_EN_PREPARATION = 'en_preparation';
    const STATUS_PRETE          = 'prete';
    const STATUS_PAYEE          = 'payee';
    const STATUS_ANNULEE        = 'annulee';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_EN_ATTENTE     => 'En attente',
            self::STATUS_EN_PREPARATION => 'En préparation',
            self::STATUS_PRETE          => 'Prête',
            self::STATUS_PAYEE          => 'Payée',
            self::STATUS_ANNULEE        => 'Annulée',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateReference(): string
    {
        return 'ISI-' . strtoupper(uniqid());
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_EN_ATTENTE,
            self::STATUS_EN_PREPARATION
        ]);
    }

    public function canBePaid(): bool
    {
        return $this->status === self::STATUS_PRETE;
    }
}