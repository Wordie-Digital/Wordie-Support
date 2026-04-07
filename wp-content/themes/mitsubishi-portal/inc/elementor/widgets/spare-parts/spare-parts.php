<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Spare_Parts extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Spare_Parts';
  }

  public function get_title() {
    return 'Spare Parts Enquiry';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [];
  }

  public function get_style_depends() {
    return [];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'eta_recipient',
      [
        'label'       => __( 'ETA Recipient Email', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'placeholder' => 'Eg: parts@meaust.meap.com',
        'default'     => 'parts@meaust.meap.com',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'porel-spare-parts-' );

    $eta_recipient = $this->get_settings_for_display( 'eta_recipient' );

    // $filter_id = 'por_spare_parts_filter';

    $is_bom_visible = true; // Visible to all now
    /*$is_bom_visible =
      current_user_can( 'me_user_parts_distributor' ) ||
      current_user_can( 'administrator' ) ||
      current_user_can( 'me_product_team' ) ||
      current_user_can( 'me_portal_admin' );*/

    ?>
    <div id="<?= $uid ?>" class="porel-spare-parts">
      <ul class="nav nav-tabs pe-0" role="tablist">
        <?php if ( $is_bom_visible ) : ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="<?= $uid ?>-tab__bom" data-bs-toggle="tab" data-bs-target="#<?= $uid ?>-tab-pane__bom" type="button" role="tab" aria-controls="<?= $uid ?>-tab-pane__bom" aria-selected="false">Model Search</button>
          </li>
        <?php endif; ?>

        <li class="nav-item" role="presentation">
          <button class="nav-link <?= ! $is_bom_visible ? 'active' : '' ?>" id="<?= $uid ?>-tab__spare-part" data-bs-toggle="tab" data-bs-target="#<?= $uid ?>-tab-pane__spare-part" type="button" role="tab" aria-controls="<?= $uid ?>-tab-pane__spare-part" aria-selected="true">Parts Search</button>
        </li>

        <li class="nav-item nav-item--eta ms-auto me-0" role="presentation">
          <button class="nav-link position-relative" data-bs-toggle="modal" data-bs-target="#<?= "$uid" ?>__etaRequestModal" style="background:red;color:#fff;font-weight:500;">
            ETA Request
            <i style="vertical-align:-4px;" data-feather="mail"></i>
            <span class="porel-spare-parts__eta-count"></span>
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade <?= ! $is_bom_visible ? 'show active' : '' ?>" id="<?= $uid ?>-tab-pane__spare-part" role="tabpanel" aria-labelledby="<?= $uid ?>-tab__spare-part" tabindex="0">
          <form id="<?= $uid ?>__spare-parts-form" class="porel-spare-parts__search-form" onsubmit="return false;">
            <div class="row">
              <div class="col-lg-2 mb-3 mb-lg-0">
                <label for="<?= $uid ?>-inputPartType" class="form-label">Part Type</label>
                <select id="<?= $uid ?>-inputPartType" class="form-select" style="width:100%;">
                  <option value="" selected>Any</option>
                  <?
                  $terms = get_terms( [
                    'taxonomy'   => 'part_type',
                    'hide_empty' => true,
                  ] );

                  foreach ( $terms as $term ) {
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                  }
                  ?>
                </select>
              </div>

              <div class="col-lg-2 col-xl-1 mb-3 mb-lg-0">
                <label for="<?= $uid ?>-inputAvailable" class="form-label">Available</label>
                <select id="<?= $uid ?>-inputAvailable" class="form-select">
                  <option value="" selected>Any</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>

              <div class="col-lg-7">
                <label for="<?= $uid ?>-inputSearch" class="form-label">Search</label>
                <input id="<?= $uid ?>-inputSearch" type="search" class="form-control" placeholder="Enter part number, model number, or any keywords…" aria-label="Search">
              </div>

              <div class="col-lg-2">
                <button type="submit" class="btn btn--default btn-fullwidth">
                  Search
                  <i style="vertical-align:-2px;" data-feather="search"></i>
                </button>
              </div>
            </div>
          </form>

          <div class="porel-spare-parts__results mt-5" style="display:none;">
            <div class="porel-spare-parts__table table">
              <div class="porel-spare-parts__table-body">
                <?
                ////////////////////////////////////////////////////////////////
                // Infinite ajax load more posts
                $args = [
                  'id'                           => $uid,
                  'container_type'               => 'div',
                  'post_type'                    => 'cpt-spare-part',
                  'posts_per_page'               => 20,
                  'preloaded'                    => 'true',
                  'preloaded_amount'             => 20,
                  'transition_container_classes' => "",
                  'images_loaded'                => 'true',
                  'no_results_text'              => POR_ALM_NO_RESULTS_TEXT,
                  'archive'                      => 'false',
                  'pause'                        => 'true',
                  'scroll'                       => 'false',
                  'vars'                         => 'etaModalId:' . $uid . '__etaRequestModal',
                ];

                /*if ( ! empty( $filter_id ) ) {
                  $args['filters'] = 'true';
                  $args['target']  = $filter_id;
                }*/

                // Render
                if ( function_exists( 'alm_render' ) ) {
                  alm_render( $args );
                }
                ?>
              </div>
            </div>
          </div>

          <script>
            /* global ajaxloadmore */
            jQuery(function ($) {
              const reloadAjaxLoadMore = function (e) {
                setTimeout(function () {
                  $('<?="#{$uid} .porel-spare-parts__results" ?>').show();
                }, 200);

                e.preventDefault();

                const search = $('<?="#{$uid}__spare-parts-form [type=\"search\"]"?>').val();
                const available = $('<?="#{$uid}-inputAvailable"?>').val();
                const type = $('<?="#{$uid}-inputPartType"?>').val();

                if ('ajaxloadmore' in window) {
                  const transition = 'fade';
                  const speed = 250;
                  let meta = {
                    search: search
                  };

                  meta = {
                    'meta-key': 'available',
                    'meta-value': available,
                    'meta-compare': '=',
                    'meta-relation': 'OR',
                    'taxonomy': 'part_type',
                    'taxonomy-terms': type,
                    ...meta
                  };

                  // Call core Ajax Load More `filter` function.
                  // @see https://connekthq.com/plugins/ajax-load-more/docs/public-functions/#filter
                  ajaxloadmore.filter(transition, speed, {
                    target: '<?= $uid ?>',
                    pause: 'false',
                    ...meta
                  });
                }
              };

              // Triggered on search input throttle
              $('<?="#{$uid}__spare-parts-form" ?>').on('submit', function (e) {
                // Disable submit button
                const $submitButton = $('<?="#{$uid}__spare-parts-form [type=\"submit\"]"?>');
                $submitButton.prop('disabled', true);

                setTimeout(function () {
                  $submitButton.prop('disabled', false);
                }, 3000);

                // Send datalayer
                const dataLayer = window.dataLayer || [];
                dataLayer.push({
                  'event': 'portal-spare-part--query',
                  'q': $(this).find('[type=search]').val(),
                  'timestamp': new Date().getTime(),
                  'username': '<?= wp_get_current_user()->user_login ?>',
                  'user_firstname': '<?= wp_get_current_user()->user_firstname ?>',
                  'user_lastname': '<?= wp_get_current_user()->user_lastname ?>',
                  'user_company': '<?= get_user_meta( get_current_user_id(), 'billing_company', true ) ?>',
                });

                reloadAjaxLoadMore(e);
              });

              // Scrollbars
              OverlayScrollbars($('.porel-spare-parts__results'), {
                overflowBehavior: {
                  x: 'hidden',
                  y: 'scroll',
                },
                scrollbars: {
                  autoHide: 'leave',
                  autoHideDelay: 200,
                }
              });

              // Select 2
              $('<?="#{$uid}-inputPartType"?>').select2();
            });
          </script>
        </div>

        <?php if ( $is_bom_visible ) : ?>
          <div class="tab-pane fade show active" id="<?= $uid ?>-tab-pane__bom" role="tabpanel" aria-labelledby="<?= $uid ?>-tab__bom" tabindex="0">
            <form id="<?= $uid ?>__bom-form" class="porel-spare-parts__search-form" onsubmit="BOM.submitBomForm(this); return false;">
              <div class="row">
                <div class="col-lg-10">
                  <label for="<?= $uid ?>-bom-inputSearch" class="form-label">Search</label>
                  <input id="<?= $uid ?>-bom-inputSearch" type="search" class="form-control" placeholder="Enter material name" aria-label="Search">
                </div>

                <div class="col-lg-2">
                  <button type="submit" class="btn btn--default btn-fullwidth">
                    Search
                    <i style="vertical-align:-2px;" data-feather="search"></i>
                  </button>
                </div>
              </div>
            </form>

            <div class="porel-spare-parts__bom-results mt-5" style="display:none;">
              <div class="porel-spare-parts__table table">
                <div class="porel-spare-parts__table-body">
                  <?
                  ////////////////////////////////////////////////////////////////
                  // Infinite ajax load more posts
                  $bom_args = [
                    'id'                           => "$uid-bom",
                    'container_type'               => 'div',
                    'post_type'                    => 'cpt-material',
                    'posts_per_page'               => 20,
                    'preloaded'                    => 'true',
                    'preloaded_amount'             => 20,
                    'transition_container_classes' => "",
                    'images_loaded'                => 'true',
                    'no_results_text'              => POR_ALM_NO_RESULTS_TEXT,
                    'archive'                      => 'false',
                    'pause'                        => 'true',
                    'scroll'                       => 'false',
                    'order'                        => 'ASC',
                    'orderby'                      => 'title',
                  ];

                  /*if ( ! empty( $filter_id ) ) {
                    $args['filters'] = 'true';
                    $args['target']  = $filter_id;
                  }*/

                  // Render
                  if ( function_exists( 'alm_render' ) ) {
                    alm_render( $bom_args );
                  }
                  ?>
                </div>
              </div>
            </div>

            <script defer>
              jQuery(function ($) {
                const BOM = window['BOM'] || {};

                BOM.keyAdded = 'bom_eta_added_spare_parts';

                // Triggered on search input throttle
                BOM.reloadAjaxLoadMoreForBom = function (extraMeta = {}) {
                  setTimeout(function () {
                    $('<?="#{$uid} .porel-spare-parts__bom-results" ?>').show();
                  }, 200);

                  const search = $('<?="#{$uid}__bom-form [type=\"search\"]"?>').val();

                  if ('ajaxloadmore' in window) {
                    const transition = 'fade';
                    const speed = 250;
                    let meta = Object.assign({
                      search: search
                    }, extraMeta);

                    // Call core Ajax Load More `filter` function.
                    // @see https://connekthq.com/plugins/ajax-load-more/docs/public-functions/#filter
                    ajaxloadmore.filter(transition, speed, {
                      target: '<?= "$uid-bom" ?>',
                      pause: 'false',
                      ...meta
                    });
                  }
                };

                // Handle submit form
                BOM.submitBomForm = function (form) {
                  // Disable submit button
                  const $submitButton = $(form).find('[type=submit]');
                  $submitButton.prop('disabled', true);

                  setTimeout(function () {
                    $submitButton.prop('disabled', false);
                  }, 3000);

                  // Send datalayer
                  const dataLayer = window.dataLayer || [];
                  dataLayer.push({
                    'event': 'portal-bom--query',
                    'q': $(form).find('[type=search]').val(),
                    'timestamp': new Date().getTime(),
                    'username': '<?= wp_get_current_user()->user_login ?>',
                    'user_firstname': '<?= wp_get_current_user()->user_firstname ?>',
                    'user_lastname': '<?= wp_get_current_user()->user_lastname ?>',
                    'user_company': '<?= get_user_meta( get_current_user_id(), 'billing_company', true ) ?>',
                  });

                  BOM.reloadAjaxLoadMoreForBom();
                };

                // Function to render spare part rows
                BOM.getRowsHtml = function (partsList, sortColumn = '', sortDirection = '') {
                  if (sortColumn) {
                    partsList.sort(function (a, b) {
                      // Convert to float if sortColumn is a number or float number string
                      if ((a[sortColumn] + '').match(/^[0-9]+(\.[0-9]+)?$/)) {
                        a[sortColumn] = parseFloat(a[sortColumn]);
                      }
                      if ((b[sortColumn] + '').match(/^[0-9]+(\.[0-9]+)?$/)) {
                        b[sortColumn] = parseFloat(b[sortColumn]);
                      }

                      if (sortDirection === 'asc') {
                        if (a[sortColumn] < b[sortColumn]) {
                          return -1;
                        }
                        if (a[sortColumn] > b[sortColumn]) {
                          return 1;
                        }
                        return 0;
                      } else {
                        if (a[sortColumn] > b[sortColumn]) {
                          return -1;
                        }
                        if (a[sortColumn] < b[sortColumn]) {
                          return 1;
                        }
                        return 0;
                      }
                    });
                  }

                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};
                  const addedSparePartsIds = Object.keys(addedSparePartsObject);

                  return !partsList.length ? '<p class="text-center pt-2" style="height:37px;">No Parts List Found</p>' : partsList.map(function (part) {
                    const addedToETA = addedSparePartsIds.includes(part.id + '');

                    return `
                    <div class="porel-spare-parts__table-row" style="background-color:#fff;" data-part-id="${part.id}">
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--part-number">${part.partNumber}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--description">${part.partDescription}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--qty">${part.partQuantity}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price text-end pe-lg-4">$${part.partPrice1}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--price text-end pe-lg-4">$${part.partPrice2}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--types">${part.partType}</div>
                      <div class="porel-spare-parts__table-col porel-spare-parts__table-col--available text-start">${'no' === part.availability.toLowerCase() ? `No <button class="porel-spare-parts__request-eta-btn" data-bs-toggle="modal" data-bs-target="#<?= "$uid" ?>__etaRequestModal" data-bom-request-eta-part-id='${part.id}' data-bom-request-eta='${JSON.stringify(part)}' ${addedToETA ? 'disabled' : ''} title="Add to ETA request form for this spare part">${addedToETA ? Portal['addedToEtaRequestString'] : Portal['addToEtaRequestString']}</button>` : part.availability}</div>
                    </div>
                  `;
                  }).join('');
                };

                // Function to render eta rows in modal
                BOM.renderEtaRequestRows = function () {
                  const $tableBody = $('<?="#$uid"?>__eta-request-modal-table-body');
                  const $sendButton = $('<?="#{$uid} [data-bom-send-eta-request]" ?>');
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};

                  // Check if there are added spare parts
                  if (Object.keys(addedSparePartsObject).length === 0) {
                    $tableBody.html('<tr><td colspan="4" class="text-center">No spare parts added to the request yet</td></tr>');

                    // Disable send request button
                    $sendButton.prop('disabled', true);
                  } else {
                    $tableBody.html('');

                    // Loop through added spare parts
                    for (const partNumber in addedSparePartsObject) {
                      if (addedSparePartsObject.hasOwnProperty(partNumber)) {
                        const part = addedSparePartsObject[partNumber];
                        const rowHtml = `
                        <tr>
                          <td>${part.material}</td>
                          <td>${part.partNumber}</td>
                          <td><input type="number" value="${part.partQuantity}" min="1" onchange="BOM.updateEtaRequestQuantity(this, ${part.id})" class="form-control"/></td>
                          <td class="text-end"><a href="#" style="color:var(--e-global-color-primary)" onclick="BOM.removeFromEta(${part.id}); return false;"><i data-feather="x-circle"></i> Remove</a></td>
                        </tr>
                      `;

                        $tableBody.append(rowHtml);
                        feather.replace();
                      }
                    }

                    // Enable send request button
                    $sendButton.prop('disabled', false);
                  }

                  // Update ETA request count
                  BOM.updateEtaRequestCount();
                };

                // Download button status
                BOM.handleDownload = function (element, type = '') {
                  const self = $(element);
                  // add multiple classes
                  self.addClass('disabled loading');

                  // Send datalayer
                  const dataLayer = window.dataLayer || [];
                  dataLayer.push({
                    'event': 'portal-bom--download',
                    'download_type': type,
                    'model_name': '' + self.closest('[data-bom-row]').find('[data-bom-row-title]').text(),
                    'model_description': self.closest('[data-bom-row]').find('[data-bom-row-desc]').text(),
                  });

                  setTimeout(function () {
                    self.removeClass('disabled loading');
                  }, 2000);
                };

                // Sort bom results
                BOM.sortBomResults = function (element) {
                  const $self = $(element);
                  const $closestWrapper = $(element).closest('[data-bom-row]');
                  const partsList = $closestWrapper.data('bom-row');
                  const sortColumn = $self.data('bom-sort');
                  const currentSortDirection = $self.data('bom-sort-direction');
                  const newSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
                  const partsListHtml = BOM.getRowsHtml(partsList, sortColumn, newSortDirection);

                  $closestWrapper.find('[data-bom-list-ajax]').replaceWith(`<div data-bom-list-ajax>${partsListHtml}</div>`);
                  $self.data('bom-sort-direction', newSortDirection);
                };

                // Sort models results
                BOM.sortModelsResults = function (element) {
                  const $self = $(element);
                  const searchButton = $('<?="#{$uid} .porel-spare-parts__search-form [type=\"submit\"]"?>');
                  const sortColumn = $self.data('bom-sort-model');
                  const currentSortDirection = searchButton.data('bom-sort-model-direction');
                  const newSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
                  console.log(newSortDirection);

                  switch (sortColumn) {
                    case 'post_content':
                      BOM.reloadAjaxLoadMoreForBom({
                        sort_key: 'post_content',
                        orderby: 'meta_value',
                        order: newSortDirection,
                      });
                      break;

                    default:
                      BOM.reloadAjaxLoadMoreForBom({
                        orderby: sortColumn,
                        order: newSortDirection,
                      });
                  }

                  searchButton.data('bom-sort-model-direction', newSortDirection);
                };

                // Toggle bom results
                BOM.toggleList = function (element) {
                  const $self = $(element);
                  const $closestWrapper = $(element).closest('[data-bom-row]');
                  const loaded = $self.data('loaded');
                  const model = $closestWrapper.find('[data-bom-row-title]').text();
                  const desc = $closestWrapper.find('[data-bom-row-desc]').text();

                  // Send datalayer
                  if (!$closestWrapper.hasClass('active')) {
                    const dataLayer = window.dataLayer || [];
                    dataLayer.push({
                      'event': 'portal-bom--view-entry',
                      'model_name': '' + model,
                      'model_description': desc,
                    });
                  }

                  $('<?="#{$uid}"?> [data-bom-row].active').not($closestWrapper).removeClass('active').find('[data-bom-list]').slideUp();
                  $closestWrapper.toggleClass('active').find('[data-bom-list]').slideToggle();

                  if (!loaded) {
                    const materialId = $self.data('bom-material-id');
                    $.ajax({
                      url: Portal.ajaxUrl,
                      method: 'POST',
                      data: {
                        action: 'get_material_parts_list',
                        material_id: materialId,
                      },
                      success: function (response) {
                        const partsList = response.data.partsList;
                        const partsListHtml = BOM.getRowsHtml(partsList);

                        $self.data('loaded', true);
                        $closestWrapper.find('[data-bom-list-ajax]').replaceWith(`<div data-bom-list-ajax>${partsListHtml}</div>`);

                        // Save data for sorting
                        $closestWrapper.data('bom-row', partsList);
                      }
                    });
                  }
                };

                // Send BOM ETA request
                BOM.sendEtaRequest = function (element) {
                  const $self = $(element);
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};

                  // Send datalayer
                  const dataLayer = window.dataLayer || [];
                  dataLayer.push({
                    'event': 'portal-bom--send-eta',
                    'timestamp': new Date().getTime(),
                    'username': '<?= wp_get_current_user()->user_login ?>',
                    'user_firstname': '<?= wp_get_current_user()->user_firstname ?>',
                    'user_lastname': '<?= wp_get_current_user()->user_lastname ?>',
                    'user_company': '<?= get_user_meta( get_current_user_id(), 'billing_company', true ) ?>',
                  });

                  // Send ajax request
                  $.ajax({
                    url: Portal.ajaxUrl,
                    method: 'POST',
                    data: {
                      action: 'send_eta_request',
                      recipient: '<?=$eta_recipient?>',
                      eta_request: addedSpareParts,
                      eta_reference: $('<?="#{$uid}__eta-request-reference"?>').val(),
                    },
                    beforeSend: function () {
                      $self.prop('disabled', true);
                      $self.addClass('loading');
                    },
                    success: function (response) {
                      if (response.success) {
                        // Remove all items from session
                        Object.keys(addedSparePartsObject).forEach(function (partId) {
                          BOM.removeFromEta(partId);
                        });

                        // Update ETA request table
                        BOM.renderEtaRequestRows();

                        // Clear ref field
                        $('<?="#{$uid}__eta-request-reference"?>').val('');
                      } else {
                        // Re-enable button
                        $self.prop('disabled', false);
                      }

                      // Show message
                      window.alert(response.data.message);

                      // Remove loading class
                      $self.removeClass('loading');
                    }
                  });
                };

                // Remove from ETA request
                BOM.removeFromEta = function (partId) {
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};

                  // Remove from array
                  delete addedSparePartsObject[partId];

                  // Store in session
                  window.sessionStorage.setItem(BOM.keyAdded, JSON.stringify(addedSparePartsObject));

                  // Render ETA rows
                  BOM.renderEtaRequestRows();

                  // Re-render BOM add to eta
                  $(`<?="#$uid" ?> [data-bom-request-eta-part-id=${partId}]`).each(function () {
                    const $self = $(this);
                    $self.prop('disabled', false);
                    $self.text(Portal['addToEtaRequestString']);
                  });
                };

                // Update ETA request quantity
                BOM.updateEtaRequestQuantity = function (qtyInput, partId) {
                  const $self = $(qtyInput);
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};

                  // Update quantity
                  addedSparePartsObject[partId].partQuantity = $self.val();

                  // Store in session
                  window.sessionStorage.setItem(BOM.keyAdded, JSON.stringify(addedSparePartsObject));
                };

                // Update ETA request count
                BOM.updateEtaRequestCount = function () {
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};
                  const count = Object.keys(addedSparePartsObject).length;

                  $('<?="#{$uid} .porel-spare-parts__eta-count" ?>').text(count ? `${count}` : '');
                };

                // Update Add buttons status across the page (applied to all tabs)
                BOM.updateButtonsStatus = function () {
                  const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                  const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};
                  const addedSparePartsIds = Object.keys(addedSparePartsObject);

                  // Loop through all buttons
                  const buttons = document.querySelectorAll('[data-bom-request-eta-part-id]');
                  buttons.forEach(function (button) {
                    const partId = button.getAttribute('data-bom-request-eta-part-id');
                    const addedToETA = addedSparePartsIds.includes(partId + '');

                    button.disabled = addedToETA;
                    button.innerHTML = addedToETA ? Portal['addedToEtaRequestString'] : Portal['addToEtaRequestString'];
                  });
                };

                // Initialize BOM
                (function () {
                  // Render ETA rows on page load
                  BOM.renderEtaRequestRows();

                  // Add BOM to request ETA form on toggling modal from a row
                  document.getElementById('<?= "$uid" ?>__etaRequestModal').addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    const partJsonStr = button.getAttribute('data-bom-request-eta');

                    if (partJsonStr) {
                      const part = JSON.parse(partJsonStr);
                      const addedSpareParts = window.sessionStorage.getItem(BOM.keyAdded);
                      const addedSparePartsObject = addedSpareParts ? JSON.parse(addedSpareParts) : {};

                      // Add to array as string
                      addedSparePartsObject[part.id] = part;

                      // Store in session
                      window.sessionStorage.setItem(BOM.keyAdded, JSON.stringify(addedSparePartsObject));

                      // Disable buttons
                      const relatedButtons = document.querySelectorAll(`[data-bom-request-eta-part-id='${part.id}']`);
                      relatedButtons.forEach(function (button) {
                        button.disabled = true;
                        button.innerHTML = Portal['addedToEtaRequestString'];
                      });

                      // Send datalayer
                      const dataLayer = window.dataLayer || [];
                      dataLayer.push({
                        'event': 'portal-bom--add-part-to-eta',
                        'part_model': '' + part.material,
                        'part_number': '' + part.partNumber,
                        'part_description': part.partDescription,
                        'part_quantity': part.partQuantity,
                        'part_price1': part.partPrice1,
                        'part_price2': part.partPrice2,
                        'part_type': part.partType,
                        'part_available': part.availability,
                      });

                      // Re-render table
                      BOM.renderEtaRequestRows();
                    }
                  });

                  // Scrollbars
                  OverlayScrollbars($('.porel-spare-parts__bom-results'), {
                    overflowBehavior: {
                      x: 'hidden',
                      y: 'scroll',
                    },
                    scrollbars: {
                      autoHide: 'leave',
                      autoHideDelay: 200,
                    }
                  });

                  // Add BOM to window
                  window['BOM'] = BOM;
                })();
              });
            </script>
          </div>
        <?php endif; ?>
      </div>

      <?php if ( $is_bom_visible ) : ?>
        <!-- Modal -->
        <div class="modal fade" id="<?= "$uid" ?>__etaRequestModal" tabindex="-1" aria-labelledby="<?= "$uid" ?>__etaRequestModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="<?= "$uid" ?>__etaRequestModalLabel">ETA Request</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Spare parts added to the ETA Request Form</p>
                <!-- Add a Reference Text field -->
                <div class="mb-3">
                  <label for="<?= "$uid" ?>__eta-request-reference" class="form-label">Reference Text</label>
                  <input type="text" class="form-control rounded-0" id="<?= "$uid" ?>__eta-request-reference" placeholder="Your Reference Text">
                </div>

                <table class="table table-bordered w-100" style="table-layout:fixed;">
                  <thead>
                  <tr>
                    <th scope="col" style="width:36%;">Model</th>
                    <th scope="col">Part Number</th>
                    <th scope="col" style="width:80px;">Req. Qty</th>
                    <th scope="col" class="text-end">Actions</th>
                  </tr>
                  </thead>

                  <tbody id="<?= "$uid" ?>__eta-request-modal-table-body">
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn--link me-3" data-bs-dismiss="modal"><span class="small">Add another spare part</span></button>
                <button type="button" class="btn btn--default" disabled data-bom-send-eta-request onclick="BOM.sendEtaRequest(this); return false;">
                  <span>Send Request</span>
                  <img class="loading-icon" src="<?= POR_Core::instance()->helpers->get_assets_path( 'images/loading.svg' ) ?>" alt="Loading">
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <?php
  }
}
