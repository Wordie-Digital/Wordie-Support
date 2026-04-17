<?php
/**
 * Meadan Homes — inc/block-registration.php
 *
 * Registers all 6 ACF Gutenberg blocks, the "meadan" block category,
 * and restricts the editor to only meadan blocks plus core/paragraph.
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Register custom block category
// ---------------------------------------------------------------------------
add_filter( 'block_categories_all', function ( $categories ) {
    array_unshift( $categories, [
        'slug'  => 'meadan',
        'title' => 'Meadan',
        'icon'  => null,
    ] );
    return $categories;
} );

// ---------------------------------------------------------------------------
// Register ACF blocks
// ---------------------------------------------------------------------------
add_action( 'acf/init', function () {

    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $blocks = [
        [
            'name'          => 'hero-banner',
            'title'         => __( 'Hero Banner', 'meadan' ),
            'description'   => __( 'Full-viewport hero with background image, heading, subheading and CTA button.', 'meadan' ),
            'icon'          => 'video-alt3',
            'keywords'      => [ 'hero', 'banner', 'header', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/hero-banner.css',
        ],
        [
            'name'          => 'feature-cards',
            'title'         => __( 'Feature Cards', 'meadan' ),
            'description'   => __( 'Responsive grid of feature cards, each with image, title, description and optional link.', 'meadan' ),
            'icon'          => 'grid-view',
            'keywords'      => [ 'cards', 'features', 'grid', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/feature-cards.css',
        ],
        [
            'name'          => 'about-section',
            'title'         => __( 'About Section', 'meadan' ),
            'description'   => __( 'Two-column layout with heading, body text and a supporting image.', 'meadan' ),
            'icon'          => 'format-image',
            'keywords'      => [ 'about', 'text', 'image', 'two-column', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/about-section.css',
        ],
        [
            'name'          => 'testimonial-slider',
            'title'         => __( 'Testimonial Slider', 'meadan' ),
            'description'   => __( 'Auto-advancing slider of customer testimonials with author photo.', 'meadan' ),
            'icon'          => 'format-quote',
            'keywords'      => [ 'testimonial', 'slider', 'quote', 'review', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/testimonial-slider.css',
        ],
        [
            'name'          => 'cta-section',
            'title'         => __( 'CTA Section', 'meadan' ),
            'description'   => __( 'Centred call-to-action section with heading, subheading and button.', 'meadan' ),
            'icon'          => 'megaphone',
            'keywords'      => [ 'cta', 'call to action', 'button', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/cta-section.css',
        ],
        [
            'name'          => 'gallery-section',
            'title'         => __( 'Gallery', 'meadan' ),
            'description'   => __( 'Responsive image gallery grid with optional captions shown on hover.', 'meadan' ),
            'icon'          => 'images-alt2',
            'keywords'      => [ 'gallery', 'images', 'grid', 'photos', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/gallery-section.css',
        ],
    ];

    foreach ( $blocks as $block ) {
        acf_register_block_type( [
            'name'             => $block['name'],
            'title'            => $block['title'],
            'description'      => $block['description'],
            'render_template'  => MEADAN_DIR . '/blocks/' . $block['name'] . '/template.php',
            'category'         => 'meadan',
            'icon'             => $block['icon'],
            'keywords'         => $block['keywords'],
            'enqueue_style'    => $block['enqueue_style'],
            'supports'         => [
                'align'    => false,
                'mode'     => true,
                'jsx'      => false,
                'anchor'   => true,
                'multiple' => true,
            ],
            'mode'             => 'edit',
        ] );
    }

} );

// ---------------------------------------------------------------------------
// Restrict Gutenberg to meadan blocks + core/paragraph fallback
// ---------------------------------------------------------------------------
add_filter( 'allowed_block_types_all', function ( $allowed_blocks, $editor_context ) {

    // Only restrict on post types that use the block editor for pages / content
    if ( empty( $editor_context->post ) ) {
        return $allowed_blocks;
    }

    return [
        // Meadan ACF blocks
        'acf/hero-banner',
        'acf/feature-cards',
        'acf/about-section',
        'acf/testimonial-slider',
        'acf/cta-section',
        'acf/gallery-section',
        // Core paragraph as a fallback / convenience
        'core/paragraph',
    ];

}, 10, 2 );
