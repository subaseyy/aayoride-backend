<?php

namespace Modules\UserManagement\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\TransactionManagement\Repository\TransactionRepositoryInterface;
use Modules\TripManagement\Repository\TripRequestRepositoryInterface;
use Modules\UserManagement\Repository\UserLevelRepositoryInterface;
use Modules\UserManagement\Repository\UserRepositoryInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;

class DriverService extends BaseService implements Interface\DriverServiceInterface
{
    protected $userRepository;
    protected $tripRequestRepository;
    protected $transactionRepository;
    protected $userLevelRepository;

    public function __construct(UserRepositoryInterface        $userRepository, TripRequestRepositoryInterface $tripRequestRepository,
                                TransactionRepositoryInterface $transactionRepository, UserLevelRepositoryInterface $userLevelRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
        $this->tripRequestRepository = $tripRequestRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userLevelRepository = $userLevelRepository;
    }

    public function index(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $data = [];
        if (array_key_exists('status', $criteria) && $criteria['status'] !== 'all') {
            $data['is_active'] = $criteria['status'] == 'active' ? 1 : 0;
        }
        if (array_key_exists(PENDING, $criteria)) {
            $data[] = ['old_identification_image', '!=', null];
        }
        $data['user_type'] = DRIVER;
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['full_name', 'first_name', 'last_name', 'email', 'phone'];
            $searchData['value'] = $criteria['search'];
        }
        return $this->userRepository->getBy(criteria: $data, searchCriteria: $searchData, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery);
    }

    public function create(array $data): ?Model
    {
        $firstLevel = $this->userLevelRepository->findOneBy(criteria: ['user_type' => DRIVER, 'sequence' => 1]);
        $identityImages = [];
        if (array_key_exists('identity_images', $data)) {
            foreach ($data['identity_images'] as $image) {
                $identityImages[] = fileUploader('driver/identity/', 'png', $image);
            }
        }

        if (array_key_exists('other_documents', $data)) {
            $otherDocuments = [];
            foreach ($data['other_documents'] as $document) {
                $otherDocuments[] = fileUploader('driver/document/', $document->getClientOriginalExtension(), $document);
            }
        }
        $driverData = array_merge($data, [
            'full_name' => $data['first_name'] . " " . $data['last_name'],
            'user_type' => DRIVER,
            'password' => array_key_exists('password', $data) ? bcrypt($data['password']) : null,
            'user_level_id' => $firstLevel?->id,
            'profile_image' => array_key_exists('profile_image', $data) ? fileUploader('driver/profile/', 'png', $data['profile_image']) : null,
            'other_documents' => $otherDocuments ?? null,
            'identification_image' => $identityImages ?? null,
            'is_active' => 1,
        ]);
        DB::beginTransaction();

        $driver = $this->userRepository->create($driverData);
        $driver?->levelHistory()->create([
            'user_level_id' => $firstLevel?->id,
            'user_type' => DRIVER
        ]);
        $driver?->driverDetails()->create([
            'is_online' => false,
            'availability_status' => 'unavailable'
        ]);
        $driver?->userAccount()->create();

        DB::commit();
        return $driver;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $driver = $this->userRepository->findOne(id: $id);
        $driverData = $data;
        $identityImages = [];
        $oldIdentityImages = [];
        if (array_key_exists('type', $data)) {
            if (array_key_exists('identity_images', $data)) {
                foreach ($data['identity_images'] as $image) {
                    $identityImages[] = fileUploader('driver/identity/', 'png', $image);
                }
            } else {
                $identityImages = $driver?->identification_image;
            }
        } else {
            if (array_key_exists('identity_images', $data)) {
                foreach ($data['identity_images'] as $image) {
                    $identityImages[] = fileUploader('driver/identity/', 'png', $image);
                }
                if ($driver?->identification_image != null && count($driver?->identification_image) > 0 && $driver?->old_identification_image == null) {
                    $oldIdentityImages = $driver?->identification_image;
                }
            } else {
                $identityImages = $driver?->identification_image;
                $oldIdentityImages = $driver?->old_identification_image;
            }
        }

//        if ($driver?->identification_image != null || count($driver?->identification_image) > 0) {
//            $oldIdentityImages = $driver?->identification_image;
//        }
        $otherDocuments = [];
        if (array_key_exists('other_documents', $data)) {
            foreach ($data['other_documents'] as $image) {
                $otherDocuments[] = fileUploader('driver/document/', $image->getClientOriginalExtension(), $image);
            }
        } else {
            $otherDocuments = $driver?->other_documents;
        }
        if (array_key_exists('profile_image', $data)) {
            $profile_image = fileUploader('driver/profile/', 'png', $data['profile_image'], $driver?->profile_image);
        }

        if (array_key_exists('password', $data) && !is_null($data['password'])) {
            $password = bcrypt($data['password']);
            $driverData = array_merge($driverData, [
                'password' => $password
            ]);
        } else {
            unset($driverData['password']);
        }
        $driverData = array_merge($driverData, [
            'full_name' => $data['first_name'] . " " . $data['last_name'],
            'loyalty_points' => array_key_exists('decrease', $data) ? $driver->loyalty_points -= $data['decrease'] : (array_key_exists('increase', $data) ? $driver->loyalty_points += $data['increase'] : 0),
            'profile_image' => $profile_image ?? $driver?->profile_image,
            'other_documents' => $otherDocuments,
            'identification_image' => $identityImages,
            'old_identification_image' => $oldIdentityImages,
            'is_active' => $driver?->is_active ?? 1,
        ]);

        DB::beginTransaction();
        $driver = $this->userRepository->update(id: $id, data: $driverData);

        // Customer Address
        if (array_key_exists('address', $data)) {
            $address = $driver?->addresses()->where(['user_id' => $driver?->id, 'address_label' => 'default'])->first();
            if (is_null($address)) {
                $driver?->addresses()->create([
                    'address' => $data['address'],
                    'address_label' => 'default'
                ]);
            } else {
                $address->address = $data['address'];
                $address->save();
            }
        }
        DB::commit();
        return $driver;
    }

    public function show(int|string $id, array $data)
    {
        $driver = $this->userRepository->findOne(id: $id);
        $tab = $data['tab'] ?? 'overview';
        $reviewedBy = $data['reviewed_by'] ?? null;

        //driver rate info
        $driverRateInfoData = $this->driverRateInfo($driver);

        $commonData = [
            'collectable_amount' => $driver?->userAccount->payable_balance > $driver?->userAccount->receivable_balance ? ($driver?->userAccount->payable_balance - $driver?->userAccount->receivable_balance) : 0,
            'pending_withdraw' => $driver?->userAccount->pending_balance,
            'already_withdrawn' => $driver?->userAccount->total_withdrawn,
            'withdrawable_amount' => $driver?->userAccount->receivable_balance> $driver?->userAccount->payable_balance ? ($driver?->userAccount->receivable_balance- $driver?->userAccount->payable_balance) : 0,
            'total_earning' => $driver?->userAccount->received_balance,
            'idle_rate_today' => $driverRateInfoData['idleRateToday'],
            'avg_active_day' => $driverRateInfoData['avgActiveRateByDay'],
            'driver_avg_earning' => $driverRateInfoData['driverAvgEarning'],
            'completed_trips' => $driverRateInfoData['completedTrips'],
            'cancelled_trips' => $driverRateInfoData['cancelledTrips'],
            'success_rate' => $driverRateInfoData['successRate'],
            'cancel_rate' => $driverRateInfoData['cancelRate'],
            'positive_review_rate' => $driverRateInfoData['positiveReviewRate'],
            'driver' => $driver,
            'tab' => $tab,
        ];

        $otherData = [];

        if ($tab == 'overview') {
            $overviewData = $this->overview($driver);
            $otherData = [
                'totalMorningTime' => $overviewData['totalMorningTime'],
                'totalMiddayTime' => $overviewData['totalMiddayTime'],
                'totalEveningTime' => $overviewData['totalEveningTime'],
                'totalNightTime' => $overviewData['totalNightTime'],
                'total_active_hours' => $overviewData['total_active_hours'],
                'targeted_review_point' => $overviewData['targeted_review_point'],
                'targeted_cancel_point' => $overviewData['targeted_cancel_point'],
                'targeted_amount_point' => $overviewData['targeted_amount_point'],
                'targeted_ride_point' => $overviewData['targeted_ride_point'],
                'driver_lowest_fare' => $overviewData['driver_lowest_fare'],
                'driver_highest_fare' => $overviewData['driver_highest_fare'],
                'driver_level_point_goal' => $overviewData['driver_level_point_goal']
            ];
        } else if ($tab == 'vehicle') {
            if (!empty($driver->vehicle)) {
                //vehicle tab
                $vehicleTripCount = $driver->driverTrips()->where('current_status', 'completed')->where('vehicle_id', $driver->vehicle->id)->count();
                $vehicleRate = ($commonData['completed_trips'] > 0) ? ($vehicleTripCount / $commonData['completed_trips']) * 100 : 0;

                //parcel
                $parcelTripCount = $driver->driverTrips()->where('current_status', 'completed')->where('type', 'parcel')->where('vehicle_id', $driver?->vehicle?->id)->count();
                $parcelCompletedTrips = $driver->driverTrips()->where('current_status', 'completed')->where('type', 'parcel')->count();
                $parcelRate = ($parcelCompletedTrips > 0) ? ($parcelTripCount / $parcelCompletedTrips) * 100 : 0;
            } else {
                $vehicleRate = 0;
                $parcelRate = 0;
                $vehicleTripCount = 0;
            }

            $otherData = [
                'vehicle_trip_count' => $vehicleTripCount,
                'vehicle_rate' => $vehicleRate,
                'parcel_rate' => $parcelRate,
            ];
        } else if ($tab == 'trips') {
            $criteria = [
                'driver_id' => $id,
                // 'type' => 'ride_request'
            ];
            $searchCriteria = [];
            if (array_key_exists('search', $data)) {
                $searchCriteria = [
                    'fields' => ['id', 'ref_id'],
                    'value' => $data['search']
                ];
            }
            $driverTrips = $this->tripRequestRepository->getBy(criteria: $criteria, searchCriteria: $searchCriteria, relations: ['customer', 'driver', 'fee'], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $data['page'] ?? 1);
            $otherData = [
                'trips' => $driverTrips,
                'search' => $data['search'] ?? null,
            ];
        } else if ($tab == 'transaction') {
            $transactionCriteria['user_id'] = $id;
            $searchTransactionCriteria = [];
            if (array_key_exists('search', $data)) {
                $searchTransactionCriteria = [
                    'fields' => ['id'],
                    'value' => $data['search'],
                ];
            }

            $transactions = $this->transactionRepository
                ->getBy(criteria: $transactionCriteria, searchCriteria: $searchTransactionCriteria, relations: ['user'], orderBy: ['updated_at' => 'desc'], limit: paginationLimit(), offset: $data['page'] ?? 1);
            $otherData = [
                'transactions' => $transactions,
                'search' => $data['search'] ?? null,
            ];
        } else if ($tab == 'review') {
            $reviewData = $this->reviewInformation($driver, $reviewedBy);
            $otherData = [
                'customer_reviews' => $reviewData['customerReviews'],
                'driver_reviews' => $reviewData['driverReviews'],
                'one_star' => $reviewData['oneStar'],
                'two_star' => $reviewData['twoStar'],
                'three_star' => $reviewData['threeStar'],
                'four_star' => $reviewData['fourStar'],
                'five_star' => $reviewData['fiveStar'],
                'avg_rating' => $reviewData['avgRating'],
                'total_rating' => $reviewData['totalRating'],
                'reviews_count' => $reviewData['reviewsCount'],
                'total_review_count' => $reviewData['totalReviewCount'],
                'reviewed_by' => $reviewedBy
            ];
        }
        return [
            'driver' => $driver,
            'commonData' => $commonData,
            'otherData' => $otherData
        ];
    }

    private function driverRateInfo($driver)
    {
        //driver active rate/ day
        $timeLog = $driver->latestTrack;
        if (!empty($timeLog)) {
            $totalActiveTime = $driver->timeTrack()->sum('total_online') ?? 0;
            $totalActiveHour = $totalActiveTime / 60;
            $toDate = Carbon::parse($driver->created_at);
            $fromDate = Carbon::today();
            $days = $toDate->diffInDays($fromDate);
            $avgActiveRateByDay = (($totalActiveHour / ($days > 0 ? $days : 1)) / 24) * 100;
            $onlineHours = $timeLog['total_online'] / 60;
            $idleOnlineHours = $timeLog['total_idle'] / 60;
            $idleRateToday = ($idleOnlineHours / ($onlineHours > 0 ? $onlineHours : 1)) * 100;
        } else {
            $avgActiveRateByDay = 0;
            $idleRateToday = 0;
        }

        $driverTrips = $driver->driverTrips()
            ->where('driver_id', $driver->id)
            ->whereIn('current_status', ['completed', 'cancelled'])
            ->where('payment_status', PAID)
            ->get();

        $driverAvgEarning = ($driver?->userAccount?->received_balance + $driver?->userAccount?->receivable_balance) / (count($driverTrips) > 0 ? count($driverTrips) : 1);

        //Positive review rate
        $positiveReviewRate = $driver->receivedReviews()
            ->where('trip_type', 'ride_request')
            ->selectRaw('SUM(CASE WHEN rating IN (4,5) THEN 1 ELSE 0 END) / COUNT(*) * 100 AS positive_review_rate')
            ->value('positive_review_rate');

        //driver success rate
        $completedTrips = $driver->driverTrips()->where('current_status', 'completed')->count();
        $cancelledTrips = $driver->driverTrips()->where('current_status', 'cancelled')->count();
        $totalTrips = $driver->driverTrips()->whereIn('current_status', ['completed', 'cancelled', ONGOING])->count();

        $successRate = ($totalTrips > 0) ? (($totalTrips - $cancelledTrips) / $totalTrips) * 100 : 0;
        $cancelRate = ($totalTrips > 0) ? (($totalTrips - $completedTrips) / $totalTrips) * 100 : 0;

        return compact('idleRateToday', 'avgActiveRateByDay', 'driverAvgEarning',
            'positiveReviewRate', 'completedTrips', 'cancelledTrips', 'successRate', 'cancelRate');
    }

    private function reviewInformation($driver, $reviewedBy)
    {
        if ($reviewedBy == 'customer') {
            $avgRating = $driver->receivedReviews()->avg('rating');
            $totalRating = $driver->receivedReviews()->sum('rating');
            $reviewsCount = $driver->receivedReviews()->whereNotNull('feedback')->count();
            $totalReviewCount = $driver->receivedReviews()->count();
            $reviews = $driver->receivedReviews()
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            $oneStar = $reviews[1] ?? 0;
            $twoStar = $reviews[2] ?? 0;
            $threeStar = $reviews[3] ?? 0;
            $fourStar = $reviews[4] ?? 0;
            $fiveStar = $reviews[5] ?? 0;
        } else {
            $avgRating = $driver->givenReviews()->avg('rating');
            $totalRating = $driver->givenReviews()->sum('rating');
            $reviewsCount = $driver->givenReviews()->whereNotNull('feedback')->count();
            $totalReviewCount = $driver->givenReviews()->count();
            $reviews = $driver->givenReviews()
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            $oneStar = $reviews[1] ?? 0;
            $twoStar = $reviews[2] ?? 0;
            $threeStar = $reviews[3] ?? 0;
            $fourStar = $reviews[4] ?? 0;
            $fiveStar = $reviews[5] ?? 0;
        }

        $customerReviews = $driver->receivedReviews()->with(['givenUser', 'trip'])->latest()->paginate(paginationLimit());
        $driverReviews = $driver->givenReviews()->with(['trip'])->latest()->paginate(paginationLimit());

        return compact('customerReviews', 'driverReviews', 'oneStar', 'twoStar', 'threeStar', 'fourStar', 'fiveStar', 'avgRating',
            'totalRating', 'reviewsCount', 'totalReviewCount');
    }

    private function overview($driver)
    {
        $targetedAmountPoint = $targetedReviewPoint = $targetedCancelPoint = $targetedRidePoint = 0;
        // Calculate Morning time
        $totalMorningTime = $driver->timeLog()
            ->whereDate('created_at', Carbon::today())
            ->whereRaw("TIME(online) >= '06:00:00' AND TIME(offline) <= '11:59:59'")
            ->sum('online_time');


        // Calculate Midday time
        $totalMiddayTime = $driver->timeLog()
            ->whereDate('created_at', Carbon::today())
            ->whereRaw("TIME(online) >= '12:00:00' AND TIME(offline) <= '15:59:59'")
            ->sum('online_time');

        // Calculate Evening time
        $totalEveningTime = $driver->timeLog()
            ->whereDate('created_at', Carbon::today())
            ->whereRaw("TIME(online) >= '16:00:00' AND TIME(offline) <= '20:59:59'")
            ->sum('online_time');

        // Calculate Night time
        $totalNightTime = $driver->timeLog()
            ->whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->whereRaw("TIME(online) >= '21:00:00' AND TIME(offline) <= '23:59:59'");
            })
            ->orWhere(function ($query) {
                $query->whereRaw("TIME(online) >= '00:00:00' AND TIME(offline) <= '05:59:59'");
            })
            ->sum('online_time');


        //trip info of driver details
        $driverLowestFare = $driver->driverTrips()->whereIn('current_status', ['completed', 'cancelled'])->min('paid_fare');
        $driver_highest_fare = $driver->driverTrips()->whereIn('current_status', ['completed', 'cancelled'])->max('paid_fare');

        //driver details duty and review
        $total_active_min = $driver->timeTrack()->sum('total_online');
        $totalActiveHours = intdiv($total_active_min, 60) . ':' . ($total_active_min % 60);

        //driver level calculation
        $driverLevelPointGoal = $driver->level()->selectRaw('(targeted_ride_point + targeted_amount_point + targeted_cancel_point + targeted_review_point) as level_point')
            ->first()?->level_point;

        $driverLevel = $driver->level()->first();

        $driverLevelHistory = $driver->latestLevelHistory()->first();

        if (!empty($driverLevelHistory)) {
            if ($driverLevelHistory->ride_reward_status == 1) {
                $targetedRidePoint = $driverLevel->targeted_ride_point;
            } else {
                $targetedRidePoint = 0;
            }

            if ($driverLevelHistory->amount_reward_status == 1) {
                $targetedAmountPoint = $driverLevel->targeted_amount_point;
            } else {
                $targetedAmountPoint = 0;
            }

            if ($driverLevelHistory->cancellation_reward_status == 1) {
                $targetedCancelPoint = $driverLevel->targeted_cancel_point;
            } else {
                $targetedCancelPoint = 0;
            }

            if ($driverLevelHistory->reviews_reward_status == 1) {
                $targetedReviewPoint = $driverLevel->targeted_review_point;
            } else {
                $targetedReviewPoint = 0;
            }
        }

        return [
            'driver_highest_fare' => $driver_highest_fare,
            'driver_lowest_fare' => $driverLowestFare,
            'totalMorningTime' => $totalMorningTime,
            'totalMiddayTime' => $totalMiddayTime,
            'totalEveningTime' => $totalEveningTime,
            'totalNightTime' => $totalNightTime,
            'targeted_ride_point' => $targetedRidePoint,
            'targeted_amount_point' => $targetedAmountPoint,
            'targeted_cancel_point' => $targetedCancelPoint,
            'targeted_review_point' => $targetedReviewPoint,
            'driver_level_point_goal' => $driverLevelPointGoal,
            'total_active_hours' => $totalActiveHours,
        ];
    }

    public function getStatisticsData(array $data)
    {
        $whereBetweenCriteria = [];
        if (array_key_exists('date_range', $data) && $data['date_range'] != 'all_time') {
            $dateRange = getDateRange($data['date_range']);
            $whereBetweenCriteria = [
                'created_at' => [$dateRange['start'], $dateRange['end']],
            ];

        }
        $total = $this->userRepository->getBy(criteria: ['user_type' => DRIVER], whereBetweenCriteria: $whereBetweenCriteria)
            ->count();
        $active = $this->userRepository->getBy(criteria: ['user_type' => DRIVER, 'is_active' => true], whereBetweenCriteria: $whereBetweenCriteria)
            ->count();
        $inactive = $this->userRepository->getBy(criteria: ['user_type' => DRIVER, 'is_active' => false], whereBetweenCriteria: $whereBetweenCriteria)
            ->count();

        $relations = [
            'vehicle.category' => [
                ['type', '=', 'car'],
            ],
        ];
        $whereHasRelations = [
            'vehicle.category' => ['type' => 'car']
        ];
        $car = $this->userRepository->getBy(whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, relations: $relations)
            ->count();

        $relations = [
            'vehicle.category' => [
                ['type', '=', 'motor_bike'],
            ],
        ];
        $whereHasRelations = [
            'vehicle.category' => ['type' => 'motor_bike']
        ];
        $motorBike = $this->userRepository->getBy(whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, relations: $relations)
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'car' => $car,
            'motor_bike' => $motorBike
        ];
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy)->map(function ($item) {
            $count = 0;
            if (!is_null($item?->first_name)) {
                $count++;
            }
            if (!is_null($item?->last_name)) {
                $count++;
            }
            if (!is_null($item->email)) {
                $count++;
            }
            if (!is_null($item->phone)) {
                $count++;
            }
            if (!is_null($item->gender)) {
                $count++;
            }
            if (!is_null($item->identification_number)) {
                $count++;
            }
            if (!is_null($item->identification_type)) {
                $count++;
            }
            if (!is_null($item->identification_image)) {
                $count++;
            }
            if (!is_null($item->other_documents)) {
                $count++;
            }
            if (!is_null($item->date_of_birth)) {
                $count++;
            }
            if (!is_null($item->profile_image)) {
                $count++;
            }

            $ids = $item->driverTripsStatus->whereNotNull('completed')->pluck('trip_request_id');
            $earning = set_currency_symbol($item->userAccount->received_balance + $item->userAccount->total_withdrawn);

            return [
                'Id' => $item['id'],
                'Name' => $item['first_name'] . ' ' . $item['last_name'],
                'Email' => $item['email'],
                'Phone' => $item['phone'],
                'Profile Status' => round(($count / 11) * 100) . ' %',
                'Level' => $item?->level->name ?? 'No Level Attached',
                'Total Trip' => $item->driverTrips->count(),
                'Earning' => $earning,
                'Status' => $item['is_active'] ? 'Active' : 'Inactive',
            ];
        });
    }

    public function getDriverWithoutVehicle(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $data = [];
        if (array_key_exists('status', $criteria) && $criteria['status'] !== 'all') {
            $data['is_active'] = $criteria['status'] == 'active' ? 1 : 0;
        }
        $data['user_type'] = DRIVER;
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['first_name', 'last_name', 'email', 'phone'];
            $searchData['value'] = $criteria['search'];
        }
        return $this->userRepository->getDriverWithoutVehicle(criteria: $data, searchCriteria: $searchData, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery);
    }

    public function updateIdentityImage($id, array $data): ?Model
    {
        $driver = $this->userRepository->findOne(id: $id);
        if ($data['status'] == 'approved') {
            $driverData = [
                'old_identification_image' => null
            ];
        } else {
            $driverData = [
                'identification_image' => $driver?->old_identification_image,
                'old_identification_image' => null,
            ];
        }
        DB::beginTransaction();
        $driver = $this->userRepository->update(id: $id, data: $driverData);
        DB::commit();
        return $driver;
    }

    public function trashedData(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $data['user_type'] = DRIVER;
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['full_name', 'first_name', 'last_name', 'email', 'phone'];
            $searchData['value'] = $criteria['search'];
        }
        return $this->userRepository->getBy(criteria: $data, searchCriteria: $searchData, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, onlyTrashed: true, withCountQuery: $withCountQuery);
    }

    public function changeLanguage(int|string $id, array $data = []): ?Model
    {
        return $this->userRepository->update(id: $id, data: $data);
    }
}
