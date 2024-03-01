<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinates</title>
</head>
<body>
    <div>
        <label for="address">Enter Address:</label>
        <input type="text" id="address">
        <button onclick="getCoordinates()">Get Coordinates</button>
    </div>
    <div id="results">
        <h3>Google Maps Coordinates:</h3>
        <p id="googleCoords"></p>
        <h3>OpenStreetMap Coordinates:</h3>
        <p id="osmCoords"></p>
    </div>

    <script>
        function getCoordinates() {
            var address = document.getElementById("address").value;

            // Fetch coordinates from Google Maps API
            fetch(`https:maps.googleapis.com/maps/api/geocode/json?address=${address}&key=AIzaSyDUirsIqyiePbIXWA4mH6BlQozBPi_c1O8`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'OK') {
                    var location = data.results[0].geometry.location;
                    var googleCoords = `Latitude: ${location.lat}, Longitude: ${location.lng}`;
                    document.getElementById("googleCoords").textContent = googleCoords;
                } else {
                    document.getElementById("googleCoords").textContent = "Coordinates not found1";
                }
            })
            .catch(error => console.log('Error fetching Google Maps data:', error));

            // Fetch coordinates from OpenStreetMap API
            fetch(`https:nominatim.openstreetmap.org/search?q=${address}&format=json`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var osmCoords = `Latitude: ${data[0].lat}, Longitude: ${data[0].lon}`;
                    document.getElementById("osmCoords").textContent = osmCoords;
                } else {
                    document.getElementById("osmCoords").textContent = "Coordinates not found";
                }
            })
            .catch(error => console.log('Error fetching OpenStreetMap data:', error));
        }
    </script>
</body>
</html>

<?php

interface GeocodingService {
    public function getCoordinates($address);
}

class GoogleMapsGeocoding implements GeocodingService {
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getCoordinates($address) {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $this->apiKey;
        $data = json_decode(file_get_contents($url), true);
        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            return ['latitude' => $location['lat'], 'longitude' => $location['lng']];
        } else {
            return null;
        }
    }
}

class OpenStreetMapGeocoding implements GeocodingService {
    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json";
        $data = json_decode(file_get_contents($url), true);
        if (!empty($data)) {
            return ['latitude' => $data[0]['lat'], 'longitude' => $data[0]['lon']];
        } else {
            return null;
        }
    }
}
