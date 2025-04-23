<?php
header('Content-Type: application/json');
// Disable error output - very important to prevent PHP errors from breaking JSON
// error_reporting(0);
// ini_set('display_errors', 0);
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
// Include dependencies
require_once '../config.php';
require_once __DIR__ . '/../../config/db.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => false,
        'message' => 'Method not allowed',
        'data' => null
    ]);
    exit();
}

// Get auth token
$token = getBearerToken();
if (!$token) {
    echo json_encode([
        'status' => false,
        'message' => 'Authorization token required',
        'data' => null
    ]);
    exit();
}

// Get user information including role
$userSql = "SELECT id, role FROM users WHERE token = ? AND token_expiry > NOW()";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $token);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid or expired token',
        'data' => null
    ]);
    exit();
}

// Get the user data
$userData = $userResult->fetch_assoc();
$user_id = $userData['id'];
$user_type = $userData['role']; // Map role to user_type

// Get post data
$log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;
$location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : 0;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

//For postman
// $input = json_decode(file_get_contents('php://input'), true); // decode JSON to associative array

// $log_id = isset($input['log_id']) ? intval($input['log_id']) : 0;
// $location_id    = isset($input['location_id']) ? floatval($input['location_id']) : 0;
// $latitude   = isset($input['latitude']) ? floatval($input['latitude']) : 0;
// $longitude   = isset($input['longitude']) ? floatval($input['longitude']) : 0;
// $notes   = isset($input['notes']) ? floatval($input['notes']) : 0;


       file_put_contents('distance_log_punch_out.txt', json_encode([
        'log_id' => $log_id,
        'location_id' => $location_id,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'notes' => $notes
    ], JSON_PRETTY_PRINT), FILE_APPEND);
// Check if log record exists and belongs to the user
$checkSql = "SELECT * FROM attendance_logs 
            WHERE id = ? 
            AND user_id = ? 
            AND user_type = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("iis", $log_id, $user_id, $user_type);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid punch in record',
        'data' => null
    ]);
    exit();
}

$log = $checkResult->fetch_assoc();

// Check if already punched out
if ($log['punch_out_time'] !== null) {
    echo json_encode([
        'status' => false,
        'message' => 'You have already punched out for this record',
        'data' => null
    ]);
    exit();
}

// Check if geofencing is enabled
$geofencingEnabled = false;
$settingsSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'geofencing_enabled'";
$settingsResult = $conn->query($settingsSql);

if ($settingsResult && $settingsResult->num_rows > 0) {
    $setting = $settingsResult->fetch_assoc();
    $geofencingEnabled = $setting['setting_value'] == '1';
}

// If geofencing is enabled, verify user's location
if ($geofencingEnabled) {
    $locationCheckSql = "SELECT * FROM office_locations WHERE id = ? AND is_active = 1";
    $locationCheckStmt = $conn->prepare($locationCheckSql);
    $locationCheckStmt->bind_param("i", $location_id);
    $locationCheckStmt->execute();
    $locationResult = $locationCheckStmt->get_result();
    
    if ($locationResult->num_rows === 0) {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid office location',
            'data' => null
        ]);
        exit();
    }
    
    $location = $locationResult->fetch_assoc();
    $radiusMeters = $location['radius_meters'];
    
    // Calculate distance between user and office (using Haversine formula)
    $distance = calculateDistance($latitude, $longitude, $location['latitude'], $location['longitude']);
    
    if ($distance > $radiusMeters) {
        echo json_encode([
            'status' => false,
            'message' => 'You must be within the office premises to punch out',
            'data' => null
        ]);
        exit();
    }
}

// Get current time
$currentTime = date('Y-m-d H:i:s');

// Calculate total hours worked
$punchInTime = strtotime($log['punch_in_time']);
$punchOutTime = strtotime($currentTime);
$totalHours = round(($punchOutTime - $punchInTime) / 3600, 2); // Convert seconds to hours

// Check if early exit
$status = $log['status'];
$isEarlyExit = 0;

