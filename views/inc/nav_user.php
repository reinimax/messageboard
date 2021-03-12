<nav class="navbar navbar-expand-xl bg-light fixed-top justify-content-between">
  <a class="navbar-brand mx-5" href="/">Startpage</a>
  <?php include ROOT.'/views/inc/search.php'; ?>
  <ul class="navbar-nav mx-5">
    <span class="navbar-text">Welcome,</span>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
        <strong><?php echo $_SESSION['user']; ?></strong>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="/show.php">Show all my posts</a>
        <a class="dropdown-item" href="/settings.php">Settings</a>
        <a class="dropdown-item" href="/logout.php">Logout</a>
      </div>
    </li>
  </ul>
</nav>
