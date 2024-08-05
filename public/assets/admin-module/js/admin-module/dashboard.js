"use strict";

$('#zone_wise_ride').on('change', function () {
    zoneWiseTripStatistics(document.getElementById('area_wise_ride_date').value, this.value)

    if (this.value != 'all') {
        loadMapLater(this.value)
    } else if (this.value == 'all') {
        loadAllZone()
    }
})
