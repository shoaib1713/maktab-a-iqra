<?php
header('Content-Type: application/json');
// Disable error output - very important to prevent PHP errors from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

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
$location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : 0;

    //    file_put_contents('distance_log.txt', json_encode([
    //     'location_id' => $location_id,
    //     'latitude' => $latitude,
    //     'longitude' => $longitude,
    //     'user_id' => $user_id,
    //     'user_type' => $user_type
    // ], JSON_PRETTY_PRINT), FILE_APPEND);

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
    
    //     file_put_contents('distance_log.txt', json_encode([
    //     'user_lat' => $latitude,
    //     'user_lng' => $longitude,
    //     'office_lat' => $location['latitude'],
    //     'office_lng' => $location['longitude'],
    //     'calculated_distance' => $distance
    // ], JSON_PRETTY_PRINT), FILE_APPEND);

    if ($distance > $radiusMeters) {
        echo json_encode([
            'status' => false,
            'message' => 'You must be within the office premises to punch in',
            'data' => null
        ]);
        exit();
    }
}

// Check if multiple shifts are enabled
$multipleShiftsEnabled = false;
$multipleShiftsSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'multiple_shifts_enabled'";
$multipleShiftsResult = $conn->query($multipleShiftsSql);

if ($multipleShiftsResult && $multipleShiftsResult->num_rows > 0) {
    $setting = $multipleShiftsResult->fetch_assoc();
    $multipleShiftsEnabled = $setting['setting_value'] == '1';
}

// If multiple shifts are not enabled, check for existing punch-in
if (!$multipleShiftsEnabled) {
    $checkSql = "SELECT id FROM attendance_logs 
                WHERE user_id = ? 
                AND user_type = ? 
                AND DATE(punch_in_time) = CURDATE() 
                AND punch_out_time IS NULL";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("is", $user_id, $user_type);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'status' => false,
            'message' => 'You are already punched in for today',
            'data' => null
        ]);
        exit();
    }
}

// Get current time
$currentTime = date('Y-m-d H:i:s');

// Get work shifts
$shiftsSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'work_shifts'";
$shiftsResult = $conn->query($shiftsSql);

$workShifts = [];
if ($shiftsResult && $shiftsResult->num_rows > 0) {
    $shiftsData = $shiftsResult->fetch_assoc();
    $workShifts = json_decode($shiftsData['setting_value'], true);
}

// Default shift if none found
if (empty($workShifts)) {
    $workShifts = [
        [
            'start' => '09:00',
            'end' => '17:00',
            'min_hours' => 8
        ]
    ];
}

// Find current shift
$currentHour = date('H:i');
$currentShift = null;

foreach ($workShifts as $shift) {
    if ($currentHour >= $shift['start'] && $currentHour <= $shift['end']) {
        $currentShift = $shift;
        break;
    }
}

// If no matching shift, use the first one
if (!$currentShift) {
    $currentShift = $workShifts[0];
}

// Determine attendance status
$status = 'present';

// Check if it's a weekend
$weekendDaysSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'weekend_days'";
$weekendDaysResult = $conn->query($weekendDaysSql);
$weekendDays = [];

if ($weekendDaysResult && $weekendDaysResult->num_rows > 0) {
    $weekendDaysSetting = $weekendDaysResult->fetch_assoc();
    $weekendDays = explode(',', $weekendDaysSetting['setting_value']);
}

// Get day of week (0 = Sunday, 6 = Saturday)
$dayOfWeek = date('w');

if (in_array($dayOfWeek, $weekendDays)) {
    $status = 'weekend';
} else {
    // Check if it's a holiday
    $holidaySql = "SELECT id FROM holidays WHERE holiday_date = CURDATE()";
    $holidayResult = $conn->query($holidaySql);
    
    if ($holidayResult && $holidayResult->num_rows > 0) {
        $status = 'holiday';
    } else {
        // Check if user is late
        $lateThresholdSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'late_threshold_minutes'";
        $lateThresholdResult = $conn->query($lateThresholdSql);
        $lateThreshold = 15; // Default
        
        if ($lateThresholdResult && $lateThresholdResult->num_rows > 0) {
            $lateThresholdSetting = $lateThresholdResult->fetch_assoc();
            $lateThreshold = intval($lateThresholdSetting['setting_value']);
        }
        
        // Get shift start time for today
        $shiftStartTime = strtotime(date('Y-m-d') . ' ' . $currentShift['start']);
        
        // Add late threshold minutes
        $lateThresholdTime = $shiftStartTime + ($lateThreshold * 60);
        
        // If current time is after late threshold, mark as late
        if (time() > $lateThresholdTime) {
            $status = 'late';
        }
    }
}

// Insert attendance record
$insertSql = "INSERT INTO attendance_logs 
             (user_id, user_type, punch_in_time, punch_in_location_id, 
              punch_in_latitude, punch_in_longitude, punch_in_ip, status,
              shift_start, shift_end) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$ip = $_SERVER['REMOTE_ADDR'];
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param(
    "issiddssss", 
    $user_id, 
    $user_type, 
    $currentTime, 
    $location_id, 
    $latitude, 
    $longitude, 
    $ip,
    $status,
    $currentShift['start'],
    $currentShift['end']
);

$insertResult = $insertStmt->execute();

if ($insertResult) {
    // Get the inserted record ID
    $logId = $conn->insert_id;
    
    // Create attendance summary record for reporting (only if it doesn't exist)
    $today = date('Y-m-d');
    $month = date('m');
    $year = date('Y');
    
    // Check if summary already exists for today
    $checkSummarySql = "SELECT id FROM attendance_summary 
                       WHERE user_id = ? 
                       AND user_type = ? 
                       AND summary_date = ?";
    $checkSummaryStmt = $conn->prepare($checkSummarySql);
    $checkSummaryStmt->bind_param("iss", $user_id, $user_type, $today);
    $checkSummaryStmt->execute();
    $checkSummaryResult = $checkSummaryStmt->get_result();
    
    if ($checkSummaryResult->num_rows === 0) {
        // Create new summary record
        $summarySql = "INSERT INTO attendance_summary 
                      (user_id, user_type, summary_date, month, year, status, is_late) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $isLate = ($status === 'late') ? 1 : 0;
        $summaryStmt = $conn->prepare($summarySql);
        $summaryStmt->bind_param(
            "issiiis", 
            $user_id, 
            $user_type, 
            $today, 
            $month, 
            $year, 
            $status,
            $isLate
        );
        
        $summaryStmt->execute();
    }
    
    // Get the inserted record
    $getSql = "SELECT * FROM attendance_logs WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param("i", $logId);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    $log = $getResult->fetch_assoc();
    
    // Get location name
    $locationName = "Unknown";
    if ($location_id > 0) {
        $locationSql = "SELECT location_name FROM office_locations WHERE id = ?";
        $locationStmt = $conn->prepare($locationSql);
        $locationStmt->bind_param("i", $location_id);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();
        
        if ($locationResult->num_rows > 0) {
            $locationData = $locationResult->fetch_assoc();
            $locationName = $locationData['location_name'];
        }
    }
    
    // Add location name to response
    $log['punch_in_location'] = $locationName;
    
    echo json_encode([
        'status' => true,
        'message' => 'Successfully punched in',
        'data' => $log
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to record punch in',
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