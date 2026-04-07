<?php

defined( 'ABSPATH' ) or exit;

global $post;

$index = $args['index'] ?? 0;

if ( 1 == $index ) : ?>
  <div class="porel-spare-parts__table-row porel-spare-parts__table-row--head">
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--material-title">
      Model
      <button class="porel-spare-parts__sort-models" data-bom-sort-model="title" onclick="BOM.sortModelsResults(this); return false;"><i class="align-middle" data-feather="code" style="transform:rotate(90deg);width:10px;"></i></button>
    </div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--description">
      Description
      <button class="porel-spare-parts__sort-models" data-bom-sort-model="post_content" onclick="BOM.sortModelsResults(this); return false;"><i class="align-middle" data-feather="code" style="transform:rotate(90deg);width:10px;"></i></button>
    </div>
    <div class="porel-spare-parts__table-col porel-spare-parts__table-col--actions text-end">Actions</div>
  </div>
<?php endif; ?>

  <div class="porel-spare-parts__table-row-wrapper" data-bom-row <?= $index ? "data-item=\"$index\"" : '' ?>>
    <div class="porel-spare-parts__table-row">
      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--material-title" data-bom-row-title><?= get_the_title() ?></div>
      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--description" data-bom-row-desc><?= force_balance_tags( $post->post_content ) ?></div>
      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--actions text-end">
        <a href="#" class="porel-spare-parts__table-col--action-toggle-parts-list" onclick="BOM.toggleList(this); return false;" data-bom-material-id="<?= get_the_ID() ?>">View <i class="align-middle" data-feather="list"></i></a>
      </div>
    </div>

    <div class="porel-spare-parts__table-row--parts-list" data-bom-list style="display:none;">
      <div class="p-3">
        <div class="porel-spare-parts__table-col--actions mb-3 text-center">
          <?php
          $tech_docs = get_posts( [
            'post_type'      => 'cpt-technical-doc',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
              [
                'key'     => 'materials',
                'value'   => sprintf( ':"%d";', $post->ID ),
                'compare' => 'LIKE',
              ],
            ],
          ] );

          if ( ! empty( $tech_docs ) ) : ?>
            <div class="d-lg-inline-block">
              <strong>Parts Manual: </strong>
              <a href="<?= get_field( 'file', $tech_docs[0] )['url'] ?>" target="_blank" download>PDF <i class="align-middle" data-feather="download"></i></a>
            </div>
          <? endif; ?>

          <div class="d-lg-inline-block ms-4">
            <strong>Parts List: </strong>

            <a href="<?= add_query_arg( [
              'portal_export' => 'parts_list_pdf',
              'material_id'   => get_the_ID(),
              '_wpnonce'      => wp_create_nonce( 'parts_list_pdf' ),
            ], home_url() ) ?>"
               style="min-width: 60px;"
               onclick="BOM.handleDownload(this, 'pdf');"
            >
              <span>PDF <i class="align-middle" data-feather="download"></i></span>
              <img class="loading-icon" src="<?= POR_Core::instance()->helpers->get_assets_path( 'images/loading.svg' ) ?>" alt="Loading">
            </a>

            <a href="<?= add_query_arg( [
              'portal_export' => 'parts_list_excel',
              'material_id'   => get_the_ID(),
              '_wpnonce'      => wp_create_nonce( 'parts_list_excel' ),
            ], home_url() ) ?>"
               style="min-width: 65px;"
               onclick="BOM.handleDownload(this, 'excel');"
            >
              <span>Excel <i class="align-middle" data-feather="download"></i></span>
              <img class="loading-icon" src="<?= POR_Core::instance()->helpers->get_assets_path( 'images/loading.svg' ) ?>" alt="Loading">
            </a>
          </div>
        </div>

        <hr>

        <div class="porel-spare-parts__table-row porel-spare-parts__table-row--head" style="background-color:#fff;">
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--part-number">Part # <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partNumber' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--description">Description <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partDescription' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--qty">BOM Qty <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partQuantity' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price">Trade Price<br> (Ex. GST) <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partPrice1' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price">Trade Price<br> (Incl. GST) <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partPrice1' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--types">Part Type <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'partType' ) ?></div>
          <div class="porel-spare-parts__table-col porel-spare-parts__table-col--available">Available <?= POR_Core::instance()->helpers->get_spare_part_column_sort_button_html( 'availability' ) ?></div>
        </div>

        <div class="porel-spare-parts__table-row--parts-list-content text-center px-3 pt-2" data-bom-list-ajax>
          <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
