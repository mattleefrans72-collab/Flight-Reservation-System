<?php requireStyle(["general", "flight.view", "nav"]) ?>
  <body>

  <?php require "partials/nav.php" ?>

    <div class="main-section">
      <div class="side-bar">
        <div>Flight filter</div>
        <div class="side-bar-info">

          <div class="side-bar-text">FROM:
            <input class="filter-input from-input" type="text" value="<?= $_GET["from"] ?>">
          </div>

          <div class="side-bar-text">TO:
            <input class="filter-input to-input" type="text" value="<?= $_GET["to"] ?>">
          </div>

          <div class="side-bar-text">DEPARTING:
            <input class="filter-input departing-input" type="date" value="<?= $_GET["departure"] ?>">
          </div>

          <div class="side-bar-text">RETURNING:
            <input class="filter-input departing-input" type="date" value="<?= $_GET["return"] ?>">
          </div>

          <div class="side-bar-search">
            <button class="search-button">Search</button>
          </div>
        </div>
      </div>

      <div class="flight-rows">
        <div class="flights">

          <?php foreach ($flights as $flight): ?>
            <?php
              $outbound = $flight['itineraries'][0]['segments'];
              $inbound = $flight['itineraries'][1]['segments'] ?? [];

              $price = $flight['price']['total'];
              $currency = $flight['price']['currency'];
              $airline = $flight['validatingAirlineCodes'][0];
          
            ?>

          <div class="flight">
              <div class="outer-layout">

                <div class="inner-layout-1">

                  <!-- OUTBOUND -->
                  <?php
                    $first = $outbound[0];
                    $last = end($outbound);
                  ?>
                  <div class="logo"><?= $airline ?></div>

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
                    <div class="logo"><?= $airline ?></div>

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
                  <div class="travel-class">
                    <div class="personalBag-img" >
                      <img src="images/personalBag.png" alt="">
                    </div>
                    <img src="images/CarryonBag.png" alt="">
                    <img src="images/checkedBag.png">
                  </div>
                  <div class="price">£<?= number_format($price, 2) ?></div>
                  <div>
                    <button class="view-button">View Seats</button>
                  </div>
                </div>

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
   
  </body>
</html>