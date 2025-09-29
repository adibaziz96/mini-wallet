<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'commission_fee',
    ];

    protected $appends = ['total_amount'];

    /**
     * Get the sender of the transaction.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the transaction.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getTotalAmountAttribute()
    {
        $total = $this->amount;

        if ($this->sender_id === Auth::id()) {
            $total += $this->commission_fee;
        }

        return number_format($total, 2, '.', '');
    }
}
