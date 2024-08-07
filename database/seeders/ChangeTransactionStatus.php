<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChangeTransactionStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::chunk(100, function ($transactions) {
            foreach ($transactions as $transaction) {
                if ($transaction->status === 0 || $transaction->status === -1) {
                    $transaction->update(['status' => -138]);
                }

                if ($transaction->status === 1) {
                    $transaction->update(['status' => 0]);
                }
            }
        });

        Order::chunk(100, function ($orders) {
            foreach ($orders as $order) {
                if ($order->status === 0 || $order->status === -1) {
                    $order->update(['status' => -138]);
                }

                if ($order->status === 1) {
                    $order->update(['status' => 0]);
                }
            }
        });
    }
}
