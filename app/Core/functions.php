<?php 
use App\Core\Response;
use App\Core\Session;
function dd($value) {
  echo "<pre>" . var_dump($value) . "<pre>";
  die();
}
function urlIs($value) {
  return $_SERVER["REQUEST_URI"] == $value;
}
function abort($code = 404) {
  http_response_code($code);
  require base_path("views/{$code}.php");
  die();
}
function authorize($condition, $status = Response::FORBIDEN) {
  if (!$condition) {
    abort($status);
  }
}
function base_path($path) {
  return BASE_PATH . $path;
}
function view ($path, $attributes = []) {
  extract ($attributes);
  require base_path("app/Views/" . $path);
}
function requireStyle ($attributes = []) {
  require base_path("app/Views/partials/header.php");
}
function requireModule ($attributes = []) {
  require base_path("app/Views/partials/footer.php");
}
function redirect($path) {
  header("location: {$path}");
  exit();
}
function old($key, $default = '') {
  return Session::get("old")[$key] ?? $default;
}
function errors($key, $default = '') {
  return Session::get("errors")[$key] ?? $default;
}


function formatTime($datetime) {
    return date('H:i', strtotime($datetime));
}
function formatDate($datetime) {
    return date('d M', strtotime($datetime));
}
function duration($durationStr) {
    // Example: PT10H45M => 10h 45m
    preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $durationStr, $matches);
    $h = $matches[1] ?? 0;
    $m = $matches[2] ?? 0;
    return "{$h}h {$m}m";
}
function stopLabel($segments) {
    return count($segments) - 1;
}
function getBagCountBySegments($fareDetails, $segmentIds) {
  $cabin = 0;
  $checked = 0;

  foreach ($fareDetails as $detail) {
      if (in_array($detail['segmentId'], $segmentIds)) {
          $cabin += $detail['includedCabinBags']['quantity'] ?? 0;
          $checked += $detail['includedCheckedBags']['quantity'] ?? 0;
      }
  }

  return ['cabin' => $cabin, 'checked' => $checked];
}
function displayFlightSegmentDetails($segments, $fareDetails, $dictionaries, $title = 'Flight Segment') {
    echo "<h3>{$title}</h3>";

    foreach ($segments as $segment) {
        $segmentId = $segment['id'];
        $fare = getFareBySegmentId($fareDetails, $segmentId);

        $departure = $segment['departure'];
        $arrival = $segment['arrival'];
        $airline = $dictionaries['carriers'][$segment['carrierCode']] ?? $segment['carrierCode'];
        $aircraftCode = $segment['aircraft']['code'];
        $aircraft = $dictionaries['aircraft'][$aircraftCode] ?? $aircraftCode;

        $cabin = $fare['cabin'] ?? 'N/A';
        $checkedBag = $fare['includedCheckedBags']['quantity'] ?? ($fare['includedCheckedBags']['weight'] ?? '0');
        $cabinBag = $fare['includedCabinBags']['quantity'] ?? '0';

        echo "<div class='segment'>";
        echo "<p><strong>{$departure['iataCode']}</strong> → <strong>{$arrival['iataCode']}</strong></p>";
        echo "<p>".date('D, M j Y, H:i', strtotime($departure['at'])) ." → " . date('H:i', strtotime($arrival['at'])) . "</p>";
        echo "<p>Airline: {$airline} ({$segment['carrierCode']}{$segment['number']})</p>";
        echo "<p>Aircraft: {$aircraft}</p>";
        echo "<p>Cabin: {$cabin}</p>";
        echo "<p>Checked Bag: {$checkedBag}</p>";
        echo "<p>Cabin Bag: {$cabinBag}</p>";
        echo "</div><hr>";
    }
}

// Helper to find fare by segment ID
function getFareBySegmentId($fareDetails, $segmentId) {
    foreach ($fareDetails as $detail) {
        if ($detail['segmentId'] == $segmentId) {
            return $detail;
        }
    }
    return [];
}


function map_array(array $array, string $targetKey) {
  $result = [];

  foreach ($array as $item) {
    if (!isset($item[$targetKey])) continue;

    $value = $item[$targetKey];

    if (!in_array($value, $result)) {
        $result[] = $value;
    }
  }

  return $result;
}
?>