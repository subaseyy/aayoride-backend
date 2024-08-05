<?php

namespace Modules\TripManagement\Lib;

use Carbon\Carbon;
use Modules\TripManagement\Entities\FareBidding;
use Modules\TripManagement\Entities\TripRequestFee;
use Modules\TripManagement\Entities\TripRequestTime;

trait CommonTrait
{
    use DiscountCalculationTrait, CouponCalculationTrait;
    public function calculateFinalFare($trip, $fare): array
    {
        $admin_trip_commission = (double)get_cache('trip_commission') ?? 0;
        // parcel start
        if ($trip->type == 'parcel') {

            $vat_percent = (double)get_cache('vat_percent') ?? 1;
            $actual_fare =  $trip->actual_fare / (1 + ($vat_percent / 100));
            $parcel_payment = $actual_fare;
            $vat = round(($vat_percent * $parcel_payment) / 100, 2);
            $fee = TripRequestFee::where('trip_request_id', $trip->id)->first();
            $fee->vat_tax = $vat;
            $fee->admin_commission = (($parcel_payment * $admin_trip_commission) / 100) + $vat;
            $fee->save();

            return [
                'actual_fare' => round($actual_fare, 2),
                'final_fare' => round($parcel_payment + $vat, 2),
                'waiting_fee' => 0,
                'idle_fare' => 0,
                'cancellation_fee' => 0,
                'delay_fee' => 0,
                'vat' => $vat,
                'actual_distance' => $trip->estimated_distance,
            ];
        }

        $fee = TripRequestFee::query()->firstWhere('trip_request_id', $trip->id);
        $time = TripRequestTime::query()->firstWhere('trip_request_id', $trip->id);

        $bid_on_fare = FareBidding::where('trip_request_id', $trip->id)->where('is_ignored', 0)->first();
        $current_status = $trip->current_status;
        $cancellation_fee = 0;
        $waiting_fee = 0;
        $distance_in_km = 0;

        $drivingMode = $trip?->vehicleCategory?->type === 'motor_bike' ? 'TWO_WHEELER' : 'DRIVE';
        $drop_coordinate = [
            $trip->coordinate->drop_coordinates->latitude,
            $trip->coordinate->drop_coordinates->longitude
        ];
        $destination_coordinate = [
            $trip->coordinate->destination_coordinates->latitude,
            $trip->coordinate->destination_coordinates->longitude
        ];
        $pickup_coordinate = [
            $trip->coordinate->pickup_coordinates->latitude,
            $trip->coordinate->pickup_coordinates->longitude
        ];
        $intermediate_coordinate = [];
        if ($trip->coordinate->is_reached_1) {
            if ($trip->coordinate->is_reached_2) {
                $intermediate_coordinate[1] = [
                    $trip->coordiante->int_coordinate_2->latitude,
                    $trip->coordiante->int_coordinate_2->longitude
                ];
            }
            $intermediate_coordinate[0] = [
                $trip->coordiante->int_coordinate_1->latitude,
                $trip->coordiante->int_coordinate_1->longitude
            ];
        }

        if ($current_status === 'cancelled') {
            $route = getRoutes($pickup_coordinate, $drop_coordinate, $intermediate_coordinate, [$drivingMode]);

            $distance_in_km = $route[0]['distance'];
            $distance_wise_fare_cancelled = $fare->base_fare_per_km * $distance_in_km;

            $actual_fare = $fare->base_fare + $distance_wise_fare_cancelled;

            if ($trip->fee->cancelled_by === 'customer') {
                $cancellation_percent = $fare->cancellation_fee_percent;
                $cancellation_fee = max((($cancellation_percent * $distance_wise_fare_cancelled) / 100), $fare->min_cancellation_fee);
            }
        } elseif ($current_status == 'completed') {
            $haversine = haversineDistance(
                $trip->coordinate->drop_coordinates->latitude,
                $trip->coordinate->drop_coordinates->longitude,
                $trip->coordinate->destination_coordinates->longitude,
                $trip->coordinate->destination_coordinates->longitude,
            );

            // when drop location is 100 meters more than than destination
//            if ($haversine > (double)get_cache('driver_completion_radius') ?? 0) {
                //when bidding is on finding the distance between requested destination and actual drop coordinates

                $route = getRoutes(
                    $pickup_coordinate,
                    $drop_coordinate,
                    $intermediate_coordinate,
                    [$drivingMode],
                );
                $distance_in_km = $route[0]['distance'];

//            } // when drop location is 100 meters less than than destination
//            else {
//                $distance_in_km = (double)$trip->estimated_distance;
//            }
            $distance_wise_fare_completed = $fare->base_fare_per_km * $distance_in_km;
            $vat_percent = (double)get_cache('vat_percent') ?? 1;
            $distanceFare = $trip->rise_request_count>0? $trip->actual_fare / (1 + ($vat_percent / 100)) : $fare->base_fare + $distance_wise_fare_completed;
            $actual_fare = $bid_on_fare ? $bid_on_fare->bid_fare/ (1 + ($vat_percent / 100)) : $distanceFare;
        } else {
            $actual_fare = 0;
        }


        $trip_started = Carbon::parse($trip->tripStatus->ongoing);
        $trip_ended = Carbon::parse($trip->tripStatus->$current_status);
        $actual_time = $trip_started->diffInMinutes($trip_ended);

        //        Idle time & fee calculation
        $idle_fee_buffer = (double)get_cache('idle_fee') ?? 0;
        $idle_diff = $trip->time->idle_time - $idle_fee_buffer;
        $idle_time = max($idle_diff, 0);
        $idle_fee = $idle_time * $fare->idle_fee_per_min;

        //        Delay time & fee calculation
        $delay_fee_buffer = (double)get_cache('delay_fee') ?? 0;
        $delay_diff = $actual_time - ($trip->time->estimated_time + $delay_fee_buffer + $trip->time->idle_time);
        $delay_time = max($delay_diff, 0);
        $delay_fee = $delay_time * $fare->trip_delay_fee_per_min;


        $vat_percent = (double)get_cache('vat_percent') ?? 1;
        $final_fare_without_tax = ($actual_fare + $waiting_fee + $idle_fee + $cancellation_fee + $delay_fee);
        $vat = ($final_fare_without_tax * $vat_percent) / 100;

        $fee->vat_tax = round($vat, 2);
        $fee->admin_commission = (($final_fare_without_tax * $admin_trip_commission) / 100) + $vat;
        $fee->cancellation_fee = round($cancellation_fee, 2);
        $time->actual_time = $actual_time;
        $time->idle_time = $idle_time;
        $fee->idle_fee = round($idle_fee, 2);
        $time->delay_time = $delay_time;
        $fee->delay_fee = round($delay_fee, 2);
        $fee->save();
        $time->save();

        return [
            'actual_fare' => round($actual_fare, 2),
            'final_fare' => round($final_fare_without_tax + $vat, 2),
            'waiting_fee' => $waiting_fee,
            'idle_fare' => $idle_fee,
            'cancellation_fee' => $cancellation_fee,
            'delay_fee' => $delay_fee,
            'vat' => $vat,
            'actual_distance' => $distance_in_km
        ];
    }


