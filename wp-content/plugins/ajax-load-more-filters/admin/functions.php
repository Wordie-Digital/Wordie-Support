<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * This function will list all filters.
 *
 * @since 1.5
 * @param {String} $filter_id the ID.
 * @param {String} $pos the position.
 */
function alm_list_all_filters( $filter_id, $pos = 'sidebar' ) {

	$filters = ALMFilters::alm_get_all_filters();

	if ( $filters ) : ?>
		<div class="filter-listing--wrap">
			<?php if($pos !== 'sidebar'){ ?>
			<div class="filter-listing--row header">
				<div class="left">
					<?php _e('Filter', 'ajax-load-more-filters'); ?>
				</div>
				<div class="right">
					<?php _e('Date', 'ajax-load-more-filters'); ?>
				</div>
			</div>
			<?php } ?>
			<ul class="filter-listing--ul">
				<?php
				foreach ( $filters as $filter ) :	// Loop the filters.
					$filter      = preg_replace( '/' . esc_sql( ALM_FILTERS_PREFIX ) . '/', '', $filter, 1 );
					$filter_data = unserialize( get_option( ALM_FILTERS_PREFIX . $filter ) );
					$current     = ( $filter === $filter_id ) ? ' class="current"' : '';

					if ( $filter_data ) { ?>
	   				<li>
	   					<div class="filter-listing--row">

		   					<?php if ( $pos !== 'sidebar' ) { ?>
		   					<div class="left">
			   				<?php } ?>

			   					<div class="title-row">
				   					<strong>
				   					<?php
											echo '<a href="'. ALM_FILTERS_BASE_URL .'&filter='. $filter .'"'. $current .'>'. str_replace(ALM_FILTERS_PREFIX, '', $filter) .'</a>'; ?>
				   					</strong>
										<span class="counter" title="<?php echo count($filter_data['filters']); ?> <?php _e('filter block(s) in this filter', 'ajax-load-more-filters'); ?>"><?php echo count($filter_data['filters']); ?></span>
			   					</div>

			   					<div class="edit-row">
				   					<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&filter=<?php echo $filter_data['id']; ?>">
						   				<?php _e('Edit', 'ajax-load-more-filters'); ?>
						   			</a>
				   					<span class="sep">|</span>
				   					<?php if($pos === 'sidebar'){ ?>
				   					<a v-on:click="deleteFilter($event)" data-id="<?php echo str_replace(ALM_FILTERS_PREFIX, '', $filter); ?>" href="javascript:void(0);" title="<?php _e('Delete Filter', 'ajax-load-more-filters'); ?>">
					   					<span><?php _e('Delete', 'ajax-load-more-filters'); ?></span>
					   				</a>
					   				<?php } else { ?>
					   				<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&delete_filter=<?php echo $filter_data['id']; ?>" class="delete-filter" data-name="<?php echo $filter_data['id']; ?>"><?php _e('Delete', 'ajax-load-more-filters'); ?></a>
					   				<?php } ?>
			   					</div>

		   					<?php if($pos !== 'sidebar'){ ?>
		   					</div>
			   				<div class="right">
				   				<p class="date">
					   				<?php _e('Published', 'ajax-load-more-filters'); ?>:<br/>
					   				<?php if(isset($filter_data['date_created'])){
						   				echo '<abbr title="'. date('Y/m/d h:i:s a', $filter_data['date_created']) .'">'. date('Y/m/d',$filter_data['date_created']) .'</abbr>';
					   				} else {
						   				echo '--';
					   				}
					   				?>
				   				</p>
			   				</div>
			   				<?php } ?>

	   					</div>
	   				</li>
	   			<?php } endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<?php echo alm_empty_filters($pos); ?>
   <?php endif; ?>
<?php
}

/**
 * This function is called when filters do not exist
 *
 * @since 1.5
 * @param {String} $pos
 */
function alm_empty_filters( $pos ) {

	$response = '<div class="alm-no-filters '. $pos .'">';
		$response .= '<div class="alm-no-filters--inner">';
			$response .= '<p class="first-intro">';
				$response .= __('It appears you don\'t have any filters!', 'ajax-load-more-filters');
			$response .= '</p>';
			$response .= '<p>';
				$response .= __('The first step in filtering with Ajax Load More is to create one!', 'ajax-load-more-filters');
			$response .= '</p>';

			if($pos !== 'sidebar'){
			   $response .= '<p class="create-btn"><a href="'. ALM_FILTERS_BASE_URL .'&action=new" class="button button-primary button-large"> '. __('Create Filter','ajax-load-more-filters') .'</a></p>';
			}

		$response .= '</div>';
	$response .= '</div>';

	return $response;
}
