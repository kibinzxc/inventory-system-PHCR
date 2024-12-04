<?php
function getCoordinates($address, $apiKey)
{
    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($address) . "&key=" . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status']['code'] == 200 && !empty($data['results'])) {
        $lat = $data['results'][0]['geometry']['lat'];
        $lng = $data['results'][0]['geometry']['lng'];
        return ['lat' => $lat, 'lng' => $lng];
    }

    return null;
}

function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;

    return $distance; // distance in kilometers
}

$apiKey = '9f368e104b744ddab127fb3cbcf84673';
$address1 = '64B, don carlos revilla st, pasay city, 1300';
$address2 = ' 2116 Chino Roces Ave, Cor Dela Rosa Street, Pio, Makati, Metro Manila, Philippines';

$coords1 = getCoordinates($address1, $apiKey);
$coords2 = getCoordinates($address2, $apiKey);

if ($coords1 && $coords2) {
    $distance = haversineDistance($coords1['lat'], $coords1['lng'], $coords2['lat'], $coords2['lng']);
    if ($distance > 5) {
        echo "The delivery address is too far. The maximum range is 5 km.";
    } else {
        echo "The distance between the addresses is: " . $distance . " km.";
    }
} else {
    echo "Could not get coordinates.";
}
