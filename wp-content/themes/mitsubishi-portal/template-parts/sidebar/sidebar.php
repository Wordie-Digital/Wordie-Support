<nav id="sidebar" class="por-sidebar sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">
    <a class="sidebar-brand" href="https://www.mitsubishielectric.com.au/">
        <span class="align-middle">
          <img class="img-fluid" width="280" src="<? POR_Core::instance()->helpers->the_assets_path( 'images/logo-au-white.svg' ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ) ?>">
        </span>
    </a>

    <div class="por-sidebar__user-info d-flex align-items-center">
      <?= get_avatar( get_current_user_id(), 200, '', wp_get_current_user()->first_name, [
        'class' => 'img-fluid rounded-circle',
      ] ) ?>
      <div class="ms-3">
        <small>Welcome back</small><br>
        <strong style="word-break: break-word"><?= ucwords( wp_get_current_user()->first_name ?: wp_get_current_user()->display_name ) ?></strong>
      </div>
    </div>

    <?
    $menu_items_array = [];

    if ( isset( ( $menu_locations = get_nav_menu_locations() )['primary_menu'] ) ) {
      $menu_items = wp_get_nav_menu_items( $menu_locations['primary_menu'] );

      foreach ( $menu_items as $menu_item ) {
        $menu_item->menu_item_parent = absint( $menu_item->menu_item_parent );

        if (
          0 != absint( $menu_item->menu_item_parent ) &&
          isset( $menu_items_array[ $menu_item->menu_item_parent ] )
        ) {
          $menu_items_array[ $menu_item->menu_item_parent ]['children'][] = $menu_item;
        } else {
          $menu_items_array[ $menu_item->ID ]['menu'] = $menu_item;
        }
      }
    }

    if ( ! empty( $menu_items_array ) ) : ?>
      <ul class="sidebar-nav">
        <?php foreach ( $menu_items_array as $item ) : ?>
          <?php if ( empty( $item['children'] ) ) : ?>
            <?php if ( function_exists( 'wppb_check_content_restriction_on_post_id' ) && wppb_check_content_restriction_on_post_id( $item['menu']->object_id ) ) {
              continue;
            } ?>
            <li class="sidebar-item <?= esc_attr( implode( ' ', $item['menu']->classes ) ) ?> <?= get_the_ID() == $item['menu']->object_id ? 'active' : '' ?>">
              <a class="sidebar-link" href="<?= esc_url( $item['menu']->url ) ?>">
                <span class="align-middle"><?= $item['menu']->title ?></span>
              </a>
            </li>
          <?php else: ?>
            <?php if ( function_exists( 'wppb_check_content_restriction_on_post_id' ) && wppb_check_content_restriction_on_post_id( $item['menu']->object_id ) ) {
              continue;
            } ?>
            <li class="sidebar-item <?= esc_attr( implode( ' ', $item['menu']->classes ) ) ?> <?= get_the_ID() == $item['menu']->object_id ? 'active' : '' ?>">
              <div class="sidebar-link d-flex align-items-center justify-content-between">
                <a href="<?= esc_url( $item['menu']->url ) ?>">
                  <span class="align-middle"><?= $item['menu']->title ?></span>
                </a>

                <?
                /* Look for active child */
                $has_active_child = false;
                foreach ( $item['children'] as $child ) {
                  if ( get_the_ID() == $child->object_id ) {
                    $has_active_child = true;
                    break;
                  }
                }
                ?>
                <a data-bs-target="#sidebar-submenu-<?= $item['menu']->ID ?>" data-bs-toggle="collapse" aria-expanded="<?= $has_active_child ? 'true' : 'false' ?>" class="<?= $has_active_child ? '' : 'collapsed' ?>">
                  <i class="align-middle" data-feather="chevron-down"></i> <span class="visually-hidden">Toggle</span>
                </a>
              </div>

              <ul id="sidebar-submenu-<?= $item['menu']->ID ?>" class="sidebar-dropdown list-unstyled collapse <?= $has_active_child ? 'show' : '' ?>" data-bs-parent="#sidebar">
                <?php foreach ( $item['children'] as $child ) : ?>
                  <?php if ( function_exists( 'wppb_check_content_restriction_on_post_id' ) && wppb_check_content_restriction_on_post_id( $child->object_id ) ) {
                    continue;
                  } ?>
                  <li class="sidebar-item <?= esc_attr( implode( ' ', $child->classes ) ) ?>">
                    <a class="sidebar-link <?= get_the_ID() == $child->object_id ? 'sidebar-link--active' : '' ?>" href="<?= esc_url( $child->url ) ?>"><?= $child->title ?></a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <? endif; ?>

    <div class="sidebar-cta">
      <div class="sidebar-cta-content">
        <ul class="list-unstyled">
          <li><a href="<?= get_permalink( get_page_by_path( 'account-settings' ) ) ?>"><i class="align-middle" data-feather="settings"></i> Account Settings</a></li>
          <li><a href="<?= get_permalink( get_page_by_path( 'support' ) ) ?>"><i class="align-middle" data-feather="message-square"></i> Support</a></li>
          <li><a href="<?= get_privacy_policy_url() ?>" target="_blank"><i class="align-middle" data-feather="file-text"></i> Policies</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
