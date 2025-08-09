<!DOCTYPE html>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style/general.css">
    <link rel="stylesheet" href="style/page1.css">
    <link rel="stylesheet" href="style/header.css">
  </head>
  <body>
    <div class="header">
      <div class="left-header">
        <button class="js-home home-button">
          Home
        </button>
        <button class="book-button">
          Book Flights
        </button>
        <button class="manage-button">
          Manage Flight
        </button>
      </div>
      <div class="mid-header">
        
      </div>
      <div class="right-header">
        <button class="profile-button">
          profile
        </button>
      </div>
    </div>
    <div class="main-container">
      <div class="box">
        <div class="flying-location">
          <div class="flying-from">
            <div>FLYING FROM</div>
            <input class="from-input js-from-input" type="text">
            <div class="js-dropdown-menu dropdown-menu">
              
            </div>
          </div>
          <div class="flying-to">  
            <div>FLYING TO</div>
            <input class="to-input js-to-input" type="text">
            <div class="js-dropdown-menu dropdown-menu">
              
            </div>
          </div>
        </div>

        <div class="flying-dates">
          <div class="departing"> 
            <div>DEPARTING</div>
            <input class="departing-input js-departing-input" type="date">
          </div>
          <div class="returning">
            <div>RETUNING</div>
            <input class="returning-input js-returning-input" type="date">
          </div>
        </div>

        <div class="flying-age">
          <div class="adults">
            <div>ADULT(18+)</div>
            <input class="adults-input js-adult-input" type="text">
          </div>
          <div class="childrens">
            <div>CHILDRENS</div>
            <input class="childrens-input js-children-input" type="text">
          </div>
        </div>
        
        <div class="class-booking">
          <div>TRAVEL CLASS</div>
          <input class="flying-class js-class-input" type="text">  
          <button class="flights-button js-flights-button">SHOWS FLIGHTS</button>
          <div class="filled-out filled-out-none">All field must be filled out</div>
          
        </div>
      
      </div>
    </div>
    <script type="module" src="script/utils/heading.js"></script>
    <script type="module" src="script/page1.js"></script>
 
  </body>
</html>