<?php requireStyle(["general", "flight.view", "nav"]) ?>
  <body>

  <?php require "partials/nav.php" ?>

    <div class="main-section">
      <div class="side">
        <div class="side-bar">
          <div>Flight filter</div>
          
          <form action="/flight" method="get">
            <input type="hidden" name="adults" value="<?= $_GET["adults"] ?>">
            <input type="hidden" name="children" value="<?= $_GET["children"] ?? 0?>">
            <input type="hidden" name="class" value="<?= $_GET["class"] ?>">

            <div class="side-bar-info">
              <div class="side-bar-text">FROM:
                <input name="from" class="filter-input from-input" type="text" value="<?= $_GET["from"] ?>">
              </div>

              <div class="side-bar-text">TO:
                <input name="to" class="filter-input to-input" type="text" value="<?= $_GET["to"] ?>">
              </div>

              <div class="side-bar-text">DEPARTING:
                <input name="departure" class="filter-input departing-input" type="date" value="<?= $_GET["departure"] ?>">
              </div>

              <div class="side-bar-text">RETURNING:
                <input name="return" class="filter-input departing-input" type="date" value="<?= $_GET["return"] ?>">
              </div>

              <div class="side-bar-search">
                <button class="search-button" type="submit">Search</button>
              </div>
            </div>  
          </form>
        </div>

        <form class="filters" action="/flight" method="GET">
          <div class="filters-header">
            <div>Filters</div>
            <button type="reset" class="reset-btn">Reset</button>
          </div>
          <div class="result-count">Showing 14 results</div>

          <!-- Stops -->
          <div class="filter-section">
            <div class="section-title">
              <span>Stops</span>
              
            </div>

            <div class="filter-option">
              <label>
                <input type="radio" name="stops" value="any">
                Any
              </label>
              <div class="count">2594<br><span class="from-price">From £493.46</span></div>
            </div>

            <div class="filter-option">
              <label>
                <input type="radio" name="direct" value="direct" checked>
                Direct only
              </label>
              <div class="count">14<br><span class="from-price">From £727.51</span></div>
            </div>

            <div class="filter-option">
              <label>
                <input type="radio" name="1-stops" value="1stop">
                1 stop max
              </label>
              <div class="count">2072<br><span class="from-price">From £493.46</span></div>
            </div>
          </div>

          <div class="divider"></div>

          <!-- Airlines -->
          <div class="filter-section">
            <div class="section-title">Airlines</div>

            <div class="filter-option">
              <label>
                <input type="checkbox" name="airlines" value="thai-airways" checked>
                Thai Airways
              </label>
              <div class="count">10</div>
            </div>

            <div class="filter-option">
              <label>
                <input type="checkbox" name="airlines" value="eva-airways" checked>
                Eva Airways
              </label>
              <div class="count">10</div>
            </div>
          </div>

          <!-- Submit/Reset Buttons -->
          
            
            <button type="submit">Apply Filters</button>

        </form>
      </div>

      

      <div class="flight-rows">
        <div class="flights">

          <?php foreach ($flights as $flight): ?>
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
              $dictionaries = $response['dictionaries']; 

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
                        <img src="https://img.wway.io/pics/root/<?=$airline?>@png?exar=1&rs=fit:200:200" alt="">

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
                          <img src="https://img.wway.io/pics/root/<?=$airline?>@png?exar=1&rs=fit:200:200" alt="">
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
                <h2>Your flight to Bangkok</h2>
                <button class="close-details close-details-js" data-modal="<?=$flight['id']?>">✖</button>
              </div>

              <div class="modal-content">
                <?php displayFlightSegmentDetails($outbound, $fareDetails, $dictionaries, 'Outbound Flight'); ?>

                <?php if (!empty($inbound)): ?>
                  <?php displayFlightSegmentDetails($inbound, $fareDetails, $dictionaries, 'Inbound Flight'); ?>
                <?php endif; ?>
              </div>
            </div>

          <?php endforeach; ?>

          
        </div>
        
        <div class="flight-pages">
          <form action="/flight" method="get">

            <!-- Preserve search input -->
            <input type="hidden" name="from" value="<?= $_GET['from'] ?? '' ?>">
            <input type="hidden" name="to" value="<?= $_GET['to'] ?? '' ?>">
            <input type="hidden" name="departure" value="<?= $_GET['departure'] ?? '' ?>">
            <input type="hidden" name="return" value="<?= $_GET['return'] ?? '' ?>">
            <input type="hidden" name="adults" value="<?= $_GET['adults'] ?? ''?>">
            <input type="hidden" name="childrens" value="<?= $_GET['childrens'] ?? ''?>">

            <?php if ($totalPages > 9): ?>
              <?php if ($page <= 4): ?>
                <!-- Near the start -->
                <?php for ($i = 1; $i <= 6; $i++): ?>
                  <button name="page" value="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>
                <button disabled>...</button>
                <button name="page" value="<?= $totalPages - 1 ?>"><?= $totalPages - 1 ?></button>
                <button name="page" value="<?= $totalPages ?>"><?= $totalPages ?></button>

              <?php elseif ($page >= $totalPages - 3): ?>
                <!-- Near the end -->
                <button name="page" value="1">1</button>
                <button name="page" value="2">2</button>
                <button disabled>...</button>
                <?php for ($i = $totalPages - 5; $i <= $totalPages; $i++): ?>
                  <button name="page" value="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>

              <?php else: ?>
                <!-- Middle range -->
                <button name="page" value="1">1</button>
                <button name="page" value="2">2</button>
                <button disabled>...</button>

                <?php for ($i = $page - 2; $i <= $page + 2; $i++): ?>
                  <button name="page" value="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>

                <button disabled>...</button>
                <button name="page" value="<?= $totalPages - 1 ?>"><?= $totalPages - 1 ?></button>
                <button name="page" value="<?= $totalPages ?>"><?= $totalPages ?></button>
              <?php endif; ?>

            <?php else: ?>
              <!-- Fewer than 10 pages, show all -->
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button name="page" value="<?= $i ?>"><?= $i ?></button>
              <?php endfor; ?>
            <?php endif; ?>
          </form>

        </div>

      </div> 
    </div>
   <?php requireModule(["utils/heading", "flight"]) ?>
  </body>
</html>