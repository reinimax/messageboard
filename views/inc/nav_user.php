<nav class="navbar navbar-expand-xl bg-light fixed-top justify-content-between">
  <a class="navbar-brand mx-5" href="/">Startpage</a>
  <ul class="navbar-nav mx-5">
    <span class="navbar-text">
    Welcome, <strong><?php echo $_SESSION['user']; ?></strong>
    </span>
    <li class="nav-item">
      <a class="nav-link btn btn-outline-info mx-2" href="/logout">Logout</a>
    </li>
  </ul>
</nav>
