<?php

defined( 'ABSPATH' ) or exit;

$list = $args['list'] ?? [];

if ( ! empty( $list ) ) : ?>
  <div class="mit-anchor-nav">
    <div class="container">
      <ul class="list-inline text-center">
        <?php foreach ( $list as $item ) : ?>
          <li class="list-inline-item">
            <a href="<?= isset( $item['link'] ) ? esc_url( $item['link'] ) : '#' ?>"><?= $item['label'] ?? 'Link' ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif;
