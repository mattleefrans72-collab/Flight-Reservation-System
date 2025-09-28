

<div class="header">
  <div class="left-header">
    <a href="/">
      Home
    </a>
    <a href="/flight">
      Book Flights
    </a>
    <?php if ($_SESSION['user'] ?? false): ?>
    <a href="/flight/manage">
      Manage Flight
    </a>
    <?php endif; ?>
  </div>
  <div class="mid-header">
    
  </div>
  <div class="right-header">
    <?php if ($_SESSION['user'] ?? false): ?>
      <form action="/session" method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <button class="logout-button">
          Logout
        </button>
      </form>
    <?php else: ?>
      <a href="/login">
        Login
      </a>
      <a href="/register">
        Register
      </a>
    <?php endif; ?>
  </div>

</div>