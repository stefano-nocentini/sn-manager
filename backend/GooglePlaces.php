<?php

require_once 'base_model.php';

class GooglePlaces extends BaseModel {
    private $apiKey;

    public function __construct($apiKey, $db) {
        parent::__construct($db);
        $this->apiKey = $apiKey;
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM esercizi_commerciali WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all() {
        $result = $this->conn->query("SELECT * FROM esercizi_commerciali");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($data) {
        // Implementa il metodo create se necessario
        throw new Exception("Metodo non implementato.");
    }

    public function update($id, $data) {
        // Implementa il metodo update se necessario
        throw new Exception("Metodo non implementato.");
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM esercizi_commerciali WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function searchNearby($latitude, $longitude, $type, $keyword = '') {
        $endpoint = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
    
        $params = [
            'location' => $latitude . ',' . $longitude,
            'rankby' => 'distance',
            'type' => $type,
            'key' => $this->apiKey
        ];
    
        if (!empty($keyword)) {
            $params['keyword'] = $keyword;
        }
    
        $url = $endpoint . '?' . http_build_query($params);
    
        $response = $this->makeRequest($url);
    
        if ($response && $response['status'] === 'OK') {
            return $this->formatResults($response['results'], $latitude, $longitude);
        } else {
            return [
                'error' => $response['status'] ?? 'Unknown error',
                'message' => $response['error_message'] ?? 'No details available'
            ];
        }
    }              

    private function makeRequest($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        return null;
    }

    private function formatResults($results, $originLat, $originLon) {
        $formattedResults = [];
    
        foreach ($results as $result) {
            $distance = isset($result['geometry']['location']) ?
                $this->calculateDistance(
                    $originLat,
                    $originLon,
                    $result['geometry']['location']['lat'],
                    $result['geometry']['location']['lng']
                ) : 'N/A';
    
            if ($distance !== 'N/A') {
                // Converti la distanza in metri se inferiore a 1 km
                $distance = $distance < 1 ? round($distance * 1000) . ' m' : round($distance, 2) . ' km';
            }
    
            $formattedResults[] = [
                'name' => $result['name'] ?? '',
                'address' => $result['vicinity'] ?? '',
                'latitude' => $result['geometry']['location']['lat'] ?? null,
                'longitude' => $result['geometry']['location']['lng'] ?? null,
                'rating' => $result['rating'] ?? null,
                'user_ratings_total' => $result['user_ratings_total'] ?? 0,
                'photo_reference' => $result['photos'][0]['photo_reference'] ?? null,
                'place_id' => $result['place_id'] ?? '',
                'open_now' => $result['opening_hours']['open_now'] ?? null,
                'distance' => $distance,
            ];
        }
    
        return $formattedResults;
    }       

    public function getCoordinatesFromAddress($address) {
        $endpoint = "https://maps.googleapis.com/maps/api/geocode/json";
        
        $params = [
            'address' => $address,
            'key' => $this->apiKey
        ];
        
        $url = $endpoint . '?' . http_build_query($params);
        $response = $this->makeRequest($url);

        if ($response && $response['status'] === 'OK') {
            $location = $response['results'][0]['geometry']['location'];
            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng']
            ];
        }
        
        return [
            'error' => 'Unable to retrieve coordinates',
            'message' => $response['error_message'] ?? 'No details available'
        ];
    }

    public function searchNearbyByAddress($address, $type, $keyword = '') {
        $coordinates = $this->getCoordinatesFromAddress($address);
    
        if (isset($coordinates['latitude'], $coordinates['longitude'])) {
            return $this->searchNearby($coordinates['latitude'], $coordinates['longitude'], $type, $keyword);
        }

        return [
            'error' => 'Unable to retrieve coordinates',
            'message' => 'Invalid address or coordinates not found'
        ];
    }        

    public function saveResult($cantiereId, $result) {
        $query = "INSERT INTO esercizi_commerciali (
                    cantiere_id, nome, indirizzo, latitudine, longitudine, 
                    rating, user_ratings_total, foto_referenza, place_id, aperto
                  ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                  )";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param(
            'isssdiissb',
            $cantiereId,
            $result['name'],
            $result['address'],
            $result['latitude'],
            $result['longitude'],
            $result['rating'],
            $result['user_ratings_total'],
            $result['photo_reference'],
            $result['place_id'],
            $result['open_now']
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore durante il salvataggio dei dati: " . $stmt->error);
        }
    }

    public function saveResults($cantiereId, $results) {
        foreach ($results as $result) {
            $this->saveResult($cantiereId, $result);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Raggio della Terra in km
    
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
    
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        return $earthRadius * $c; // Distanza in km
    }
    
}