<?php

defined( 'ABSPATH' ) or exit;

$heading       = $args['heading'] ?? MIT_Core::instance()->helpers->get_page_title();
$description   = $args['description'] ?? '';
$right_content = $args['right_content'] ?? '';

?>
<div class="mit-section-heading mb-4 mb-xxl-5">
  <div class="row align-items-center">
    <div class="col-<?= ! empty( $right_content ) ? '6' : '12' ?> mit-section-heading__heading">
      <h2 class="mb-0"><?= $heading ?></h2>
      <?php if ( ! empty( $description ) ) : ?>
        <div class="mt-2">
          <?= wpautop( $description ) ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if (
      ! empty( $right_content ) &&
      false === strpos( $right_content, '<div class="alm-filter--inner"></div>' ) /* Check empty filter */
    ) : ?>
      <div class="col-6 mit-section-heading__filters text-end">
        <?= force_balance_tags( $right_content ) ?>
      </div>
    <?php endif; ?>
  </div>
</div>
