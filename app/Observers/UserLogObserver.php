<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Variable;
use Illuminate\Support\Facades\DB;
use App\Models\Level\UserLevel;

class UserLogObserver
{
    /**
     * Handle User Activity Hours Event
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function hourReached(User $user): void
    {
        $totalActiveHours = $user->activies->sum('total');
        if ($totalActiveHours % 60 == 0) {
            $user->log->update([
                'activity_hours' => $totalActiveHours * 0.1
            ]);
        }
        $this->calculateScore($user);
    }

    /**
     * Handle the User "followed" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function followed(User $user): void
    {
        $totalFollwers = $user->followers->count();
        $user->log->update([
            'followers_count' => $totalFollwers * 0.1
        ]);
        $this->calculateScore($user);
    }

    /**
     * Handle user trades events
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function traded(User $user): void
    {
        $psc_value = Variable::getRate('psc');
        $trades = DB::table('trades')
            ->where('buyer_id', $user->id)
            ->where(function ($query) use ($psc_value) {
                $query->where('irr_amount', '>', 1000000)
                    ->orWhere('psc_amount', '>', $psc_value);
            })
            ->orWhere('seller_id', $user->id)
            ->where(function ($query) use ($psc_value) {
                $query->where('irr_amount', '>', 1000000)
                    ->orWhere('psc_amount', '>', $psc_value);
            })->count();

        $user->log->update([
            'transactions_count' => $trades * 2
        ]);
        $this->calculateScore($user);
    }

    /**
     * Handel user Deposit Events
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function deposit(User $user): void
    {
        $amount = $user->latestTransaction->amount;
        $user->log->increment('deposit_amount', $amount * 0.0001);
        $this->calculateScore($user);
    }



    private function calculateScore(User $user)
    {
        $log = $user->log;
        $sum = $log->followers_count + $log->transactions_count
            + $log->acitivity_hours + $log->deposit_amount;
        $log->update([
            'score' => $sum
        ]);
        $user->update([
            'score' => $sum
        ]);
        DB::table('levels')->orderBy('score')->each(function ($level) use ($user, $sum) {
            if ($sum >= $level->score) {
                UserLevel::updateOrcreate(
                    ['user_id' => $user->id],
                    ['level_id' => $level->id]
                );
                $prize = DB::table('prizes')->where('level_id', $level->id)->first();
                $alreadyRecievedPrize = DB::table('recieved_level_prizes')
                                            ->where('user_id', $user->id)
                                            ->where('prize_id', $prize->id)
                                            ->exists();
                if (/*$user->can('recievePrize', $prize)*/ ! $alreadyRecievedPrize) {
                    foreach ($prize as $key => $value) {
                        if (
                            in_array($key, ['psc', 'blue', 'red', 'yellow', 'satisfaction', 'effect'])
                            && !is_null($value)
                        ) {
                            $user->assets->increment($key, $value);
                        }
                    }
                    $user->recievedPrizes()->create([
                        'prize_id' => $prize->id
                    ]);
                }
            }
        });
    }
}
