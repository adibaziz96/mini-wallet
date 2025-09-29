<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Events\TransactionMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Get authenticated user's transactions + balance
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('page', 1);

        $transactions = Transaction::with(['sender', 'receiver'])->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'balance' => $user->balance,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Execute a new money transfer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:'.Auth::id(),
            'amount' => 'required|numeric|min:0.01',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($validated['receiver_id']);
        $amount = $validated['amount'];
        $transaction = null;

        try {
            DB::transaction(function () use ($sender, $receiver, $amount, &$transaction) {
                $commission = round($amount * 0.015, 2);
                $totalDebit = $amount + $commission;

                $sender->lockForUpdate();
                $receiver->lockForUpdate();

                if ($sender->balance < $totalDebit) {
                    throw new \Exception("Insufficient balance");
                }

                $sender->decrement('balance', $totalDebit);
                $receiver->increment('balance', $amount);

                $transaction = Transaction::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'amount' => $amount,
                    'commission_fee' => $commission,
                ]);

                broadcast(new TransactionMade($transaction, $receiver))->toOthers();
            });

            return response()->json(['message' => 'Transfer successful', 'data' => [
                'balance' => $sender->fresh()->balance,
                'transaction' => $transaction->load('sender', 'receiver'),
            ]], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
