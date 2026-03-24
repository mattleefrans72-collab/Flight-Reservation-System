<?php
namespace App\Services;

use App\Forms\FlightForm;
use App\Core\Cache;
use App\Repositories\AirportRepository;

class FlightSearchService {
  protected bool $resetPageNum = false;

  public function search($queryParams) {
    if (empty($queryParams)) {
      $queryParams = $_SESSION["params"] ?? [];
      
    }

    $params = $this->mainParams($queryParams);
    $filter = $this->filtersParams($queryParams);
    $page = (int) ($queryParams["page"] ?? 1);

    $_SESSION["params"] = $params;

    FlightForm::validate($params);

    $cache = new Cache();

    $encode = $params;
    $encode["stops"] = $filter["stops"];
    $encode["airlines"] = $filter["airlines"];

    $key = $cache->encode($encode);

    $cached = $cache->get($key);
    $response = $cached["response"] ?? [];
    $meta = $cached["extraMeta"] ?? [];

    if (!$cached) {
      $this->resetPageNum = true;

      $flightsRepo = new AirportRepository($params);
      $flightsRepo->filter($filter);

      $response = $flightsRepo->response;
      $meta = $flightsRepo->meta;

      $cache->set($key, [
        "response" => $response,
        "extraMeta" => $meta,
      ]);
    }

    $response = $this->paginate($response, $this->resetPageNum, $page);

    unset($encode["airlines"]);
    $stopKey = $cache->encode($encode);
    $stopCache = $cache->get($stopKey);

    return [
      "response" => $response,
      "extraMeta" => $meta,
      "original_cache" => $stopCache,
    ];
  }

  protected function paginate($response, $reset = false, $requestedPage = 1, $perPage = 15) {
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
      "destinationLocationCode" => $queryParams["destinationLocationCode"] ?? "",
      "departureDate" => $queryParams["departureDate"] ?? "",
      "returnDate" => $queryParams["returnDate"] ?? "",
      "adults" => $queryParams["adults"] ?? "",
      "children" => is_numeric($queryParams["children"] ?? null)
        ? (int) $queryParams["children"]
        : 0,
      "max" => 100,
    ];

    if (isset($queryParams["travelClass"]) && $queryParams["travelClass"] !== "ANY") {
      $params["travelClass"] = $queryParams["travelClass"];
    }

    return $params;
  }

  private function filtersParams($queryParams) {
    return [
      "stops" => $queryParams["stops"] ?? "",
      "airlines" => $queryParams["airlines_show"] ?? [],
    ];
  }
}
