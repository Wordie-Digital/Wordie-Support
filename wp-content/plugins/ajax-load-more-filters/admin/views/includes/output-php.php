<div id="alm-filter-pop-up">
	<div class="inner-wrap">
		<h3><?php _e('Generated PHP', 'ajax-load-more-filters'); ?></h3>
		<p><?php _e('The following PHP has been generated from your filter selections and can be added directly to a WordPress template in place of the filter shortcode', 'ajax-load-more-filters'); ?>.</p>
		<div class="alm-filter-output output">
			<pre class="output"></pre>    
		</div>	
		<p><?php _e('Don\'t forget to update the <strong>{your_alm_id}</strong> value with your Ajax Load More ID', 'ajax-load-more-filters'); ?>.</p>
		<button class="button" v-on:click="closeModal"><?php _e('Close Window', 'ajax-load-more-filters'); ?></button>
	</div>
</div>