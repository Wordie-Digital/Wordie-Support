<?php
/**
 * Filters form used with ALM only
 */

defined( 'ABSPATH' ) or exit;

$alm_uid          = $args['alm_uid'] ?? '';
$taxonomy         = $args['taxonomy'] ?? '';
$parent_term_slug = $args['parent_term_slug'] ?? '';

if ( empty( $taxonomy ) || empty( $parent_term_slug ) || empty( $alm_uid ) ) {
  return;
}

$terms = get_terms( [
  'taxonomy'   => $taxonomy,
  'hide_empty' => true,
  'child_of'   => @get_term_by( 'slug', $parent_term_slug, $taxonomy )->term_id,
] );

$uid_tax      = "mit-filters-form__{$taxonomy}__{$alm_uid}";
$uid_form     = "mit-filters-form__form__{$alm_uid}";
$uid_search   = "mit-filters-form__search__{$alm_uid}";
$uid_collapse = "mit-filters-form__search-collapse__{$alm_uid}";

if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
  <form action="#" class="mit-filters-form" id="<?= $uid_form ?>">
    <div class="mit-filters-form__group">
      <div class="mit-filters-form__title">
        <button
          type="button"
          class="mit-filters-form__toggle"
          data-bs-toggle="collapse"
          data-bs-target="#<?= $uid_collapse ?>"
          aria-expanded="false"
          aria-controls="<?= $uid_collapse ?>"
          tabindex="0"
        >
          Search title
        </button>
      </div>

      <div class="mit-filters-form__inner">
        <div class="collapse collapse-horizontal" id="<?= $uid_collapse ?>">
          <input class="mit-filters-form__textfield textfield" type="text" placeholder="Search..." id="<?= $uid_search ?>">
          <button type="submit">Submit</button>
        </div>
      </div>
    </div>

    <div class="mit-filters-form__group">
      <div class="mit-filters-form__title"><h3>Filter</h3></div>

      <div class="mit-filters-form__inner">
        <select class="mit-filters-form__select" id="<?= $uid_tax ?>">
          <option value="<?= esc_attr( $parent_term_slug ) ?>">All Product Categories</option>

          <?php foreach ( $terms as $term ) : ?>
            <option value="<?= esc_attr( $term->slug ) ?>"><?= $term->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </form>

  <script>
    /* global ajaxloadmore */
    jQuery(document).ready(function($) {
      $('<?="#{$uid_form}"?>').on('submit', function(e) {
        e.preventDefault();

        if ('ajaxloadmore' in window) {
          const transition = 'fade';
          const speed = 250;
          const terms = $('<?="#{$uid_tax}"?>').val();
          const search = $('<?="#{$uid_search}"?>').val();

          // Call core Ajax Load More `filter` function.
          // @see https://connekthq.com/plugins/ajax-load-more/docs/public-functions/#filter
          ajaxloadmore.filter(transition, speed, {
            target: '<?= $alm_uid ?>',
            pause: 'false',
            'taxonomy-terms': terms,
            search: search,
          });
        }
      });

      $('<?="#{$uid_tax}"?>').on('change', function() {
        $('<?="#{$uid_form}"?>').submit();
      });
    });
  </script>
<?php endif;
