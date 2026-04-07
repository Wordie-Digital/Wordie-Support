<div class="alm-filter--row alm-filter--row_instructions" id="row-instructions">

   <?php
      $how_to_intro = __('How to:', 'ajax-load-more-filters' );
      $how_to_generic = __('Use <strong>Custom Values</strong> or the <a href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#alm_filters_id_key" target="_blank">alm_filter_id_key</a> filter to build the filters for this parameter.', 'ajax-load-more-filters' );
      $how_to_override = __('You can override the default listing by using <strong>Custom Values</strong> or the <a href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#alm_filters_id_key" target="_blank">alm_filter_id_key</a> filter.', 'ajax-load-more-filters' );
   ?>


	<!-- Author -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'author'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>author</span> query parameter will filter posts by Author.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Author filters are auto-generated and displayed in alphabetical order based on the selected author role.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
		   </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Meta Query -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'meta'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>meta_query</span> query parameter will filter posts by custom field value.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Custom Field filters are not auto-generated and must be created manually.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Post Type -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'post_type'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>post_type</span> query parameter will filter posts by Post Type.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Post Type filters are not auto-generated.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Search -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'search'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>search</span> query parameter will filter posts by search term.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Use the textfield field type to render an input for searching. Entering a `Button Label` will render a separate submit button for submitting the field value.', 'ajax-load-more-filters' ); ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Taxonomy -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'taxonomy'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>taxonomy</span> query parameter will filter posts by taxonomy term slug.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Taxonomy term filters are auto-generated and displayed in alphabetical order.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Category -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'category'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>category</span> query parameter will filter posts (by slug) that are tagged with <u>any</u> of the chosen filters.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Category filters are auto-generated and displayed in alphabetical order based on the categories active on your website.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Category AND -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'category__and'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>category__and</span> query parameter will filter posts (by ID) that have been tagged with <u>all</u> of the chosen filters.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Category filters are auto-generated and displayed in alphabetical order based on the categories active on your website.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Tag -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'tag'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>tag</span> query parameter will filter posts (by slug) that are tagged with <u>any</u> of the chosen filters.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Tag filters are auto-generated and displayed in alphabetical order based on the tags active on your website.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Tag AND -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'tag__and'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>tag__and</span> query parameter will filter posts (by ID) that have been tagged with <u>all</u> of the chosen filters.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php _e( 'Tag filters are auto-generated and displayed in alphabetical order based on the tags active on your website.', 'ajax-load-more-filters' ); ?> <?php echo $how_to_override; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Order -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'order'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>order</span> query parameter designates the ascending or descending order of the `orderby` parameter. The value of this filter can only be `ASC` or `DESC` as shown in the <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">Docs</a>.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Orderby -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'orderby'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>orderby</span> query parameter will order posts by the selected value. All `orderby` parameters found in the <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">Docs</a> can be used as the value for this.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Sort -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'sort'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p>
				<?php _e( 'The <span>sort</span> query parameter requires both an <strong>Order</strong> and <strong>Orderby</strong> value to be present - sort values must be colon separated.', 'ajax-load-more-filters' ); ?>
				<br/>e.g. <em>order:orderby</em>
			</p>
			<p>
				<strong><?php _e( 'Custom Fields', 'ajax-load-more-filters' ); ?></strong><br/>
				<?php _e( 'When sorting by custom field values, the sort orderby value must be the custom field key.', 'ajax-load-more-filters' ); ?>
				<br/>e.g. <em>DESC:my_field</em>
			</p>
			<p>
				<?php _e( 'An optional 3rd parameter can also be set for <strong>meta_value</strong> (default) or <strong>meta_value_num</strong>.', 'ajax-load-more-filters' ); ?>
				<br/>e.g. <em>ASC:my_field:meta_value_num</em>
			</p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Day -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'day'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>day</span> query parameter will filter content by a day of the month.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Month -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'month'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>month</span> query parameter will filter content by the month of the year.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>

	<!-- Year -->
	<div class="alm-instructions whatsthis" v-show="filter.key === 'year'">
		<a class="alm-instructions--toggle" href="javascript: void(0);" v-on:click="toggleInstructions($event)">
			<?php _e( 'What\'s This?', 'ajax-load-more-filters' ); ?>
		</a>
		<div class="alm-instructions--copy">
			<p><?php _e( 'The <span>year</span> query parameter will filter content by the year of the post.', 'ajax-load-more-filters' ); ?></p>
			<p class="how-to">
   			<strong><?php echo $how_to_intro; ?></strong><br/>
   			<?php echo $how_to_generic; ?>
         </p>
		   <?php include ALM_FILTERS_PATH .'admin/views/includes/hookname.php'; ?>
		</div>
	</div>




</div>
