<?php
// Use __DIR__ to ensure relative paths work correctly
require_once __DIR__ . '/query.php';

define('DATA_FILE', __DIR__ . '/servers.json');

// --- HELPER FUNCTIONS FOR JSON STORAGE ---

function _loadData() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function _saveData($data) {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// --- MAIN FUNCTIONS ---

function getMockServers() {
    $servers = _loadData();
    
    // LIVE QUERY LOGIC
    $updated = false;
    
    foreach ($servers as $k => $s) {
        $supportedGames = ['Counter-Strike', 'Rust', 'Team Fortress', 'Minecraft'];
        $isSupported = false;
        
        if(isset($s['game'])) {
            foreach($supportedGames as $g) {
                if(strpos($s['game'], $g) !== false) {
                    $isSupported = true;
                }
            }
        }

        if ($isSupported) {
            if (class_exists('QueryHandler')) {
                try {
                    // Suppress errors for query to prevent crashes
                    $liveData = @QueryHandler::query($s['ip'], $s['port'], $s['game']);
                    
                    if ($liveData && isset($liveData['status']) && $liveData['status'] === 'ONLINE') {
                        $servers[$k]['players'] = (int)$liveData['players'];
                        $servers[$k]['max_players'] = (int)$liveData['max_players'];
                        $servers[$k]['map'] = htmlspecialchars($liveData['map']);
                        $servers[$k]['status'] = 'ONLINE';
                        if(!empty($liveData['name'])) {
                            $servers[$k]['name'] = htmlspecialchars($liveData['name']);
                        }
                        $servers[$k]['player_list'] = isset($liveData['player_list']) ? $liveData['player_list'] : [];
                    } 
                } catch (Exception $e) {
                    // Ignore query errors
                }
            }
        }
    }

    // Sort by Votes
    usort($servers, function($a, $b) { 
        $va = isset($a['votes']) ? (int)$a['votes'] : 0;
        $vb = isset($b['votes']) ? (int)$b['votes'] : 0;
        return $vb - $va; 
    });
    
    // Assign Ranks
    $ranked = [];
    foreach ($servers as $index => $server) { 
        $server['rank'] = $index + 1;
        $ranked[] = $server;
    }

    return $ranked;
}

function getServerById($id) {
    $servers = getMockServers(); 
    foreach($servers as $s) {
        if(isset($s['id']) && $s['id'] === $id) {
            return $s;
        }
    }
    return null;
}

function addServerToJson($newServer) {
    $servers = _loadData();
    // Ensure ID is unique
    $newServer['id'] = 's_' . time() . '_' . rand(100,999);
    $newServer['votes'] = 0;
    $servers[] = $newServer;
    _saveData($servers);
    return true;
}

function voteForServer($id) {
    if (session_status() == PHP_SESSION_NONE) session_start();

    // 1. Check Cooldown
    $lastVoteKey = 'vote_timestamp_' . $id;
    if (isset($_SESSION[$lastVoteKey])) {
        $lastVoteTime = $_SESSION[$lastVoteKey];
        if (time() - $lastVoteTime < 24 * 3600) {
            return ['success' => false, 'message' => 'You have already voted for this server in the last 24 hours.'];
        }
    }

    // 2. Find and Increment
    $servers = _loadData();
    $found = false;
    foreach ($servers as $k => $s) {
        if ($s['id'] === $id) {
            if(!isset($servers[$k]['votes'])) $servers[$k]['votes'] = 0;
            $servers[$k]['votes']++;
            $found = true;
            break;
        }
    }

    if ($found) {
        _saveData($servers);
        $_SESSION[$lastVoteKey] = time();
        return ['success' => true, 'message' => 'Vote recorded successfully!'];
    }

    return ['success' => false, 'message' => 'Server not found.'];
}

function getCountryFlag($code) {
    $flags = [ 'RO' => '🇷🇴', 'US' => '🇺🇸', 'DE' => '🇩🇪', 'FR' => '🇫🇷' ];
    return isset($flags[$code]) ? $flags[$code] : '🌐';
}

function getGameTypes() {
    return ['Counter-Strike 2', 'Counter-Strike 1.6', 'Minecraft', 'Rust', 'GTA V / FiveM'];
}
?>