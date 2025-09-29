<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use App\Models\User;

class TransactionMade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $user;

    public function __construct(Transaction $transaction, User $receiver)
    {
        $this->transaction = $transaction->load('sender', 'receiver');
        $this->user = $receiver;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->transaction->receiver_id);
    }

    public function broadcastAs()
    {
        return 'TransactionMade';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->transaction->id,
            'sender_id' => $this->transaction->sender_id,
            'receiver_id' => $this->transaction->receiver_id,
            'amount' => $this->transaction->amount,
            'commission_fee' => $this->transaction->commission_fee,
            'created_at' => $this->transaction->created_at->toDateTimeString(),
            'sender' => $this->transaction->sender,
            'receiver' => $this->transaction->receiver,
            'balance' => $this->user->balance,
            'total_amount' => $this->transaction->total_amount,
        ];
    }
}
