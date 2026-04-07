<div class="ajax-load-more-inner-wrapper">

   <!-- MAIN COLUMN -->
	<div class="cnkt-main stylefree">
		<div class="alm-filters">
			<?php include(ALM_FILTERS_PATH .'admin/views/includes/navigation.php'); ?>
			<div class="repeater-listing">
				<header class="alm-filter--intro">
	   			<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&action=new" class="button button-primary">Create New</a>
					<h2><?php _e('Your Filters', 'ajax-load-more-filters'); ?></h2>
	   			<p><?php _e('Ajax Load More filters are listed below in alphabetical order', 'ajax-load-more-filters'); ?>.</p>
				</header>
				<div class="filter-listing--main">
				   <?php echo alm_list_all_filters($filter_id, 'main'); ?>
				   <script>
					   var deleteLink = document.querySelectorAll('.filter-listing--wrap a.delete-filter');
						for (var i = 0; i < deleteLink.length; i++) {
							deleteLink[i].addEventListener('click', function(event) {
								var id = event.target.dataset.name;
								if (!confirm(alm_filters_localize.delete_filter + ' ' + id +'?')) {
									event.preventDefault();
								}
							});
						}
					</script>
				</div>
			</div>
		</div>
	</div>
	<!-- END MAIN COLUMN -->

	<aside class="cnkt-sidebar">
	   <div id="cnkt-sticky-wrapper">
		   <div id="cnkt-sticky">
	         <?php include(ALM_FILTERS_PATH .'admin/views/cta/whats-new.php'); ?>
	         <?php include(ALM_FILTERS_PATH .'admin/views/cta/help.php'); ?>
		   </div>
	   </div>
	</aside>

   <div class="clear"></div>
</div>
