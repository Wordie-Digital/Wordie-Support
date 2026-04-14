<div class="ajax-load-more-inner-wrapper">

   <!-- MAIN COLUMN -->
	<div class="cnkt-main stylefree">
		<div class="alm-filters">
			<?php include(ALM_FILTERS_PATH .'admin/views/includes/navigation.php'); ?>
			<div class="repeater-listing">

				<?php $the_filters = ALMFilters::alm_get_all_filters(); ?>
				<?php if($the_filters) : ?>
				<section class="alm-filter-tools-wrap" id="export">
					<header class="alm-filter--intro full">
						<h2><?php _e('Export Filter Groups', 'ajax-load-more-filters'); ?></h2>
		   			<p><?php _e('Select the filter groups you would like to export. Use the export button to export to a .json file which you can then import to another ALM Filters instance', 'ajax-load-more-filters'); ?>.</p>
					</header>

					<form method="post" class="alm-filter--tools">
   					<?php
   					echo '<ul class="alm-import-wrap">';
	      				echo '<span>'. __('Select Filter Groups for Export', 'ajax-load-more-filters') .'</span>';
	      				if(count($the_filters) > 1){
	      					echo '<li><label><input type="checkbox" id="toggle-all-filters" name="filter_keys_master" value="">'. __('Toggle All', 'ajax-load-more-filters') .'</label></li>';
	      				}
	      				echo '<div class="export-columns">';
	      				foreach($the_filters as $filter){ ?>
	         				<li>
	            				<label>
	            					<input type="checkbox" name="filter_keys[]" id="<?php echo $filter; ?>" value="<?php echo $filter; ?>">
	            					<?php echo ALMFilters::alm_filters_replace_string($filter); ?>
	            				</label>
								</li>
	         			<?php
	      				}
	      				echo '</div>';
      				echo '</ul>';
      				?>
      				<div class="button-wrap">
						   <button class="button button-primary" id="export-filters" name="button"><?php _e('Export', 'ajax-load-more-filters'); ?></button>
						   <input type="hidden" name="alm_filters_export" value="true">
	   				</div>
	   			</form>
				</section>
   			<?php endif; ?>


				<section <?php if($the_filters){ echo 'style="padding-top: 30px;"'; } ?> id="import">
					<header class="alm-filter--intro full">
						<h2><?php _e('Import Filter Groups', 'ajax-load-more-filters'); ?></h2>
		   			<p><?php _e('Select the Ajax Load More JSON file you would like to import. When you click the import button below, ALM will import the filter groups', 'ajax-load-more-filters'); ?>.</p>
					</header>

					<form method="post" class="alm-filter--tools" enctype="multipart/form-data">
	   				<div class="alm-import-wrap">
		   				<label for="alm_import_file" class="import"><?php _e('Select File', 'ajax-load-more-filters'); ?></label>
		   				<input name="alm_import_file" id="alm_import_file" type="file" >
	   				</div>
	   				<div class="button-wrap">
						   <button class="button button-primary" id="import-filters" name="button"><?php _e('Import', 'ajax-load-more-filters'); ?></button>
						   <input type="hidden" name="alm_filters_import" value="true">
	   				</div>
					</form>
				</section>

			</div>
		</div>
	</div>
	<!-- END MAIN COLUMN -->

	<aside class="cnkt-sidebar">
	   <div id="cnkt-sticky-wrapper">
		   <div id="cnkt-sticky">
	         <?php include(ALM_FILTERS_PATH .'admin/views/cta/help.php'); ?>
		   </div>
	   </div>
	</aside>

   <div class="clear"></div>
</div>
