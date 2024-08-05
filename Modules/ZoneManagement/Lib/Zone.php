<?php

if (!function_exists('formatCoordinates')) {
    function formatCoordinates($coordinates)
    {
        $data = [];
        foreach ($coordinates as $coordinate) {
            $data[] = (object)['lat' => $coordinate[1], 'lng' => $coordinate[0]];
        }
        return $data;
    }
}
if (!function_exists('formatZoneCoordinates')) {
    function formatZoneCoordinates($zones)
    {
        $data = [];
        foreach($zones as $zone)
        {
            $area = json_decode($zone->coordinates[0]->toJson(),true);
            $data[] = formatCoordinates(coordinates: $area['coordinates']);
        }
        return $data;
    }
}
