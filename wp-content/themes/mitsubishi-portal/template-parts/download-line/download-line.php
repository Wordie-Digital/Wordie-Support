<?php

defined( 'ABSPATH' ) or exit;

global $post;
$file = get_field( 'file' );

if ( ! empty( $file ) ) : ?>
  <a href="<?= $file['url'] ?>" target="_blank" class="por-download-line d-flex justify-content-between">
    <span><?= get_the_title() ?></span>
    <span class="ms-auto me-5"><?= POR_Core::instance()->helpers->get_size_as_kb( $file['filesize'] ) ?></span>
    <span>Download <i class="align-middle" data-feather="download"></i></span>
  </a>
<?php endif;
