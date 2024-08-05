<?php

namespace Modules\UserManagement\Lib;

use Illuminate\Support\Facades\DB;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Entities\TripRequestFee;
use Modules\UserManagement\Entities\LoyaltyPointsHistory;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAccount;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Entities\UserLevelHistory;

trait LevelUpdateCheckerTrait
{
    public function customerLevelUpdateChecker($customer)
    {
        $level = $customer->level;
        $currentSequence = $level->sequence;
        $nextLevel = UserLevel::where('user_type', CUSTOMER)->where('is_active',1)->where('sequence', '>', $currentSequence)->orderBy('sequence', 'asc')->first();
        $userLevelHistory = $this->getUserLevelHistoryByUserLevelId($customer->id,$level->id);
        if ($userLevelHistory == null) {
            $spendAmount = TripRequest::where(['customer_id' => $customer->id, 'payment_status' => PAID])->sum('paid_fare');
            $totalTrip = $customer?->customerTrips?->count();
            $cancelTrip = TripRequest::where(['customer_id' => $customer->id, 'current_status' => CANCELLED])->count();
            $completedTrip = TripRequest::where(['customer_id' => $customer->id, 'current_status' => COMPLETED])->count();
            $cancellationRate = ($cancelTrip / ($totalTrip == 0 ? 1 : $totalTrip)) * 100;
            $givenReviews = $customer->givenReviews->count();
            if ($level->targeted_ride <= $completedTrip && $level->targeted_amount <= $spendAmount && ($level->targeted_cancel >= $cancellationRate || $level->targeted_cancel == 0) && $level->targeted_review <= $givenReviews) {
                $customerLevelStatus = businessConfig(CUSTOMER_LEVEL, CUSTOMER_SETTINGS);
                if ($customerLevelStatus && $customerLevelStatus->value == 1) {
                    if ($nextLevel) {
                        $customer->update([
                            'user_level_id' => $nextLevel->id,
                        ]);
                    }
                    $this->createLevelHistory($customer,$level);
                    $this->grantRewardUpdate($level, $customer);
                    sendDeviceNotification(fcm_token: $customer->fcm_token,
                        title: translate('Level Completed Successfully!'),
                        description: translate("Congratulations! Your Level has been completed."),
                        action: 'level_completed',
                        user_id: $customer->id,
                        notificationData: [
                            'reward_type' => $level->reward_type,
                            'reward_amount' => $level->reward_amount,
                            'next_level' => $nextLevel ? $nextLevel?->name : null,
                        ]
                    );
                } else {
                    if ($nextLevel) {
                        $customer->update([
                            'user_level_id' => $nextLevel->id,
                        ]);
                    }
                }
            }
        }else{
            $customerLevelStatus = businessConfig(CUSTOMER_LEVEL, CUSTOMER_SETTINGS);
            if ($customerLevelStatus && $customerLevelStatus->value == 1) {
                if ($nextLevel) {
                    $customer->update([
                        'user_level_id' => $nextLevel->id,
                    ]);
                    sendDeviceNotification(fcm_token: $customer->fcm_token,
                        title: translate('Level Completed Successfully!'),
                        description: translate("Congratulations! Your Level has been completed."),
                        action: 'no_rewards',
                        user_id: $customer->id,
                        notificationData: [
                            'reward_type' => $level->reward_type,
                            'reward_amount' => $level->reward_amount,
                            'next_level' => $nextLevel ? $nextLevel?->name : null,
                        ]
                    );
                }
            }
        }
        return User::find($customer->id);
    }

