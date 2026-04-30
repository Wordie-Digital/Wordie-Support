<?php
/**
 * Meadan Homes — inc/seed-blog-posts.php
 *
 * One-time seeder: creates 3 sample blog posts so the single-post
 * template and related-articles section render immediately.
 *
 * Also ensures the /blog/ archive page exists and is set as the
 * WordPress "Posts page" so breadcrumbs and archive links resolve.
 *
 * Runs once on init, then sets an option so it never runs again.
 * To re-run: delete 'meadan_blog_seeded' from wp_options.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

    if ( get_option( 'meadan_blog_seeded' ) === 'v2' ) {
        return;
    }

    // ── 1. Permalink structure ──────────────────────────────────────────
    // Ensure pretty permalinks are active (/%postname%/) so post URLs work.
    if ( get_option( 'permalink_structure' ) !== '/%postname%/' ) {
        update_option( 'permalink_structure', '/%postname%/' );
    }

    // ── 2. Blog archive page ────────────────────────────────────────────
    // Create a WordPress page at slug "blog" and set it as the Posts page.
    $blog_page_id = 0;
    $existing_blog = get_page_by_path( 'blog', OBJECT, 'page' );

    if ( $existing_blog ) {
        $blog_page_id = $existing_blog->ID;
    } else {
        $blog_page_id = wp_insert_post( [
            'post_title'   => 'Blog',
            'post_name'    => 'blog',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ] );
    }

    if ( $blog_page_id && ! is_wp_error( $blog_page_id ) ) {
        // Only set if not already configured
        if ( (int) get_option( 'page_for_posts' ) !== $blog_page_id ) {
            update_option( 'page_for_posts', $blog_page_id );
            // Static front page must be set for page_for_posts to work
            if ( ! get_option( 'page_on_front' ) ) {
                $front = get_page_by_path( 'home', OBJECT, 'page' );
                if ( $front ) {
                    update_option( 'show_on_front', 'page' );
                    update_option( 'page_on_front', $front->ID );
                }
            }
        }
    }

    // ── 3. News category ────────────────────────────────────────────────
    $cat_id = get_cat_ID( 'News' );
    if ( ! $cat_id ) {
        $cat = wp_insert_term( 'News', 'category' );
        $cat_id = is_wp_error( $cat ) ? 1 : $cat['term_id'];
    }

    // ── 4. Sample posts ─────────────────────────────────────────────────
    $posts = [
        [
            'title'   => 'Inside the Avignon: A Modern French Provincial Story',
            'slug'    => 'inside-the-avignon-a-modern-french-provincial-story',
            'excerpt' => 'Step inside Project Avignon and discover how traditional charm and modern living come together to create a home defined by light, space, and considered design.',
            'content' => '<h4>Designing for Light and Living</h4>
<p>A soaring void draws natural light into the heart of the home, while expansive living areas and carefully curated interiors create a sense of space, warmth, and refinement. Each room has been thoughtfully designed to balance timeless style with everyday functionality.</p>
<p>The open-plan living spaces embody a modern French provincial aesthetic, blending soft neutral palettes, gentle textures, and subtle wrought iron accents to create a timeless yet relaxed atmosphere enhanced by natural light and a sense of openness.</p>
<h4>The Kitchen and Dining Experience</h4>
<p>The open-plan kitchen and dining area flows seamlessly into an extended alfresco, showcasing modern French provincial style with a farmhouse sink, shaker cabinetry, and elegant wrought iron finishes throughout. Filled with natural light and enhanced by a soaring void nearby, the space transitions effortlessly outdoors, creating a relaxed yet refined setting ideal for entertaining and year-round living.</p>
<p>Bathrooms echo this elegance with classic detailing, incorporating refined finishes, stone surfaces, and traditional-inspired fixtures that bring a touch of understated luxury while maintaining a calm, contemporary feel.</p>',
        ],
        [
            'title'   => '5 Design Trends Shaping Custom Homes in 2026',
            'slug'    => '5-design-trends-shaping-custom-homes-in-2026',
            'excerpt' => 'From biophilic interiors to mixed-material facades, we explore the five design trends our clients are embracing most in new custom home builds this year.',
            'content' => '<h4>1. Biophilic Design Takes Centre Stage</h4>
<p>Bringing the outside in has moved from a nice-to-have to a core design principle. Clients are requesting larger glazed openings, indoor gardens, and natural material palettes that blur the boundary between home and landscape.</p>
<h4>2. Considered Material Selection</h4>
<p>Stone, timber, and raw plaster are replacing polished and painted surfaces. The emphasis is on materials that age beautifully and tell a story — surfaces that develop character over time rather than demanding constant upkeep.</p>
<h4>3. Multi-Generational Living</h4>
<p>With housing affordability pressures, more families are designing homes that work across generations — whether that means a self-contained studio above the garage or a secondary dwelling that allows ageing parents to remain close without sacrificing independence.</p>',
        ],
        [
            'title'   => 'What to Expect During the Design & Build Process',
            'slug'    => 'what-to-expect-during-the-design-build-process',
            'excerpt' => 'Building a custom home is one of life\'s most significant investments. Here\'s what the Meadan Homes design and build journey looks like from first conversation to handover.',
            'content' => '<h4>The Initial Consultation</h4>
<p>Every Meadan home begins with a conversation. We take time to understand how you and your family actually live — your routines, your aspirations, and the moments you want your home to enable. This human-first approach shapes every decision that follows.</p>
<h4>Concept Design</h4>
<p>Our design team translates your brief into a concept that balances your vision with site constraints, council requirements, and budget realities. You\'ll see your home come to life through floor plans, elevations, and 3D visualisations before a single sod is turned.</p>
<h4>Construction and Communication</h4>
<p>Our build teams operate with full transparency. You\'ll receive regular progress updates, have direct access to your site supervisor, and be invited to key milestones throughout the construction phase. Handover is a celebration — not a surprise.</p>',
        ],
    ];

    foreach ( $posts as $i => $data ) {
        // Skip if a post with this slug already exists
        $existing = get_posts( [
            'name'        => $data['slug'],
            'post_type'   => 'post',
            'post_status' => 'any',
            'numberposts' => 1,
        ] );

        if ( ! empty( $existing ) ) {
            continue;
        }

        wp_insert_post( [
            'post_title'    => $data['title'],
            'post_name'     => $data['slug'],
            'post_excerpt'  => $data['excerpt'],
            'post_content'  => $data['content'],
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_category' => [ $cat_id ],
            'menu_order'    => $i + 1,
        ] );
    }

    // ── 5. Flush rewrite rules so new post slugs resolve ────────────────
    flush_rewrite_rules();

    update_option( 'meadan_blog_seeded', 'v2' );
} );
