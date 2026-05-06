<?php
/**
 * Meadan / EETA — Seed Script
 *
 * Creates the WordPress page at /essential-energy/ with the EETA template
 * and populates all ACF flexible content and options fields from Figma content.
 *
 * Run via WP-CLI from the WordPress root:
 *   wp eval-file wp-content/themes/meadan/inc/eeta-seed.php
 *
 * Or via the trigger URL (dev only):
 *   https://wordieproddev.wpenginepowered.com/?eeta_seed=run
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'update_field' ) ) {
    wp_die( 'ACF PRO is required. Activate the plugin and re-run.' );
}

// ---------------------------------------------------------------------------
// 1. Create (or find) the /essential-energy/ page
// ---------------------------------------------------------------------------
$page_slug = 'essential-energy';
$page      = get_page_by_path( $page_slug );

if ( ! $page ) {
    $page_id = wp_insert_post( [
        'post_title'   => 'Essential Energy Training Academy',
        'post_name'    => $page_slug,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
        'meta_input'   => [
            '_wp_page_template' => 'templates/template-essential-energy.php',
        ],
    ] );

    if ( is_wp_error( $page_id ) ) {
        wp_die( 'Failed to create page: ' . $page_id->get_error_message() );
    }

    echo "Created page ID {$page_id} at /{$page_slug}/\n";
} else {
    $page_id = $page->ID;
    // Make sure the template is set
    update_post_meta( $page_id, '_wp_page_template', 'templates/template-essential-energy.php' );
    echo "Found existing page ID {$page_id} at /{$page_slug}/\n";
}

// ---------------------------------------------------------------------------
// 2. Helper — import an image from the theme's assets into the media library
// ---------------------------------------------------------------------------
function eeta_import_image( string $relative_path, int $parent_id = 0 ): int {
    $theme_dir = get_template_directory();
    $file      = $theme_dir . '/assets/images/eeta/' . $relative_path;

    if ( ! file_exists( $file ) ) {
        echo "  [WARN] Image not found: $file\n";
        return 0;
    }

    // Check if already imported (avoid duplicates on re-runs)
    $existing = get_posts( [
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'meta_key'       => '_eeta_source_file',
        'meta_value'     => $relative_path,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ] );

    if ( ! empty( $existing ) ) {
        echo "  [SKIP] Already imported: $relative_path (ID {$existing[0]})\n";
        return $existing[0];
    }

    $upload_dir = wp_upload_dir();
    $filename   = basename( $file );
    $dest       = $upload_dir['path'] . '/' . $filename;

    // Ensure unique filename
    $dest = wp_unique_filename( $upload_dir['path'], $filename );
    $dest = $upload_dir['path'] . '/' . $dest;

    copy( $file, $dest );

    $file_type = wp_check_filetype( basename( $dest ), null );
    $attach_id = wp_insert_attachment( [
        'guid'           => $upload_dir['url'] . '/' . basename( $dest ),
        'post_mime_type' => $file_type['type'],
        'post_title'     => pathinfo( $dest, PATHINFO_FILENAME ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $dest, $parent_id );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata( $attach_id, $dest );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    // Mark it so we can find it next time
    update_post_meta( $attach_id, '_eeta_source_file', $relative_path );

    echo "  [OK]   Imported: $relative_path → ID $attach_id\n";
    return $attach_id;
}

// ---------------------------------------------------------------------------
// 3. Import all images
// ---------------------------------------------------------------------------
echo "\nImporting images...\n";
$img = [
    'hero'    => eeta_import_image( 'hero.png',            $page_id ),
    'tib'     => eeta_import_image( 'text-image.png',      $page_id ),
    'card1'   => eeta_import_image( 'audience-card-1.png', $page_id ),
    'card2'   => eeta_import_image( 'audience-card-2.png', $page_id ),
    'card3'   => eeta_import_image( 'audience-card-3.png', $page_id ),
    'ack'     => eeta_import_image( 'acknowledgement-banner.png', $page_id ),
    'ico1'    => eeta_import_image( 'icons/star.svg',           $page_id ),
    'ico2'    => eeta_import_image( 'icons/editor-choice.svg',  $page_id ),
    'ico3'    => eeta_import_image( 'icons/location-on.svg',    $page_id ),
    'ico4'    => eeta_import_image( 'icons/strategy.svg',       $page_id ),
    'ico5'    => eeta_import_image( 'icons/verified-user.svg',  $page_id ),
    'ico6'    => eeta_import_image( 'icons/partner-exchange.svg', $page_id ),
];

// ---------------------------------------------------------------------------
// 4. Build flexible content rows
// ---------------------------------------------------------------------------
echo "\nPopulating ACF flexible content...\n";

$sections = [

    // ── Hero ──────────────────────────────────────────────────────────────
    [
        'acf_fc_layout' => 'hero',
        'heading'       => 'Powering the workforce of the future',
        'subheading'    => "Australia needs 42,000 more energy trades workers by 2030. The skills shortage is here, in the communities at the heart of the energy transition. The Essential Energy Training Academy offers nationally recognised, industry led training to support meaningful, future ready careers in the energy sector.",
        'body'          => 'Enquire now to receive updates on courses and partnership opportunities.',
        'cta_label'     => 'Enquire now',
        'cta_url'       => '#enquire',
        'hero_image'    => $img['hero'],
    ],

    // ── Text + Image ──────────────────────────────────────────────────────
    [
        'acf_fc_layout' => 'text_image_block',
        'image'         => $img['tib'],
        'caption'       => '*This is a conceptual representation and may not reflect the final design.',
        'heading'       => 'Award-winning, practical training',
        'body'          => '<p>The Training Academy leverages Essential Energy\'s award winning Registered Training Organisation (RTO) and existing training footprint, the Academy offers hands on, practical learning supported by experienced trainers and modern facilities.</p><p>We offer multiple training locations across regional NSW, with a flagship training facility planned for Tamworth. The Academy creates a long-term legacy for regional Australia, addressing the critical skills needed for the energy sector, while strengthening local communities.</p>',
    ],

    // ── Audience Cards ────────────────────────────────────────────────────
    [
        'acf_fc_layout'    => 'audience_cards',
        'section_heading'  => 'Be a part of the energy transition',
        'section_subheading' => 'Ready to learn, upskill or collaborate? Find out what the Training Academy offers:',
        'cards'            => [
            [
                'icon'          => $img['card1'],
                'title'         => 'Individuals and employees',
                'bullet_points' => [
                    [ 'point' => 'People seeking nationally recognised qualifications in the energy sector' ],
                    [ 'point' => 'Apprentices and trainees starting their careers' ],
                    [ 'point' => 'Workers looking to upskill, reskill or transition into energy related roles' ],
                ],
            ],
            [
                'icon'          => $img['card2'],
                'title'         => 'Employers and industry partners',
                'bullet_points' => [
                    [ 'point' => 'Energy sector employers and contractors' ],
                    [ 'point' => 'Organisations seeking high quality workforce training' ],
                    [ 'point' => 'Industry partners interested in collaboration, sponsorship or placements' ],
                ],
            ],
            [
                'icon'          => $img['card3'],
                'title'         => 'Communities and stakeholders',
                'bullet_points' => [
                    [ 'point' => 'Regional communities building local capability and employment' ],
                    [ 'point' => 'Schools and education partners supporting pathways into energy careers' ],
                    [ 'point' => 'Community organisations aligned to workforce development' ],
                ],
            ],
        ],
    ],

    // ── Icon Boxes ────────────────────────────────────────────────────────
    [
        'acf_fc_layout'    => 'icon_boxes',
        'section_heading'  => "Why train with the\nEssential Energy Training Academy",
        'section_subheading' => '',
        'boxes'            => [
            [
                'icon'        => $img['ico1'],
                'title'       => '',
                'description' => 'Industry led training informed by real operational experience',
            ],
            [
                'icon'        => $img['ico2'],
                'title'       => '',
                'description' => 'Hands on learning delivered by expert trainers',
            ],
            [
                'icon'        => $img['ico3'],
                'title'       => '',
                'description' => 'Regionally embedded delivery, supporting local communities',
            ],
            [
                'icon'        => $img['ico4'],
                'title'       => '',
                'description' => 'Training designed to build job-ready skills aligned to real industry roles',
            ],
            [
                'icon'        => $img['ico5'],
                'title'       => '',
                'description' => 'A strong focus on safety, reliability and future energy skills',
            ],
            [
                'icon'        => $img['ico6'],
                'title'       => '',
                'description' => 'Commitment to inclusive, accessible and high quality education',
            ],
        ],
    ],

    // ── Upcoming Courses ─────────────────────────────────────────────────
    [
        'acf_fc_layout' => 'upcoming_courses',
        'heading'       => "What's coming next",
        'subheading'    => 'The Essential Energy Training Academy is being delivered in stages, with training already operating across our regional footprint and further expansion underway.',
        'courses'       => [
            [
                'status'      => 'ongoing',
                'year_label'  => 'Ongoing',
                'title'       => 'Ongoing rollout',
                'description' => 'Course information, enrolment pathways and partnership opportunities will continue to be released progressively as delivery locations and programs are confirmed.',
            ],
            [
                'status'      => 'year',
                'year_label'  => 'Mid-2026',
                'title'       => 'Zone Substation',
                'description' => 'From mid 2026, training is planned to commence at our Orange campus, with initial delivery focused on Zone Substation skills informed by real operational requirements.',
            ],
            [
                'status'      => 'late_2027',
                'year_label'  => 'Late 2027',
                'title'       => 'Tamworth Training Academy (planned)',
                'description' => "A purpose built Training Academy facility in Tamworth is planned as a future flagship site. Subject to required approvals, construction is expected to commence in 2027, with the facility anticipated to support expanded course delivery from late 2027 as the Academy continues to grow.\n\nThis site will enable broader, purpose designed training environments to support future energy skills, safety and workforce capability.",
            ],
        ],
    ],

    // ── Enquire ───────────────────────────────────────────────────────────
    [
        'acf_fc_layout'  => 'enquire_section',
        'heading'        => 'Enquire now',
        'subheading'     => 'By enquiring, you will be among the first to hear about:',
        'intro_text'     => "If you would like to learn more about the Essential Energy Training Academy, submit an enquiry and our team will be in touch.\n\nYou can also register your interest to stay informed as courses, partnerships and delivery locations are confirmed.",
        'benefits'       => [
            [ 'benefit_text' => 'Upcoming courses and training opportunities' ],
            [ 'benefit_text' => 'Course details, enrolment pathways and delivery locations' ],
            [ 'benefit_text' => 'Progress updates on the Training Academy' ],
            [ 'benefit_text' => 'Industry partnerships, sponsorships and community programs' ],
        ],
        'disclaimer_text' => '* By submitting this form, you agree to receive communications from the Essential Energy Training Academy.',
        'form_id'        => 1,
        'bg_image'       => 0,
    ],
];

$result = update_field( 'eeta_page_sections', $sections, $page_id );
echo $result ? "  [OK]   eeta_page_sections saved.\n" : "  [FAIL] eeta_page_sections — check ACF field name & page ID.\n";

// ---------------------------------------------------------------------------
// 5. Populate EETA Header options
// ---------------------------------------------------------------------------
echo "\nPopulating EETA Header options...\n";

update_field( 'eeta_header_logo', 0, 'option' ); // upload logo via WP Admin

update_field( 'eeta_header_nav_links', [
    [ 'label' => 'Nationally Accredited', 'url' => '#' ],
    [ 'label' => 'Training and Induction', 'url' => '#' ],
    [ 'label' => 'Contractor Programs', 'url' => '#' ],
], 'option' );

update_field( 'eeta_header_cta_label', 'Enquire now', 'option' );
update_field( 'eeta_header_cta_url', '#enquire', 'option' );

// ---------------------------------------------------------------------------
// 6. Populate EETA Footer options
// ---------------------------------------------------------------------------
echo "\nPopulating EETA Footer options...\n";

update_field( 'eeta_footer_logo', 0, 'option' );
update_field( 'eeta_footer_email', 'trainingacademy@essentialenergy.com.au', 'option' );
update_field( 'eeta_footer_phone', '(02) 6588 4570', 'option' );
update_field( 'eeta_footer_address', "Essential Energy\nPO Box 5730\nPort Macquarie NSW 2444", 'option' );
update_field( 'eeta_footer_acknowledgement_image', $img['ack'], 'option' );
update_field( 'eeta_footer_acknowledgement_text', 'Essential Energy acknowledges the Traditional Custodians of the lands on which our company is located and where we live and work. We pay our respects to ancestors and Elders, past, present and emerging.', 'option' );
update_field( 'eeta_footer_copyright', '© Essential Energy 2026 ABN 37 428 185 226', 'option' );
update_field( 'eeta_footer_links', [
    [ 'label' => 'Privacy Policy', 'url' => '#' ],
], 'option' );

echo "\nDone. Visit https://wordieproddev.wpenginepowered.com/essential-energy/ to view the page.\n";
echo "NOTE: Upload the EETA logo via WP Admin → EETA Settings → Header.\n";
