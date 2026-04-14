<ul class="alm-toggle-switch">
   <li>
		<a href="<?php echo ALM_FILTERS_BASE_URL; ?>"<?php if ( $section === 'dashboard' ){ echo ' class="active"'; } ?>>
			<i class="fa fa-dashboard" aria-hidden="true"></i> <?php _e( 'Dashboard', 'ajax-load-more-filters' ); ?>
		</a>
	</li>
   <li>
		<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&action=new"<?php if ( $section === 'new' ){ echo ' class="active"'; } ?>>
		<i class="fa fa-pencil" aria-hidden="true"></i>
		<?php _e( 'Create', 'ajax-load-more-filters' ); ?>
		</a>
	</li>
   <li>
		<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&action=tools"<?php if ( $section === 'tools' ){ echo ' class="active"'; } ?>>
			<i class="fa fa-wrench" aria-hidden="true"></i> <?php _e( 'Tools', 'ajax-load-more-filters' ); ?>
		</a>
	</li>
</ul>
