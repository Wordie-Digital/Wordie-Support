<nav class="por-navbar navbar navbar-expand navbar-light navbar-bg">
  <a class="sidebar-toggle js-sidebar-toggle">
    <i class="hamburger align-self-center"></i>
  </a>

  <div class="por-navbar__search">
    <?php echo do_shortcode( '[wpdreams_ajaxsearchlite]' ); ?>

    <? /*
  <form class="d-none d-sm-inline-block">
    <div class="input-group input-group-navbar">
      <input type="search" class="form-control" placeholder="Search…" aria-label="Search">
      <button type="button"><i class="align-middle" data-feather="search"></i></button>
    </div>
  </form>
  */ ?>
  </div>

  <div class="navbar-collapse collapse">
    <ul class="navbar-nav navbar-align">
      <li class="nav-item dropdown">
        <a class="nav-item__logout" href="<?= wp_logout_url( home_url() ) ?>">
          Sign-Out <i class="align-middle" data-feather="log-out"></i>
        </a>
      </li>
    </ul>
  </div>
</nav>