    public function driverLevelUpdateChecker($driver)
    {
        $level = $driver->level;
        $currentSequence = $level->sequence;
        $nextLevel = UserLevel::where('user_type', DRIVER)->where('is_active',1)->where('sequence', '>', $currentSequence)->orderBy('sequence', 'asc')->first();
        $userLevelHistory = $this->getUserLevelHistoryByUserLevelId($driver->id,$level->id);
        if ($userLevelHistory == null) {
            $tripTotalEarning = TripRequest::where(['driver_id' => $driver->id, 'payment_status' => PAID])->sum('paid_fare');
            $trip = TripRequest::where(['driver_id' => $driver->id, 'payment_status' => PAID]);
            $adminCommission = TripRequestFee::whereIn('trip_request_id', $trip->pluck('id')->toArray())->sum('admin_commission');
            $totalTrip = $driver?->driverTrips?->count();
            $cancelTrip = TripRequest::where(['driver_id' => $driver->id, 'current_status' => CANCELLED])->count();
            $completedTrip = TripRequest::where(['driver_id' => $driver->id, 'current_status' => COMPLETED])->count();
            $cancellationRate = ($cancelTrip / ($totalTrip == 0 ? 1 : $totalTrip)) * 100;
            $givenReviews = $driver->givenReviews->count();
            $earningAmount = $tripTotalEarning - $adminCommission;
            if ($level->targeted_ride <= $completedTrip && $level->targeted_amount <= $earningAmount && ($level->targeted_cancel >= $cancellationRate || $level->targeted_cancel == 0) && $level->targeted_review <= $givenReviews) {
                $driverLevelStatus = businessConfig(DRIVER_LEVEL, DRIVER_SETTINGS);
                if ($driverLevelStatus && $driverLevelStatus->value == 1) {
                    if ($nextLevel) {
                        $driver->update([
                            'user_level_id' => $nextLevel->id,
                        ]);
                    }
                    $this->createLevelHistory($driver,$level);
                    $this->grantRewardUpdate($level, $driver);
                    sendDeviceNotification(fcm_token: $driver->fcm_token,
                        title: translate('Level Completed Successfully!'),
                        description: translate("Congratulations! Your Level has been completed."),
                        action: 'level_completed',
                        user_id: $driver->id,
                        notificationData: [
                            'reward_type' => $level->reward_type,
                            'reward_amount' => $level->reward_amount,
                            'next_level' => $nextLevel ? $nextLevel?->name : null,
                        ]
                    );
                } else {
                    if ($nextLevel) {
                        $driver->update([
                            'user_level_id' => $nextLevel->id,
                        ]);
                    }
                }
            }
        }else{
            $driverLevelStatus = businessConfig(DRIVER_LEVEL, DRIVER_SETTINGS);
            if ($driverLevelStatus && $driverLevelStatus->value == 1) {
                if ($nextLevel) {
                    $driver->update([
                        'user_level_id' => $nextLevel->id,
                    ]);
                    sendDeviceNotification(fcm_token: $driver->fcm_token,
                        title: translate('Level Completed Successfully!'),
                        description: translate("Congratulations! Your Level has been completed."),
                        action: 'level_completed',
                        user_id: $driver->id,
                        notificationData: [
                            'reward_type' => "no_rewards",
                            'reward_amount' => $level->reward_amount,
                            'next_level' => $nextLevel ? $nextLevel?->name : null,
                        ]
                    );
                }
            }
        }
        return User::find($driver->id);

    }


    private function levelLoyaltyPointHistory($user_id, $level)
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

    private function grantRewardUpdate($level, $user): void
    {
        $reward_type = $level->reward_type;
        if ($reward_type == 'loyalty_points') {
            $data = User::query()->firstWhere('id', $user->id);
            $data->loyalty_points += $level->reward_amount;
            $data->save();

            $history = new LoyaltyPointsHistory();
            $history->user_id = $user->id;
            $history->model = 'user_level';
            $history->model_id = $level->id;
            $history->points = $level->reward_amount;
            $history->type = 'credit';
            $history->save();
        } elseif ($reward_type == 'wallet') {
            if ($user->type == 'customer') {
                //Customer account update
                $customer = UserAccount::query()->firstWhere('user_id', $user->id);
                $customer->wallet_balance += $level->reward_amount;
                $customer->save();

                //customer transaction (credit)
                $primary_transaction = new Transaction();
                $primary_transaction->attribute = 'level_reward';
                $primary_transaction->credit = $level->reward_amount;
                $primary_transaction->balance = $customer->wallet_balance;
                $primary_transaction->user_id = $user->id;
                $primary_transaction->account = 'wallet_balance';
                $primary_transaction->save();
            } else {
                //Customer account update
                $driver = UserAccount::query()->firstWhere('user_id', $user->id);
                $driver->receivable_balance += $level->reward_amount;
                $driver->save();

                //customer transaction (credit)
                $primary_transaction = new Transaction();
                $primary_transaction->attribute = 'level_reward';
                $primary_transaction->credit = $level->reward_amount;
                $primary_transaction->balance = $driver->receivable_balance;
                $primary_transaction->user_id = $user->id;
                $primary_transaction->account = 'receivable_balance';
                $primary_transaction->save();
            }
        }

    }


    private function getUserLevelHistoryByUserLevelId($userId,$levelId)
    {
        return UserLevelHistory::query()
            ->where(['user_id' => $userId, 'user_level_id' => $levelId])
            ->first();
    }
    private function createLevelHistory($user, $level)
    {
        $history = new UserLevelHistory();
        $history->user_level_id = $level->id;
        $history->user_id = $user->id;
        $history->user_type = $user->user_type;
        $history->completed_ride = $level->targeted_ride;
        $history->ride_reward_status = 1;
        $history->total_amount = $level->targeted_amount;
        $history->amount_reward_status = 1;
        $history->cancellation_rate = $level->targeted_cancel;
        $history->cancellation_reward_status = 1;
        $history->reviews = $level->targeted_review;
        $history->reviews_reward_status = 1;
        $history->is_level_reward_granted = 1;
        $history->save();

        return $history;
    }

}

