<?php
/**
 * MU-Plugin: Override process-steps CSS with two-column layout.
 *
 * WP Engine CDN caches theme CSS files independently and does not always
 * serve updated versions after deployment. This mu-plugin injects the
 * corrected two-column grid layout as inline styles, which always take
 * precedence over the file-served stylesheet.
 *
 * Matches Figma node 3090:20837 (Services page "Why" / "Our Process" section).
 */
add_action( 'wp_enqueue_scripts', function () {

	if ( ! ( is_page() || is_front_page() ) ) {
		return;
	}

	$css = '
/* process-steps: two-column layout (Figma 3090:20837) — injected via mu-plugin to bypass CDN cache */
.block-process-steps__container {
	display: grid !important;
	flex-direction: unset !important;
	grid-template-columns: 415px 1fr !important;
	gap: 34px !important;
	align-items: start !important;
}
.block-process-steps__header {
	min-width: 295px;
	display: flex;
	flex-direction: column;
	gap: 12px;
	max-width: unset !important;
	width: unset !important;
}
.block-process-steps__steps {
	display: flex !important;
	flex-direction: column !important;
	gap: 40px !important;
	border-bottom: none !important;
	padding-bottom: 0 !important;
	max-width: 847px;
}
.block-process-steps__step {
	flex: unset !important;
	border-left: none !important;
	padding-left: 0 !important;
	min-height: unset !important;
	display: flex;
	flex-direction: column;
	gap: 10px;
}
.block-process-steps__step-number {
	font-size: 2.25rem !important;
}
@media (max-width: 1024px) {
	.block-process-steps__container {
		grid-template-columns: 1fr !important;
		gap: 48px !important;
		padding: 48px 32px !important;
	}
	.block-process-steps__steps {
		max-width: 100% !important;
	}
}
@media (max-width: 640px) {
	.block-process-steps__container {
		padding: 48px 20px !important;
		gap: 36px !important;
	}
}
';

	wp_add_inline_style( 'wordie-process-steps', $css );

}, 100 );
