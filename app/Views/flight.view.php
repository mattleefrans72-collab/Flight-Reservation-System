<?php requireStyle(["general", "flight.view", "nav"]) ?>
</head>
  <body>

  <?php require "partials/nav.php" ?>

    <div class="main-section">
      <div class="side">
        <div class="side-bar">
          <div>Flight filter</div>
          
          <form action="/flight" method="get">
            <input type="hidden" name="adults" value="<?= $_SESSION['params']['adults'] ?? 0?>">
            <input type="hidden" name="children" value="<?= $_SESSION['params']['children'] ?? 0?>">
            <input type="hidden" name="class" value="<?= $_SESSION['params']['travelClass'] ?? 'any' ?>">

            <div class="side-bar-info">
              <div class="side-bar-text">FROM:
                <input name="originLocationCode" class="filter-input from-input" type="text" value="<?= $_SESSION['params']["originLocationCode"] ?>">
              </div>

              <div class="side-bar-text">TO:
                <input name="destinationLocationCode" class="filter-input to-input" type="text" value="<?= $_SESSION['params']["destinationLocationCode"] ?>">
              </div>

              <div class="side-bar-text">DEPARTING:
                <input name="departureDate" class="filter-input departing-input" type="date" value="<?= $_SESSION['params']["departureDate"] ?>">
              </div>

              <div class="side-bar-text">RETURNING:
                <input name="returnDate" class="filter-input departing-input" type="date" value="<?= $_SESSION['params']["returnDate"] ?>">
              </div>

              <div class="side-bar-search">
                <button class="search-button" type="submit">Search</button>
              </div>
            </div>  
          </form>
        </div>

        <?php if (isset($response['response']['meta'])): ?> 
          <form class="filters" action="/flight" method="GET">
            <?php preserve_query_params(['stops', 'airlines_show', 'airlines_hide']) ?>
            <div class="filters-header">
              <div class="side-bar-title">Flight filter</div>
              <button type="reset" class="reset-btn">Reset</button>
            </div>
            <div class="result-count">Showing <?=count($response['allFlights'])?> results</div>

            <!-- Stops -->
            
            <div class="filter-section">
              <div class="section-title">
                <span>Stops</span>
                
              </div>

              <div class="filter-option">
                <label>
                  <input type="radio" name="stops" value="any" <?= ($_GET['stops'] ?? 'any') == 'direct' ? '' : 'checked'?>>
                  Any
                </label>
                <div class="count"><?=$response['response']['meta']['count']  ?><br><span class="from-price">From £<?= $extraMeta['lowestPrice'] ?></span></div>
              </div>

              <div class="filter-option">
                <label>
                  <input type="radio" name="stops" value="direct" <?= ($_GET['stops'] ?? 'any') == 'direct' ? 'checked' : ''?>>
                  Direct only
                </label>
                <div class="count"><?=  $extraMeta['directCount'] ?><br><span class="from-price">From £<?= $extraMeta['directLowestPrice'] ?></span></div>
              </div>

            </div>
          

            <div class="divider"></div>

            <!-- Airlines -->
            <div class="filter-section">
              <div class="section-title">Airlines</div>
                <?php foreach($original_cache['response']['dictionaries']['carriers'] as $carriers => $carriersName): ?>     
                    <div class="filter-option">
                      <label>
                        <input type="checkbox" name="airlines_show[]" value="<?= $carriers ?>" <?= checkedAirlines($carriers, $_GET['airlines_show'] ?? []) ?>>
                        <?= $carriersName ?>
                      </label>
                      <div class="count"><?= $original_cache['extraMeta']['airlinesCount'][$carriers] ?? 0 ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Submit/Reset Buttons -->
            
              
              <button type="submit" class="apply-filters-button">Apply Filters</button>

          </form> 
        </div>

        

        <div class="flight-rows">
          <div class="flights">

            <?php foreach ($response['flights'] as $flight): ?>
              <?php
                $outbound = $flight['itineraries'][0]['segments'];
                $inbound = $flight['itineraries'][1]['segments'] ?? [];

                $price = $flight['price']['total'];
                $currency = $flight['price']['currency'];
                $outboundAirlines = map_array($outbound, 'carrierCode');
                $inboundAirlines = map_array($inbound, 'carrierCode');

                $outboundSegmentIds = array_column($outbound, 'id');
                $inboundSegmentIds = array_column($inbound, 'id');
                $fareDetails = $flight['travelerPricings'][0]['fareDetailsBySegment'];
                $dictionaries = $response['response']['dictionaries']; 

                $outboundBags = getBagCountBySegments($fareDetails, $outboundSegmentIds);
                $inboundBags = getBagCountBySegments($fareDetails, $inboundSegmentIds);
              ?>

            <div class="flight">
                <div class="outer-layout">

                  <div class="inner-layout-1">

                    <!-- OUTBOUND -->
                    <?php
                      $first = $outbound[0];
                      $last = end($outbound);
                    ?>

                    <?php
                      $logoSize = count($outboundAirlines) == 1 ? 'large-logo' : 'small-logo';
                    ?>
                    <div class="logo ">
                      <div class="<?=$logoSize?>">
                      <?php foreach ($outboundAirlines as $index => $airline): ?>
                        <div class="image logo-<?=$index?>">
                          <img class="airline-logo" src="https://img.wway.io/pics/root/<?=$airline?>@png?exar=1&rs=fit:200:200" alt="">

                        </div>
                      <?php endforeach; ?>
                      </div>
                    </div>

                    <div class="depart-time">
                      <?= formatTime($first['departure']['at']) ?>
                      <div class="depart-info"><?= $first['departure']['iataCode'] ?> · <?= formatDate($first['departure']['at']) ?></div>
                    </div>

                    <div class="flight-length">
                      <img src="images/airplane-vector-isolated-icon-emoji-illustration-airplane-vector-emoticon_603823-804.avif" alt="">
                      <span><?= stopLabel($outbound) === 0 ? 'Direct' : stopLabel($outbound) . ' stop(s)' ?> · <?= duration($flight['itineraries'][0]['duration']) ?></span>
                      <img src="images/airplane-vector-isolated-icon-emoji-illustration-airplane-vector-emoticon_603823-804.avif" alt="">
                    </div>

                    <div class="arrive-time">
                      <?= formatTime($last['arrival']['at']) ?>
                      <div class="arrive-info"><?= $last['arrival']['iataCode'] ?> · <?= formatDate($last['arrival']['at']) ?></div>
                    </div>

                    <!-- INBOUND -->
                    <?php if (!empty($inbound)): ?>
                      <?php
                        $firstReturn = $inbound[0];
                        $lastReturn = end($inbound);
                      ?>
                      <?php
                        $logoSize = count($inboundAirlines) == 1 ? 'large-logo' : 'small-logo';
                      ?>
                      <div class="logo ">
                        <div class="<?=$logoSize?>">
                        <?php foreach ($inboundAirlines as $index => $airline): ?>
                          <div class="image logo-<?=$index?>">
                            <img class="airline-logo" src="https://img.wway.io/pics/root/<?=$airline?>@png?exar=1&rs=fit:200:200" alt="">
                          </div>
                        <?php endforeach; ?>
                        </div>
                      </div>

                      <div class="depart-time">
                        <?= formatTime($firstReturn['departure']['at']) ?>
                        <div class="depart-info"><?= $firstReturn['departure']['iataCode'] ?> · <?= formatDate($firstReturn['departure']['at']) ?></div>
                      </div>

                      <div class="flight-length">
                        <img src="images/airplane-vector-isolated-icon-emoji-illustration-airplane-vector-emoticon_603823-804.avif" alt="">
                        <span><?= stopLabel($inbound) === 0 ? 'Direct' : stopLabel($inbound) . ' stop(s)' ?> · <?= duration($flight['itineraries'][1]['duration']) ?></span>
                        <img src="images/airplane-vector-isolated-icon-emoji-illustration-airplane-vector-emoticon_603823-804.avif" alt="">
                      </div>

                      <div class="arrive-time">
                        <?= formatTime($lastReturn['arrival']['at']) ?>
                        <div class="arrive-info"><?= $lastReturn['arrival']['iataCode'] ?> · <?= formatDate($lastReturn['arrival']['at']) ?></div>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- Price and Button -->
                  <div class="inner-layout-2">
                    <!-- Outbound Bags -->
                    <div class="travel-class">
                        <?php if (($outboundBags['cabin'] >= $inboundBags['cabin']) && ($outboundBags['cabin'] >= 1 && $inboundBags['cabin'] >= 1)): ?>
                        <img src="images/CarryonBag.png" alt="" style="position: relative; top: 3px; z-index: 1;"> 
                        <?php endif; ?>
                        <?php if (($outboundBags['checked'] >= $inboundBags['checked']) && ($outboundBags['checked'] >= 1 && $inboundBags['checked'] >= 1)): ?>
                          <img src="images/checkedBag.png" alt="">
                        <?php endif; ?>
                    </div>

                    
                    <div class="price">£<?= number_format($price, 2) ?></div>
                    <div>
                      <button class="view-button open-details-js" data-modal="<?=$flight['id']?>">More Info</button>
                    </div>
                  </div>

                </div>
              </div>

              <!-- Overlay -->
              <div class="overlay hidden overlay-js" data-modal="<?=$flight['id']?>"></div>

              <!-- Side Modal -->
              <div class="side-modal hidden side-modal-js" data-modal="<?=$flight['id']?>">
                <div class="modal-header">
                  <h2>Your flight to <?=$flight['arrivalCountry']?></h2>
                  <button class="close-details close-details-js" data-modal="<?=$flight['id']?>">✖</button>
                </div>

                <div class="modal-content">
                  <?php displayFlightSegmentDetails($outbound, $fareDetails, $dictionaries, 'Outbound Flight'); ?>

                  <?php if (!empty($inbound)): ?>
                    <?php displayFlightSegmentDetails($inbound, $fareDetails, $dictionaries, 'Inbound Flight'); ?>
                  <?php endif; ?>
                  <div>
                    <?php if (!empty($_SESSION['user'])): ?>
                      <form method="post" action="/booking/select">
                        //add amount of people
                        <input type="hidden" name="flight_data" value="<?= htmlentities(json_encode(['inbound' => $inbound, 'outbound' => $outbound, 'fareDetails' => $fareDetails, 'distionaries' => $dictionaries])) ?>">
                        <button type="submit">Book Now</button>
                      </form>
                    <?php else: ?>
                      <form method="get" action="/register">
                        <button type="submit">Register to book</button>
                      </form>
                    <?php endif; ?>  

                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            
          </div>
          
          <div class="flight-pages">
            <form action="/flight" method="get">

              <!-- Preserve search input -->
              <input type="hidden" name="originLocationCode" value="<?= $_GET['originLocationCode'] ?? '' ?>">
              <input type="hidden" name="destinationLocationCode" value="<?= $_GET['destinationLocationCode'] ?? '' ?>">
              <input type="hidden" name="departureDate" value="<?= $_GET['departureDate'] ?? '' ?>">
              <input type="hidden" name="returnDate" value="<?= $_GET['returnDate'] ?? '' ?>">
              <input type="hidden" name="adults" value="<?= $_GET['adults'] ?? ''?>">
              <input type="hidden" name="children" value="<?= $_GET['children'] ?? ''?>">
              <input type="hidden" name="travelClass" value="<?= $_GET["travelClass"] ?? 'ANY'?>">              
              <input type="hidden" name="stops" value="<?= $_GET["stops"] ?? '' ?>">
             

              <?php foreach (($_GET['airlines_show'] ?? []) as $airline): ?>
                <input type="hidden" name="airlines_show[]" value="<?= htmlspecialchars($airline) ?>">
              <?php endforeach; ?>

              <?php if ($response['totalPages'] > 9): ?>
                <?php if ($response['page'] <= 4): ?>
                  <!-- Near the start -->
                  <?php for ($i = 1; $i <= 6; $i++): ?>
                    <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $i ?>"><?= $i ?></button>
                  <?php endfor; ?>
                  <button disabled>...</button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $response['totalPages'] - 1 ?>"><?= $response['totalPages'] - 1 ?></button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $response['totalPages'] ?>"><?= $response['totalPages'] ?></button>

                <?php elseif ($response['page'] >= $response['totalPages'] - 3): ?>
                  <!-- Near the end -->
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="1">1</button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="2">2</button>
                  <button disabled>...</button>
                  <?php for ($i = $response['totalPages'] - 5; $i <= $response['totalPages']; $i++): ?>
                    <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $i ?>"><?= $i ?></button>
                  <?php endfor; ?>

                <?php else: ?>
                  <!-- Middle range -->
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="1">1</button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="2">2</button>
                  <button disabled>...</button>

                  <?php for ($i = $response['page'] - 2; $i <= $response['page'] + 2; $i++): ?>
                    <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $i ?>"><?= $i ?></button>
                  <?php endfor; ?>

                  <button disabled>...</button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $response['totalPages'] - 1 ?>"><?= $response['totalPages'] - 1 ?></button>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $response['totalPages'] ?>"><?= $response['totalPages'] ?></button>
                <?php endif; ?>

              <?php else: ?>
                <!-- Fewer than 10 pages, show all -->
                <?php for ($i = 1; $i <= $response['totalPages']; $i++): ?>
                  <button class="<?= ($response['currentPage'] == $i) ? 'checked' : '' ?>" name="page" value="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>
              <?php endif; ?>
            </form>
          </div>
        </div> 
       <?php else: ?>

    </div>

      <div class="no-results">
        <img src="/images/aeroplane.png" alt="No results found">
         <div class="no-results-text">No flights found. Please adjust your search criteria and try again.</div>
      </div>
    <?php endif; ?>

   <?php requireModule(["utils/heading", "flight"]) ?>
  </body>
</html>