// Only check for early exit if status is 'present' or 'late'
if ($status === 'present' || $status === 'late') {
    // Get the shift end time from the log
    $shiftEnd = isset($log['shift_end']) ? $log['shift_end'] : '17:00:00';
    
    // Get early exit threshold
    $earlyExitThresholdSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'early_exit_threshold_minutes'";
    $earlyExitThresholdResult = $conn->query($earlyExitThresholdSql);
    $earlyExitThreshold = 15; // Default
    
    if ($earlyExitThresholdResult && $earlyExitThresholdResult->num_rows > 0) {
        $earlyExitThresholdSetting = $earlyExitThresholdResult->fetch_assoc();
        $earlyExitThreshold = intval($earlyExitThresholdSetting['setting_value']);
    }
    
    // Get date from punch in time
    $punchInDate = date('Y-m-d', $punchInTime);
    
    // Create timestamp for shift end time today
    $shiftEndTimestamp = strtotime($punchInDate . ' ' . $shiftEnd);
    
    // Subtract early exit threshold
    $earlyExitThresholdTimestamp = $shiftEndTimestamp - ($earlyExitThreshold * 60);
    
    // Check if punch out is before early exit threshold
    if ($punchOutTime < $earlyExitThresholdTimestamp) {
        $status = 'early_exit';
        $isEarlyExit = 1;
    }
}

// Update attendance record
$updateSql = "UPDATE attendance_logs 
              SET punch_out_time = ?, 
                  punch_out_location_id = ?, 
                  punch_out_latitude = ?, 
                  punch_out_longitude = ?, 
                  punch_out_ip = ?, 
                  total_hours = ?, 
                  status = ?, 
                  notes = ? 
              WHERE id = ?";

$ip = $_SERVER['REMOTE_ADDR'];
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param(
    "siiddssii", 
    $currentTime, 
    $location_id, 
    $latitude, 
    $longitude, 
    $ip, 
    $totalHours, 
    $status, 
    $notes, 
    $log_id
);

$updateResult = $updateStmt->execute();

if ($updateResult) {
    // Update attendance summary record
    $today = date('Y-m-d');
    
    // Get all attendance logs for the day to calculate total hours
    $logsForDaySql = "SELECT SUM(total_hours) as day_total_hours FROM attendance_logs 
                     WHERE user_id = ? 
                     AND user_type = ? 
                     AND DATE(punch_in_time) = ? 
                     AND punch_out_time IS NOT NULL";
    $logsForDayStmt = $conn->prepare($logsForDaySql);
    $logsForDayStmt->bind_param("iss", $user_id, $user_type, $today);
    $logsForDayStmt->execute();
    $logsForDayResult = $logsForDayStmt->get_result();
    $logsForDay = $logsForDayResult->fetch_assoc();
    
    $totalDayHours = $logsForDay['day_total_hours'] ?? 0;
    
    // Update the summary record
    $summarySql = "UPDATE attendance_summary 
                  SET status = ?, 
                      work_hours = ?, 
                      is_early_exit = ? 
                  WHERE user_id = ? 
                  AND user_type = ? 
                  AND summary_date = ?";
    
    $summaryStmt = $conn->prepare($summarySql);
    $summaryStmt->bind_param(
        "sdisss", 
        $status, 
        $totalDayHours, 
        $isEarlyExit, 
        $user_id, 
        $user_type, 
        $today
    );
    
    $summaryStmt->execute();
    
    // Get the updated record
    $getSql = "SELECT * FROM attendance_logs WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param("i", $log_id);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    $updatedLog = $getResult->fetch_assoc();
    
    // Get location names
    // Get punch in location
    $inLocationName = "Unknown";
    if ($updatedLog['punch_in_location_id'] > 0) {
        $inLocationSql = "SELECT location_name FROM office_locations WHERE id = ?";
        $inLocationStmt = $conn->prepare($inLocationSql);
        $inLocationStmt->bind_param("i", $updatedLog['punch_in_location_id']);
        $inLocationStmt->execute();
        $inLocationResult = $inLocationStmt->get_result();
        
        if ($inLocationResult->num_rows > 0) {
            $inLocationData = $inLocationResult->fetch_assoc();
            $inLocationName = $inLocationData['location_name'];
        }
    }
    
    // Get punch out location
    $outLocationName = "Unknown";
    if ($location_id > 0) {
        $outLocationSql = "SELECT location_name FROM office_locations WHERE id = ?";
        $outLocationStmt = $conn->prepare($outLocationSql);
        $outLocationStmt->bind_param("i", $location_id);
        $outLocationStmt->execute();
        $outLocationResult = $outLocationStmt->get_result();
        
        if ($outLocationResult->num_rows > 0) {
            $outLocationData = $outLocationResult->fetch_assoc();
            $outLocationName = $outLocationData['location_name'];
        }
    }
    
    // Add location names to response
    $updatedLog['punch_in_location'] = $inLocationName;
    $updatedLog['punch_out_location'] = $outLocationName;
    
    echo json_encode([
        'status' => true,
        'message' => 'Successfully punched out',
        'data' => $updatedLog
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to record punch out',
        'data' => null
    ]);
}

// Helper function to calculate distance between two points in meters
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // meters
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
} 