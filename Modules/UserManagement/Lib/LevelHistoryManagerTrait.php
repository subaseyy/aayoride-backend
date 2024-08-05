<?php

namespace Modules\UserManagement\Lib;

use Illuminate\Support\Facades\DB;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\LoyaltyPointsHistory;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAccount;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Entities\UserLevelHistory;

trait LevelHistoryManagerTrait
{
    public function completedRideChecker($user): void
    {
        $level = $this->getUserLevel($user);
        $history = $this->getUserLevelHistory($user);

        if ($level->targeted_ride > $history->completed_ride || $level->targeted_amount > $history->total_amount || $level->targeted_cancel < $history->cancellation_rate || $level->targeted_review > $history->reviews) {
            $history->increment('completed_ride');
            if($history->completed_ride >= $level->targeted_ride){
                if (!$history->ride_reward_status) {
                    $history->ride_reward_status = true;
                }
            }
        }
        else {
            if (!$history->is_level_reward_granted) {
                $this->loyaltyPointsHistory($user->id, $level);
                $this->grantReward($level, $user);
                $history->is_level_reward_granted = true;
                $history->cancellation_reward_status = true;
                $history->save();
            }
            $history = $this->createNewLevelHistory($user, $level, $history);
            $history->increment('completed_ride');
        }
        $history->save();
    }
    public function cancellationPercentChecker($user): void
    {
        $level = $this->getUserLevel($user);
        $history = $this->getUserLevelHistory($user);
        if ($level->targeted_ride > $history->completed_ride || $level->targeted_amount > $history->total_amount || $level->targeted_cancel < $history->cancellation_rate || $level->targeted_review > $history->reviews) {
            $column = $user->user_type == 'customer' ? 'customer_id' : 'driver_id';
            $cancelCount = TripRequest::query()->where([$column => $user->id, 'current_status' => 'cancelled'])->whereNotNull('driver_id')->count();
            $total = TripRequest::query()->where([$column => $user->id])->count();
            $cancelPercent = ($cancelCount/$total) * 100;
            $history->cancellation_rate = $cancelPercent;
            if($history->cancellation_rate <= $level->targeted_cancel){
                if (!$history->cancellation_reward_status) {
                    $history->cancellation_reward_status = true;
                }
            }
        }
        else {
            if (!$history->is_level_reward_granted) {
                $this->loyaltyPointsHistory($user->id, $level);
                $this->grantReward($level, $user);
                $history->is_level_reward_granted = true;
                $history->cancellation_reward_status = true;
                $history->save();
            }
            $history = $this->createNewLevelHistory($user, $level, $history);
            $column = $user->user_type == 'customer' ? 'customer_id' : 'driver_id';
            $cancelCount = TripRequest::query()->where([$column => $user->id, 'current_status' => 'cancelled'])->count();
            $total = TripRequest::query()->where([$column => $user->id])->count();
            $cancelPercent = ($cancelCount/$total) * 100;
            $history->cancellation_rate = $cancelPercent;
        }

        $history->save();
    }
    public function reviewCountChecker($user): void
    {
        DB::beginTransaction();
        $level = $this->getUserLevel($user);
        $history = $this->getUserLevelHistory($user);

        if ($level->targeted_ride > $history->completed_ride || $level->targeted_amount > $history->total_amount || $level->targeted_cancel < $history->cancellation_rate || $level->targeted_review > $history->reviews) {

            $history->increment('reviews');
            if($history->reviews >= $level->targeted_review){
                if (!$history->reviews_reward_status) {
                    $history->reviews_reward_status = true;
                }
            }
        }
        else {
            if (!$history->is_level_reward_granted) {
                $this->loyaltyPointsHistory($user->id, $level);
                $this->grantReward($level, $user);
                $history->is_level_reward_granted = true;
                $history->cancellation_reward_status = true;
                $history->save();
            }
            $history = $this->createNewLevelHistory($user, $level, $history);
            $history->increment('reviews');

        }

        $history->save();
        DB::commit();
    }
    public function amountChecker($user, $amount): void
    {
        DB::beginTransaction();
        $level = $this->getUserLevel($user);
        $history = $this->getUserLevelHistory($user);

        if ($level->targeted_ride > $history->completed_ride || $level->targeted_amount > $history->total_amount || $level->targeted_cancel < $history->cancellation_rate || $level->targeted_review > $history->reviews) {
            $history->total_amount += $amount;
            if($history->total_amount >= $level->targeted_amount){
                if (!$history->amount_reward_status) {
                    $history->amount_reward_status = true;
                }
            }
        }
        else {
            if (!$history->is_level_reward_granted) {
                $this->loyaltyPointsHistory($user->id, $level);
                $this->grantReward($level, $user);
                $history->is_level_reward_granted = true;
                $history->cancellation_reward_status = true;
                $history->save();
            }
            $history = $this->createNewLevelHistory($user, $level, $history);

            $history->total_amount = $amount;
        }

        $history->save();
        DB::commit();
    }


    private function grantReward($level, $user): void
    {
        $reward_type = $level->reward_type;
        if ($reward_type == 'loyalty_points') {
            $data = User::query()->firstWhere('id', $user->id);
            $data->loyalty_points += $level->reward_amount;
            $data->save();
        }
        elseif ($reward_type == 'wallet') {
            $data = UserAccount::query()->firstWhere('user_id', $user->id);

            if ($user->type == 'customer') {
                $data->wallet_balance += $level->reward_amount;
                (new class { use TransactionTrait; })->customerLevelRewardTransaction($user, $level->reward_amount);
            } else {
                $data->receivable_balance += $level->reward_amount;
                (new class { use TransactionTrait; })->driverLevelRewardTransaction($user, $level->reward_amount);
            }
            $data->save();
        }

    }
    private function getUserLevel($user)
    {
        return UserLevel::query()->where(['id' => $user->user_level_id, 'user_type' => $user->user_type])->first();
    }
    private function getUserLevelHistory($user)
    {
        return UserLevelHistory::query()
            ->where(['user_id' => $user->id, 'user_level_id' => $user->user_level_id])
            ->first();
    }
    private function createNewLevelHistory($user, $level, $current_history)
    {
        $levels = UserLevel::query()->where(['user_type' => $user->user_type, 'is_active' => 1])->orderBy('sequence')->get();
        $next_level_id = null;
        foreach ($levels as $key => $level) {
            if ($level->id == $level->id) {
                if ($levels->has($key+1)) {
                    $next_level_id = $levels[$key+1]->id;
                }
                break;
            }
        }
        if (is_null($next_level_id)) {
            //user climbed on top level, return current history
            return $current_history;
        }
        $history = new UserLevelHistory();
        $history->user_level_id = $next_level_id;
        $history->user_id = $user->id;
        $history->user_type = $user->user_type;
        $history->save();

        // update user level id on user table
        $user->update([
            'user_level_id' => $next_level_id,
        ]);

        return $history;
    }

    private function loyaltyPointsHistory($user_id, $level)
    {
        if ($level->reward_type == 'loyalty_points') {
            $history = new LoyaltyPointsHistory();
            $history->user_id = $user_id;
            $history->model = 'user_level';
            $history->model_id = $level->id;
            $history->points = $level->reward_amount;
            $history->type = 'credit';
            $history->save();
        }
    }
}
