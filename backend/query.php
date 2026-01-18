<?php
class QueryHandler {
    /**
     * Query a server to get real-time info.
     */
    public static function query($ip, $port, $game) {
        $info = self::queryValveInfo($ip, $port);
        
        if ($info) {
            // If online, try to get player list too
            $players = self::queryValvePlayers($ip, $port);
            $info['player_list'] = $players;
            return $info;
        }

        return [
            'status' => 'OFFLINE',
            'players' => 0,
            'max_players' => 0,
            'map' => 'offline',
            'name' => 'Offline',
            'player_list' => []
        ];
    }

    private static function queryValveInfo($ip, $port) {
        $packet = "\xFF\xFF\xFF\xFFTSource Engine Query\x00";
        $fp = fsockopen("udp://".$ip, $port, $errno, $errstr, 2);
        if (!$fp) return null;

        stream_set_timeout($fp, 1, 0);
        fwrite($fp, $packet);
        $data = fread($fp, 4096);
        fclose($fp);

        if (!$data) return null;

        $header = substr($data, 0, 4);
        if ($header !== "\xFF\xFF\xFF\xFF") return null;

        $type = ord($data[4]);
        $idx = 5;

        if ($type === 0x49) { 
            $protocol = ord($data[$idx++]);
            $name = self::readString($data, $idx);
            $map = self::readString($data, $idx);
            $folder = self::readString($data, $idx);
            $gameName = self::readString($data, $idx);
            $appId = self::readInt16($data, $idx);
            $players = ord($data[$idx++]);
            $maxPlayers = ord($data[$idx++]);
            
            return [
                'status' => 'ONLINE',
                'name' => $name,
                'map' => $map,
                'players' => $players,
                'max_players' => $maxPlayers
            ];
        } 
        elseif ($type === 0x6D) {
            $address = self::readString($data, $idx);
            $hostName = self::readString($data, $idx);
            $map = self::readString($data, $idx);
            $folder = self::readString($data, $idx);
            $gameName = self::readString($data, $idx);
            $players = ord($data[$idx++]);
            $maxPlayers = ord($data[$idx++]);

            return [
                'status' => 'ONLINE',
                'name' => $hostName,
                'map' => $map,
                'players' => $players,
                'max_players' => $maxPlayers
            ];
        }

        return null;
    }

    private static function queryValvePlayers($ip, $port) {
        $fp = fsockopen("udp://".$ip, $port, $errno, $errstr, 2);
        if (!$fp) return [];
        stream_set_timeout($fp, 1, 0);

        // Step 1: Get Challenge
        // Header: 0x55 (U)
        fwrite($fp, "\xFF\xFF\xFF\xFF\x55\xFF\xFF\xFF\xFF");
        $data = fread($fp, 4096);
        
        if(!$data) { fclose($fp); return []; }
        
        // Parse Challenge
        // Response format: FF FF FF FF 41 [4 bytes challenge]
        if (ord($data[4]) === 0x41) { // 'A'
            $challenge = substr($data, 5, 4);
            
            // Step 2: Send Request with Challenge
            fwrite($fp, "\xFF\xFF\xFF\xFF\x55" . $challenge);
            $data = fread($fp, 4096);
            
            if(!$data) { fclose($fp); return []; }

            // Step 3: Parse Players
            // Header: 0x44 ('D')
            if (ord($data[4]) === 0x44) {
                $playerList = [];
                $idx = 5;
                $numPlayers = ord($data[$idx++]);
                
                for ($i = 0; $i < $numPlayers; $i++) {
                    if ($idx >= strlen($data)) break;
                    
                    $playerIndex = ord($data[$idx++]); // Index (mostly unused)
                    $playerName = self::readString($data, $idx);
                    $score = self::readInt32($data, $idx);
                    $duration = self::readFloat($data, $idx);

                    if(!empty($playerName)) {
                        $playerList[] = [
                            'name' => $playerName,
                            'score' => $score,
                            'time' => self::formatDuration($duration)
                        ];
                    }
                }
                
                // Sort by score
                usort($playerList, function($a, $b) {
                    return $b['score'] - $a['score'];
                });

                fclose($fp);
                return $playerList;
            }
        }
        
        fclose($fp);
        return [];
    }

    private static function readString($data, &$idx) {
        $len = strlen($data);
        $str = '';
        while ($idx < $len && $data[$idx] !== "\x00") {
            $str .= $data[$idx];
            $idx++;
        }
        $idx++; 
        return utf8_encode($str); // Ensure UTF-8
    }

    private static function readInt16($data, &$idx) {
        if ($idx + 2 > strlen($data)) return 0;
        $v = unpack('v', substr($data, $idx, 2));
        $idx += 2;
        return $v[1];
    }

    private static function readInt32($data, &$idx) {
        if ($idx + 4 > strlen($data)) return 0;
        $v = unpack('l', substr($data, $idx, 4));
        $idx += 4;
        return $v[1];
    }

    private static function readFloat($data, &$idx) {
        if ($idx + 4 > strlen($data)) return 0;
        $v = unpack('f', substr($data, $idx, 4));
        $idx += 4;
        return $v[1];
    }

    private static function formatDuration($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = floor($seconds % 60);
        return sprintf("%02d:%02d:%02d", $h, $m, $s);
    }
}