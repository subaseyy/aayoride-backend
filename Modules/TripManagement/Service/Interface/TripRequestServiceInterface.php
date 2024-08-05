<?php

namespace Modules\TripManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface TripRequestServiceInterface extends BaseServiceInterface
{
    public function getAnalytics($dateRange): mixed;

    public function couponRuleValidate($coupon, $pickupCoordinates, $vehicleCategoryId): ?array;

    public function statusWiseTotalTripRecords(array $attributes);

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []);

    public function getAdminZoneWiseStatistics(array $data);

    public function getAdminZoneWiseEarning(array $data);

    public function getLeaderBoard(array $data, $limit = null, $offset = null);

    public function storeTrip(array $attributes): Model;

    public function pendingParcelList(array $attributes);


    public function updateRelationalTable($attributes);

    public function findOneWithAvg(array $criteria = [], array $relations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false, array $withAvgRelation = []): ?Model;

    public function getWithAvg(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $withAvgRelation = [], array $whereBetweenCriteria = [], array $whereNotNullCriteria = []): Collection|LengthAwarePaginator;

    public function getPendingRides($attributes): mixed;

    public function makeRideRequest($request, $pickupCoordinates): mixed;

    public function findNearestDriver($latitude, $longitude, $zoneId, $radius = 5, $vehicleCategoryId = null): mixed;

    public function validateDiscount($trip, $response, $tripId, $cuponId);

    public function handleCancelledTrip($trip, $attributes, $tripId);

    public function handleCompletedTrip($trip, $request, $attributes);

    public function handleCustomerRideStatusUpdate($trip, $request, $attributes);

    public function removeCouponData($trip);

    public function getCustomerIncompleteRide(): mixed;

    public function handleDriverStatusUpdate($request, $trip);

    public function getDriverIncompleteRide(): mixed;

    public function handleRequestActionPushNotification($trip, $user);

    public function getTripOverview($data);


}
