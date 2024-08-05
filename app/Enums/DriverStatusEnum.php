<?php

namespace App\Enums;

enum DriverStatusEnum:string
{
    case OnBidding = 'on_bidding';
    case OnTrip = 'on_trip';
    case Available = 'available';
    case UnAvailable = 'unavailable';
}
