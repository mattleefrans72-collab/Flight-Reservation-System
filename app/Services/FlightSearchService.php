<?php
namespace App\Services;

use App\Forms\FlightForm;
use App\Core\Cache;
use App\Repositories\AirportRepository;

class FlightSearchService {
  protected bool $resetPageNum = false;

  public function search($queryParams) {
    $params = $this->mainParams($queryParams);
    $filter = $this->filtersParams($queryParams);
    $page = (int) ($queryParams["page"] ?? 1);

    FlightForm::validate($params);

    $keyAndStops = [
      ...$params,
      "stops" => $filter["stops"]
    ];
    
    $cache = new Cache();

    $stopsFilteredKey = $cache->encodeOnlyStopKey($keyAndStops);

    $stopsFilteredCached = $cache->get($stopsFilteredKey);

    if (!$stopsFilteredCached) {
      $repo = new AirportRepository($params);
      $repo->filter($keyAndStops);

      $stopsFilteredCached = [
        "response" => $repo->response,
        "extraMeta" => $repo->meta,
      ];

      $cache->set($stopsFilteredKey, $stopsFilteredCached);
    }

    $filterKey = $cache->encodeKey([
      ...$params,
      "stops" => $filter["stops"],
      "airlines" => $filter["airlines"],
    ]);

    $cached = $cache->get($filterKey);

    if (!$cached) {
      $repo = new AirportRepository($params);
      $repo->filter($filter);

      $cached = [
        "response" => $repo->response,
        "extraMeta" => $repo->meta,
      ];

      $cache->set($filterKey, $cached);
    }

    $response = $cached["response"] ?? [];
    $meta = $cached["extraMeta"] ?? [];

    $response = $this->paginate($response, $this->resetPageNum, $page);

    return [
      "response" => $response,
      "extraMeta" => $meta,
      "original_cache" => $cached,
      "stop_filtered_cashe" => $stopsFilteredCached
    ];
  }

  protected function paginate(
    $response,
    $reset = false,
    $requestedPage = 1,
    $perPage = 15,
  ) {
    $data = $response["data"] ?? [];
    $page = $reset ? 1 : max(1, $requestedPage);

    $total = count($data);
    $totalPages = (int) ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    $pagedFlights = array_slice($data, $offset, $perPage);

    return [
      "flights" => $pagedFlights,
      "totalPages" => $totalPages,
      "currentPage" => $page,
      "allFlights" => $data,
      "response" => $response,
    ];
  }
  private function mainParams($queryParams) {
    $params = [
      "originLocationCode" => $queryParams["originLocationCode"] ?? "",
      "destinationLocationCode" =>
        $queryParams["destinationLocationCode"] ?? "",
      "departureDate" => $queryParams["departureDate"] ?? "",
      "returnDate" => $queryParams["returnDate"] ?? "",
      "adults" => $queryParams["adults"] ?? "",
      "children" => is_numeric($queryParams["children"] ?? null)
        ? (int) $queryParams["children"]
        : 0,
      "max" => 200,
    ];

    if (
      isset($queryParams["travelClass"]) &&
      $queryParams["travelClass"] !== "ANY"
    ) {
      $params["travelClass"] = $queryParams["travelClass"];
    }

    return $params;
  }

  private function filtersParams($queryParams) {
    if (!isset($queryParams["stops"]) || $queryParams["stops"] == "ANY") {
      unset($queryParams["stops"]);
    } 
    return [
      "stops" => $queryParams["stops"] ?? "",
      "airlines" => $queryParams["airlines_show"] ?? [],
    ];
  }
}
