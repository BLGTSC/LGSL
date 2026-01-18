<?php
// Start output buffering to capture any accidental text output
ob_start();

error_reporting(0);
ini_set('display_errors', 0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include data logic
require_once '../backend/mock_data.php';

// Get Server ID
$id = isset($_GET['id']) ? $_GET['id'] : null;
$server = getServerById($id);

// Check for GD Library
if (!function_exists('imagecreatetruecolor')) {
    ob_end_clean(); 
    die("GD Library not installed.");
}

// --- CONFIGURATION ---
$width = 560;
$height = 95;
$img = imagecreatetruecolor($width, $height);

// --- COLORS ---
$bg_dark = imagecolorallocate($img, 10, 5, 5); 
$bg_red = imagecolorallocate($img, 150, 0, 0); 
$text_white = imagecolorallocate($img, 255, 255, 255);
$text_red = imagecolorallocate($img, 255, 50, 50);
$border_color = imagecolorallocate($img, 255, 0, 0);
$grid_color = imagecolorallocate($img, 50, 0, 0);
$graph_line = imagecolorallocate($img, 255, 50, 50);
$black = imagecolorallocate($img, 0, 0, 0);
$green = imagecolorallocate($img, 0, 255, 0);

// --- BACKGROUND ---
for ($i = 0; $i < $width; $i++) {
    $red_val = max(0, 100 - ($i / 3)); 
    $col = imagecolorallocate($img, (int)$red_val, 0, 0);
    imageline($img, $i, 0, $i, $height, $col);
}

// --- CONTENT ---
if ($server) {
    $x_start = 10;
    
    // Name
    imagestring($img, 4, $x_start, 10, strtoupper($server['name']), $text_white);

    // IP & Status
    imagestring($img, 2, $x_start, 30, "IP: " . $server['ip'] . ":" . $server['port'], $text_white);
    
    $statusText = $server['status'];
    $statusColor = ($server['status'] === 'ONLINE') ? $green : $text_red;
    imagestring($img, 2, $x_start, 45, "STATUS: " . $statusText, $statusColor);

    // Map & Players
    imagestring($img, 2, $x_start, 60, "MAP: " . substr($server['map'], 0, 15), $text_white);
    imagestring($img, 2, $x_start + 150, 60, "PLAYERS: " . $server['players'] . "/" . $server['max_players'], $text_white);
    imagestring($img, 2, $x_start, 75, "RANK: #" . $server['rank'], $text_white);
    imagestring($img, 2, $x_start + 150, 75, "VOTES: " . $server['votes'], $text_white);

    // Graph Placeholder (Right side)
    imagerectangle($img, 350, 10, 550, 85, $text_red);
    imagestring($img, 1, 355, 15, "PLAYER HISTORY (24H)", $text_white);
    // Draw simple line
    imageline($img, 350, 85, 550, 45, $graph_line);

} else {
    imagestring($img, 5, 200, 40, "SERVER NOT FOUND", $text_red);
}

// Border
imagerectangle($img, 0, 0, $width-1, $height-1, $border_color);

// Final Output
ob_end_clean(); // Discard any text output (errors, spaces)
header("Content-type: image/png");
imagepng($img);
imagedestroy($img);
?>