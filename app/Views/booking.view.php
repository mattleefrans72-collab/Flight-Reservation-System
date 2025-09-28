<?php requireStyle (["general", "nav", "booking.view"])?>
</head>
  <body>
    <?php require "partials/nav.php" ?>
    
    <?php
    displayFlightSegmentDetails($flight['outbound'], $flight['fareDetails'], $flight['dictionaries'], 'Outbound Flight');

    displayFlightSegmentDetails($flight['inbound'], $flight['fareDetails'], $flight['dictionaries'],'Inbound Flight');
    ?>
    <form method="post" action="/booking">
      <button type="submit">Reserve</button>
    </form>
  </body>
</html>
