<?php
// backend/counter.php

function trackVisitor() {
    $file = __DIR__ . '/visitors.json';
    
    // Default structure
    $data = ['count' => 0, 'ips' => []];
    
    // Load existing data if file exists
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
    
    // Get User IP
    $userIP = $_SERVER['REMOTE_ADDR'];
    // Hash the IP for basic privacy and consistent storage length
    $ipHash = md5($userIP);
    
    // Check if this IP is unique (has not visited before)
    // Note: For a very large site, you would rotate this list or use a database. 
    // For this project, a JSON array is fine.
    if (!in_array($ipHash, $data['ips'])) {
        $data['ips'][] = $ipHash;
        $data['count']++;
        
        // Save updated data with Lock to prevent corruption
        file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    return $data['count'];
}
?>