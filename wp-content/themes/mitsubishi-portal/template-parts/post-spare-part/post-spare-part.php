<?php

defined( 'ABSPATH' ) or exit;

global $post;

$index      = $args['index'] ?? 0;
$etaModalId = $args['etaModalId'] ?? '';
$available  = get_field( 'available' ) ?? 'No';
$helpers    = POR_Core::instance()->helpers;

if ( 1 == $index ) : ?>
  <div class="porel-spare-parts__table-row porel-spare-parts__table-row--head">
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--part-number">Part #</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--title">Description</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price">Trade Price<br> (Ex. GST)</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price">Trade Price<br> (Incl. GST)</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--types">Part Type</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--available">Available</div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--compatible-models">Compatible Models</div>
  </div>
<?php endif; ?>

  <div class="porel-spare-parts__table-row" <?= $index ? "data-item=\"$index\"" : '' ?>>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--part-number"><?= ltrim( get_field( 'part_number' ), '0' ); ?></div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--title"><?= get_the_title() ?></div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price"><?= ( $price_1 = floatval( get_field( 'price_1' ) ) ) ? wc_price( $price_1 ) : '' ?></div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price"><?= ( $price_2 = floatval( get_field( 'price_2' ) ) ) ? wc_price( $price_2 ) : '' ?></div>
    <? /*<div class="porel-spare-parts__table-col porel-spare-parts__table-col--price"><?= ( $price_3 = floatval( get_field( 'price_3' ) ) ) ? wc_price( $price_3 ) : '' ?></div>*/ ?>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--types"><?= wp_strip_all_tags( get_the_term_list( get_the_ID(), 'part_type', '', ', ' ) ) ?></div>

    <?php if ( ! empty( $etaModalId ) ) : ?>
      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--available text-start"><?= 'no' == strtolower( $available ) ? 'No <button class="porel-spare-parts__request-eta-btn" data-bs-toggle="modal" data-bs-target="#' . $etaModalId . '" data-bom-request-eta-part-id="' . get_the_ID() . '" data-bom-request-eta="' . esc_attr( json_encode( $helpers->get_part_array( get_the_ID() ) ) ) . '" title="Add to ETA request form for this spare part">' . POR_TEXT_ADD_TO_ETA_REQUEST
                                                                                                                                          . '</button>'
          : $available ?></div>
    <?php else: ?>
      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--available"><?= $available ?></div>
    <?php endif; ?>

    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--compatible-models"><?= str_replace( '|', ', ', get_field( 'compatible_models' ) ) ?></div>
  </div>
<?php
