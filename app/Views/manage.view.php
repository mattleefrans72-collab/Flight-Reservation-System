<?php requireStyle (["general", "nav", "manage.view"])?>
</head>
  <body>
    <?php require "partials/nav.php" ?>

      <h2>My Bookings</h2>

      <?php foreach ($bookings as $booking): ?>
        <div class="booking">
          <div class="booking-header">
            <h3>Booking Ref: <?= htmlspecialchars($booking['booking_reference']) ?></h3>
              <form method="post" action="/flight/manage">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                <button class="btn-delete">
                  Delete Booking
                </button>
              </form>
          </div>
          <p><strong>Created:</strong> <?= htmlspecialchars($booking['created_at']) ?></p>

          <table>
            <thead>
              <tr>
                <th>Segment</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Airline</th>
                <th>Aircraft</th>
                <th>Cabin</th>
                <th>Total bags</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($booking['segments'] as $seg): ?>
              <tr>
                <td><?= htmlspecialchars($seg['segment_type']) ?></td>
                <td><?= htmlspecialchars($seg['departure_code']) ?></td>
                <td><?= htmlspecialchars($seg['arrival_code']) ?></td>
                <td><?= htmlspecialchars($seg['departure_time']) ?></td>
                <td><?= htmlspecialchars($seg['arrival_time']) ?></td>
                <td><?= htmlspecialchars($seg['airline']) ?></td>
                <td><?= htmlspecialchars($seg['aircraft']) ?></td>
                <td><?= htmlspecialchars($seg['cabin']) ?></td>
                <td><?= htmlspecialchars($seg['checked_bag'] + $seg["cabin_bag"]) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
  
  </body>
</html>
