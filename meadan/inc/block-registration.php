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
        [
            'name'          => 'hero-video',
            'title'         => __( 'Hero Video', 'meadan' ),
            'description'   => __( 'Full-viewport video hero with overlay, heading and dual CTAs.', 'meadan' ),
            'icon'          => 'video-alt3',
            'keywords'      => [ 'hero', 'video', 'banner', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/hero-video.css',
        ],
        [
            'name'          => 'intro-about',
            'title'         => __( 'Intro / About', 'meadan' ),
            'description'   => __( 'Centred section with label, heading, body and dual CTAs.', 'meadan' ),
            'icon'          => 'align-center',
            'keywords'      => [ 'intro', 'about', 'centred', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/intro-about.css',
        ],
        [
            'name'          => 'designs-section',
            'title'         => __( 'Designs Section', 'meadan' ),
            'description'   => __( 'Dynamic home designs feed from Design CPT.', 'meadan' ),
            'icon'          => 'layout',
            'keywords'      => [ 'designs', 'floor plans', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/designs-section.css',
        ],
        [
            'name'          => 'projects-section',
            'title'         => __( 'Projects Section', 'meadan' ),
            'description'   => __( 'Dynamic project feed from Project CPT with slider navigation.', 'meadan' ),
            'icon'          => 'portfolio',
            'keywords'      => [ 'projects', 'portfolio', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/projects-section.css',
        ],
        [
            'name'          => 'services-section',
            'title'         => __( 'Services Section', 'meadan' ),
            'description'   => __( 'Grid of service cards with image, title, description and CTAs.', 'meadan' ),
            'icon'          => 'grid-view',
            'keywords'      => [ 'services', 'cards', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/services-section.css',
        ],
        [
            'name'          => 'news-section',
            'title'         => __( 'News Section', 'meadan' ),
            'description'   => __( 'Dynamic latest posts feed with section header and navigation.', 'meadan' ),
            'icon'          => 'welcome-write-blog',
            'keywords'      => [ 'news', 'blog', 'posts', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/news-section.css',
        ],
        [
            'name'          => 'image-carousel',
            'title'         => __( 'Image Carousel', 'meadan' ),
            'description'   => __( 'Full-width image slider with dot pagination.', 'meadan' ),
            'icon'          => 'images-alt2',
            'keywords'      => [ 'carousel', 'slider', 'images', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/image-carousel.css',
        ],
        [
            'name'          => 'contact-section',
            'title'         => __( 'Contact Section', 'meadan' ),
            'description'   => __( 'Section label, heading, description and contact form shortcode.', 'meadan' ),
            'icon'          => 'email',
            'keywords'      => [ 'contact', 'form', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/contact-section.css',
        ],
        [
            'name'          => 'display-home',
            'title'         => __( 'Display Home', 'meadan' ),
            'description'   => __( 'Two-column section promoting the display home with image and CTA.', 'meadan' ),
            'icon'          => 'admin-home',
            'keywords'      => [ 'display', 'home', 'meadan' ],
            'enqueue_style' => MEADAN_URI . '/assets/css/blocks/display-home.css',
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
        // Meadan ACF blocks — full inventory
        'acf/hero-banner',
        'acf/hero-video',
        'acf/feature-cards',
        'acf/about-section',
        'acf/intro-about',
        'acf/designs-section',
        'acf/projects-section',
        'acf/services-section',
        'acf/news-section',
        'acf/testimonial-slider',
        'acf/cta-section',
        'acf/gallery-section',
        'acf/image-carousel',
        'acf/contact-section',
        'acf/display-home',
        // Core paragraph as a fallback / convenience
        'core/paragraph',
    ];

}, 10, 2 );
