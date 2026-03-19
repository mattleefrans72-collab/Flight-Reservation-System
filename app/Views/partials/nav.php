

<div class="header">
  <div class="left-header">
    <a href="/" class="nav-logo">Books</a>

    <a href="/" class="nav-link">Home</a>
    <a href="/flight" class="nav-link">Book Flights</a>

    <?php if ($_SESSION['user'] ?? false): ?>
      <a href="/flight/manage" class="nav-link">Manage Flight</a>
    <?php endif; ?>
  </div>

  <div class="mid-header"></div>

  <div class="right-header">
    <?php if ($_SESSION['user'] ?? false): ?>
      <form action="/session" method="POST" class="logout-form">
        <input type="hidden" name="_method" value="DELETE">
        <button class="logout-button">Logout</button>
      </form>
    <?php else: ?>
      <a href="/login" class="nav-link nav-auth">Login</a>
      <a href="/register" class="nav-link nav-auth nav-primary">Register</a>
    <?php endif; ?>
  </div>
</div>