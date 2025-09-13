
<?php requireStyle (["general", "home.view", "nav"])?>
  <body>

  <?php require "partials/nav.php" ?>

    <div class="main-container">
      <div class="box">

        <form action="flight" method="get">
        <div class="flying-location">
          <div class="flying-from">
            <div>FLYING FROM</div>
            <input name="from" class="from-input js-from-input" type="text" value="<?= old("from") ?>">
            <div class="js-dropdown-menu dropdown-menu"></div>
            <div class="error"><?= errors("from") ?></div> 
          </div>
          <div class="flying-to">  
            <div>FLYING TO</div>
            <input name="to" class="to-input js-to-input" type="text" value="<?= old("to") ?>">
            <div class="js-dropdown-menu dropdown-menu"></div>
            <div class="error"><?= errors("to") ?></div>
          </div>
        </div>

        <div class="flying-dates">
          <div class="departing"> 
            <div>DEPARTING</div>
            <input name="departure" class="departing-input js-departing-input" type="date" value="<?= old("departure") ?>">
          </div>
          <div class="returning">
            <div>RETURNING</div>
            <input name="return" class="returning-input js-returning-input" type="date" value="<?= old("return") ?>">
            <div class="error"><?= errors("dates") ?></div>
          </div>
        </div>

        <div class="flying-age">
          <div class="adults">
            <div>ADULTS(18+)</div>
            <input name="adults" class="adults-input js-adult-input" type="number" value="<?= old("adults") ?>">
          </div>
          
          <div class="childrens">
            <div>CHILDREN</div>
            <input name="children" class="childrens-input js-children-input" type="number" value="<?= old("children") ?>">
            <div class="error"><?= errors("passenger") ?></div>
          </div>
        </div>
        
        <div class="class-booking">
          <div>TRAVEL CLASS</div>
          <select name="class" class="flying-class js-class-input">
            <option value="ANY">Any</option>
            <option value="ECONOMY">Economy</option>
            <option value="PRIMIUM ECONOMY">Premium Economy</option>
            <option value="BUSINESS">Business</option>
            <option value="FIRST">First Class</option>
          </select>
          <button class="flights-button js-flights-button">SHOWS FLIGHTS</button>
          <div class="filled-out filled-out-none">All field must be filled out</div>
          
        </div>
        </form>
      
      </div>
    </div>
    <?php requireModule(["utils/heading", "home"]) ?>      

  </body>
</html>