    public function estimatedFare($tripRequest, $routes, $zone_id, $tripFare = null, $area_id = null): mixed
    {
        if ($tripRequest['type'] == 'parcel') {
            abort_if(boolean: empty($tripFare), code: 403, message: translate('invalid_or_missing_information'));
            abort_if(boolean: empty($tripFare->fares), code: 403, message: translate('no_fares_found'));

            $distance_wise_fare = $tripFare->fares[0]->fare_per_km * $routes[0]['distance'];
            $est_fare = $tripFare->fares[0]->base_fare + $distance_wise_fare;
            $user = auth('api')->user();
            $discount = $this->getEstimatedDiscount(user: $user,zoneId: $zone_id, tripType: $tripRequest['type'], vechileCategoryId: null,estimatedAmount: $est_fare);
            $vat_percent = (double)get_cache('vat_percent') ?? 1;
            $discountEstFare = $est_fare-($discount?$discount['discount_amount']:0);
            $coupon = $this->getEstimatedCouponDiscount(user: $user,vechileCategoryId:null,estimatedAmount:$discountEstFare);
            $discountFareVat = ($discountEstFare * $vat_percent) / 100;
            $discountEstFare += $discountFareVat;
            $vat = ($est_fare * $vat_percent) / 100;
            $est_fare += $vat;
            $points = (int)getSession('currency_decimal_point') ?? 0;
            $estimated_fare = [
                'id' => $tripFare->id,
                'zone_id' => $zone_id,
                'area_id' => $area_id,
                'base_fare' => $tripFare->base_fare,
                'base_fare_per_km' => $tripFare->base_fare_per_km,
                'fare' => $tripFare->fares,
                'estimated_distance' => (double)$routes[0]['distance'],
                'estimated_duration' => $routes[0]['duration'],
                'estimated_fare' => round($est_fare, $points),
                'discount_fare' => round($discountEstFare, $points),
                'discount_amount' => round(($discount?$discount['discount_amount']:0), $points),
                'coupon_applicable' => $coupon,
                'request type' => $tripRequest['type'],
                'encoded_polyline' => $routes[0]['encoded_polyline']
            ];

        } else {

            $estimated_fare = $tripFare->map(function ($trip) use ($routes, $tripRequest, $area_id) {
                foreach ($routes as $route) {
                    if ($route['drive_mode'] === 'DRIVE') {
                        $distance = $route['distance'];
                        $drive_fare = $trip->base_fare_per_km * $distance;
                        $drive_est_distance = (double)$routes[0]['distance'];
                        $drive_est_duration = $route['duration'];
                        $drive_polyline = $route['encoded_polyline'];
                    } elseif ($route['drive_mode'] === 'TWO_WHEELER') {
                        $distance = $route['distance'];
                        $bike_fare = $trip->base_fare_per_km * $distance;
                        $bike_est_distance = (double)$routes[0]['distance'];
                        $bike_est_duration = $route['duration'];
                        $bike_polyline = $route['encoded_polyline'];
                    }
                }
                $points = (int)getSession('currency_decimal_point') ?? 0;
                $est_fare = $trip->vehicleCategory->type === 'car' ? round(($trip->base_fare + $drive_fare), $points) : round(($trip->base_fare + $bike_fare), $points);
                $user = auth('api')->user();
                $discount = $this->getEstimatedDiscount(user: $user,zoneId: $trip->zone_id, tripType: $tripRequest['type'], vechileCategoryId: $trip->vehicleCategory->id,estimatedAmount: $est_fare);
                $vat_percent = (double)get_cache('vat_percent') ?? 1;
                $discountEstFare = $est_fare-($discount?$discount['discount_amount']:0);
                $coupon = $this->getEstimatedCouponDiscount(user: $user,vechileCategoryId:$trip->vehicleCategory->id,estimatedAmount:$discountEstFare);
                $discountFareVat = ($discountEstFare * $vat_percent) / 100;
                $discountEstFare += $discountFareVat;
                $vat = ($est_fare * $vat_percent) / 100;
                $est_fare += $vat;

                return [
                    "id" => $trip->id,
                    "zone_id" => $trip->zone_id,
                    'area_id' => $area_id,
                    "vehicle_category_id" => $trip->vehicle_category_id,
                    'base_fare' => $trip->base_fare,
                    'base_fare_per_km' => $trip->base_fare_per_km,
                    'fare' => $trip->VehicleCategory->type === 'car' ? round($drive_fare, 2) : round($bike_fare, 2),
                    'estimated_distance' => $trip->VehicleCategory->type === 'car' ? $drive_est_distance : $bike_est_distance,
                    'estimated_duration' => $trip->VehicleCategory->type === 'car' ? $drive_est_duration : $bike_est_duration,
                    'vehicle_category_type' => $trip->VehicleCategory->type === 'car' ? 'Car' : 'Motorbike',
                    'estimated_fare' => round($est_fare, $points),
                    'discount_fare' => round($discountEstFare, $points),
                    'discount_amount' => round(($discount?$discount['discount_amount']:0), $points),
                    'coupon_applicable' => $coupon,
                    'request_type' => $tripRequest['type'],
                    'encoded_polyline' => $trip->VehicleCategory->type === 'car' ? $drive_polyline : $bike_polyline,
                ];

            });

        }

        return $estimated_fare;
    }

}
