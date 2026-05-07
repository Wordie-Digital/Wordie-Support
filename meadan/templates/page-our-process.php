<?php
/**
 * Template Name: Our Process
 *
 * Meadan — templates/page-our-process.php
 *
 * Used by both:
 *   /our-process-build-to-your-plans/  (slug: our-process-btyp)
 *   /our-process-design-and-build/     (slug: our-process-db)
 *
 * Figma nodes:
 *   4515:30751 — Our Process - BTYP
 *   6990:23723 — Our Process - DB
 *
 * Sections (per Figma):
 *   1. Hero Image        — full-width 471px, bg image with dark overlay + tagline
 *   2. Engagement        — sand bg, centred h2 + description + 3-col horizontal overview
 *                          with prev/next arrow scroll
 *   3. Timeline          — white bg, TIMELINE label + h2 + description,
 *                          then two-column layout (centre vertical line)
 *                          left col: steps 01, 03, 05
 *                          right col: steps 02, 04
 *                          each step: large number + horizontal rule + title + body + image
 *   4. Contact Section   — shared template part
 *   5. Footer            — via get_footer()
 *
 * ACF fields (all optional — hardcoded Figma defaults used as fallback):
 *   hero_image           image       Background for hero section
 *   hero_tagline         text        Tagline displayed on hero (shared across variants)
 *   overview_title       text        "Project Engagement Process"
 *   overview_description textarea    "At Meadan, we take a structured…"
 *   overview_steps       repeater
 *       → step_heading       text    "Consultation"
 *       → step_subheading    text    "Defining the Scope"
 *       → step_body          textarea
 *   timeline_label       text        "TIMELINE"
 *   timeline_title       text        "Build With Your Plans Project Timeline" OR "Design & Build Project Timeline"
 *   timeline_description textarea
 *   timeline_steps       repeater
 *       → step_number    text        "01"
 *       → step_title     text        "Plan Assessment & Design Handover"
 *       → step_body      textarea
 *       → step_image     image
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $has_acf = function_exists( 'get_field' );

    // ── Hero ──────────────────────────────────────────────────────────
    $hero_image   = $has_acf ? get_field( 'hero_image' )   : null;
    $hero_tagline = $has_acf ? get_field( 'hero_tagline' ) : '';
    $hero_tagline = $hero_tagline ?: __( 'A Rewarding Journey', 'meadan' );

    // ── Engagement overview ───────────────────────────────────────────
    $overview_title = $has_acf ? get_field( 'overview_title' ) : '';
    $overview_title = $overview_title ?: __( 'Project Engagement Process', 'meadan' );

    $overview_description = $has_acf ? get_field( 'overview_description' ) : '';
    $overview_description = $overview_description ?: __( 'At Meadan, we take a structured, transparent approach to every project, ensuring a seamless experience from your first enquiry to the completion of your build. Our Project Engagement Process has been refined over our 13 years of operation, to achieve a 99% satisfaction rate from our 420+ clients. Combining industry expertise, local knowledge, and a client-focused approach Meadan remains committed to delivering exceptional design and build projects across our key service areas of Sydney, Brisbane and Sunshine Coast.', 'meadan' );

    $overview_steps = $has_acf ? get_field( 'overview_steps' ) : [];

    // ── Timeline ──────────────────────────────────────────────────────
    $timeline_label = $has_acf ? get_field( 'timeline_label' ) : '';
    $timeline_label = $timeline_label ?: __( 'TIMELINE', 'meadan' );

    $timeline_title = $has_acf ? get_field( 'timeline_title' ) : '';
    $timeline_title = $timeline_title ?: get_the_title(); // page title = "Build to Your Plans" / "Design & Build"

    $timeline_description = $has_acf ? get_field( 'timeline_description' ) : '';
    $timeline_description = $timeline_description ?: __( 'At Meadan, we understand that clear planning and predictable milestones are key to a successful build. Our Project Timeline has been refined over our 287+ builds, providing even more transparency at every stage and giving you confidence that your project will be delivered efficiently, on schedule, and to the highest quality standards.', 'meadan' );

    $timeline_steps = $has_acf ? get_field( 'timeline_steps' ) : [];

    // Split timeline steps: odd indices → left col, even indices → right col
    $steps_left  = [];
    $steps_right = [];
    if ( ! empty( $timeline_steps ) ) {
        foreach ( $timeline_steps as $i => $step ) {
            if ( $i % 2 === 0 ) {
                $steps_left[]  = $step;  // 01, 03, 05…
            } else {
                $steps_right[] = $step;  // 02, 04…
            }
        }
    }
?>

<main class="site-main site-main--our-process" id="main" role="main">

    <!-- ── 1. Hero Image ─────────────────────────────────────────────── -->
    <section
        class="process-hero-image <?php echo $hero_image ? 'process-hero-image--has-bg' : ''; ?>"
        <?php if ( $hero_image && is_array( $hero_image ) ) : ?>
            style="background-image: url('<?php echo esc_url( $hero_image['url'] ); ?>')"
        <?php endif; ?>
        aria-label="<?php esc_attr_e( 'Our Process hero', 'meadan' ); ?>"
    >
        <div class="process-hero-image__overlay" aria-hidden="true"></div>
        <div class="process-hero-image__content">
            <h1 class="process-hero-image__tagline"><?php echo esc_html( $hero_tagline ); ?></h1>
        </div>
    </section><!-- .process-hero-image -->

    <!-- ── 2. Project Engagement Overview ────────────────────────────── -->
    <section class="process-overview" aria-label="<?php esc_attr_e( 'Project Engagement Process', 'meadan' ); ?>">
        <div class="process-overview__inner">

            <div class="process-overview__heading-wrap">
                <h2 class="process-overview__title"><?php echo esc_html( $overview_title ); ?></h2>
                <p class="process-overview__description"><?php echo esc_html( $overview_description ); ?></p>
            </div>

            <?php if ( ! empty( $overview_steps ) ) : ?>
                <div class="process-overview__slider-wrap">
                    <div class="process-overview__steps js-overview-slider" role="list">
                        <?php foreach ( $overview_steps as $step ) :
                            $heading    = ! empty( $step['step_heading'] )    ? $step['step_heading']    : '';
                            $subheading = ! empty( $step['step_subheading'] ) ? $step['step_subheading'] : '';
                            $body       = ! empty( $step['step_body'] )       ? $step['step_body']       : '';
                        ?>
                            <div class="process-overview__step" role="listitem">
                                <h3 class="process-overview__step-heading"><?php echo esc_html( $heading ); ?></h3>
                                <?php if ( $subheading ) : ?>
                                    <p class="process-overview__step-subheading"><?php echo esc_html( $subheading ); ?></p>
                                <?php endif; ?>
                                <?php if ( $body ) : ?>
                                    <p class="process-overview__step-body"><?php echo esc_html( $body ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Prev / next navigation -->
                    <nav class="process-overview__nav" aria-label="<?php esc_attr_e( 'Scroll overview steps', 'meadan' ); ?>">
                        <button class="arrow-btn arrow-btn--prev js-overview-prev" aria-label="<?php esc_attr_e( 'Previous step', 'meadan' ); ?>" disabled>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="arrow-btn arrow-btn--next js-overview-next" aria-label="<?php esc_attr_e( 'Next step', 'meadan' ); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </nav>
                </div><!-- .process-overview__slider-wrap -->

            <?php else : ?>
                <!-- Default overview steps (Figma content — editor can override via ACF) -->
                <div class="process-overview__slider-wrap">
                    <div class="process-overview__steps js-overview-slider" role="list">

                        <div class="process-overview__step" role="listitem">
                            <h3 class="process-overview__step-heading"><?php esc_html_e( 'Consultation', 'meadan' ); ?></h3>
                            <p class="process-overview__step-subheading"><?php esc_html_e( 'Defining the Scope', 'meadan' ); ?></p>
                            <p class="process-overview__step-body"><?php esc_html_e( 'Every Meadan project begins with a detailed consultation to understand your design and build vision and objectives. During this stage, Meadan assembles an integrated Project Team who works with you to define the project scope, discuss site conditions, and explore the opportunities and constraints that may influence your home design and in turn your home build. Our experience across Sydney, Brisbane and Sunshine Coast allows us to provide early insights into local planning requirements, site considerations, and construction approaches, ensuring the project starts with a clear and realistic foundation.', 'meadan' ); ?></p>
                        </div>

                        <?php
                        // Step 2 differs between BTYP and DB — detect by page slug
                        $slug = get_post_field( 'post_name', get_the_ID() );
                        $is_db = ( strpos( $slug, 'design-and-build' ) !== false || strpos( $slug, '-db' ) !== false );
                        if ( $is_db ) : ?>
                            <div class="process-overview__step" role="listitem">
                                <h3 class="process-overview__step-heading"><?php esc_html_e( 'Design Meeting', 'meadan' ); ?></h3>
                                <p class="process-overview__step-subheading"><?php esc_html_e( 'Share your Vision', 'meadan' ); ?></p>
                                <p class="process-overview__step-body"><?php esc_html_e( 'A Meadan design meeting brings together key members of your Integrated Project Team to commence development of a design concept. Working closely with architects, building and interior designers, as well as consultants, we explore layout, functionality, materials, and construction considerations to ensure the design aligns with both your goals and the practical requirements of the site. By integrating construction expertise early in the design process, we help ensure the design is efficient, buildable, and suited to local conditions.', 'meadan' ); ?></p>
                            </div>
                        <?php else : ?>
                            <div class="process-overview__step" role="listitem">
                                <h3 class="process-overview__step-heading"><?php esc_html_e( 'Plan Assessment &amp; Design Handover', 'meadan' ); ?></h3>
                                <p class="process-overview__step-subheading"><?php esc_html_e( 'Reviewing &amp; Refining your Vision', 'meadan' ); ?></p>
                                <p class="process-overview__step-body"><?php esc_html_e( 'A Meadan Plan Assessment & Design handover meeting brings together key members of your Integrated Project Team to commence review and potentially refinement of your design. We pick up where you left off, by collaborating with your architect or building designer, as well as any other consultants you may have partnered with. We review layout, functionality, materials, and construction considerations to ensure the design aligns with both your goals and the practical requirements of the site. By integrating construction expertise early in the design review, we help ensure the design is efficient, buildable, and suited to local conditions.', 'meadan' ); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="process-overview__step" role="listitem">
                            <h3 class="process-overview__step-heading"><?php esc_html_e( 'Quoting &amp; Feasibility', 'meadan' ); ?></h3>
                            <p class="process-overview__step-subheading"><?php esc_html_e( 'Detailed Estimates and Assessments', 'meadan' ); ?></p>
                            <p class="process-overview__step-body"><?php esc_html_e( 'Once the design direction is established, your Project Team prepares a detailed project estimate and assessment. This stage involves reviewing cost estimates, project timelines, material requirements and logistics to provide clear expectations around the construction timeline. Our Integrated Project Team evaluates design through a practical construction lens, identifying opportunities to maximize value while maintaining quality and ensuring your vision is achievable.', 'meadan' ); ?></p>
                        </div>

                    </div><!-- .process-overview__steps -->

                    <nav class="process-overview__nav" aria-label="<?php esc_attr_e( 'Scroll overview steps', 'meadan' ); ?>">
                        <button class="arrow-btn arrow-btn--prev js-overview-prev" aria-label="<?php esc_attr_e( 'Previous', 'meadan' ); ?>" disabled>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="arrow-btn arrow-btn--next js-overview-next" aria-label="<?php esc_attr_e( 'Next', 'meadan' ); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </nav>
                </div><!-- .process-overview__slider-wrap -->
            <?php endif; ?>

        </div><!-- .process-overview__inner -->
    </section><!-- .process-overview -->

    <!-- ── 3. Timeline: two-column with centre line ───────────────────── -->
    <section class="process-timeline" aria-label="<?php esc_attr_e( 'Project timeline', 'meadan' ); ?>">
        <div class="process-timeline__inner">

            <!-- Centred header -->
            <header class="process-timeline__header">
                <p class="process-timeline__label"><?php echo esc_html( $timeline_label ); ?></p>
                <h2 class="process-timeline__title"><?php echo esc_html( $timeline_title ); ?></h2>
                <p class="process-timeline__description"><?php echo esc_html( $timeline_description ); ?></p>
            </header>

            <!-- Two-column steps grid -->
            <?php if ( ! empty( $timeline_steps ) ) : ?>

                <div class="process-timeline__columns">
                    <div class="process-timeline__col process-timeline__col--left">
                        <?php foreach ( $steps_left as $step ) :
                            $num   = ! empty( $step['step_number'] ) ? $step['step_number'] : '';
                            $title = ! empty( $step['step_title'] )  ? $step['step_title']  : '';
                            $body  = ! empty( $step['step_body'] )   ? $step['step_body']   : '';
                            $img   = ! empty( $step['step_image'] )  ? $step['step_image']  : null;
                        ?>
                            <div class="process-step process-step--left">
                                <div class="process-step__numrow">
                                    <span class="process-step__number"><?php echo esc_html( $num ); ?></span>
                                    <hr class="process-step__rule" aria-hidden="true">
                                </div>
                                <div class="process-step__text">
                                    <h3 class="process-step__title"><?php echo esc_html( $title ); ?></h3>
                                    <p class="process-step__body"><?php echo esc_html( $body ); ?></p>
                                </div>
                                <?php if ( $img && is_array( $img ) ) : ?>
                                    <figure class="process-step__image-wrap">
                                        <img
                                            class="process-step__image"
                                            src="<?php echo esc_url( $img['url'] ); ?>"
                                            alt="<?php echo esc_attr( $img['alt'] ?: $title ); ?>"
                                            loading="lazy"
                                            width="<?php echo esc_attr( $img['width'] ); ?>"
                                            height="<?php echo esc_attr( $img['height'] ); ?>"
                                        >
                                    </figure>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div><!-- .process-timeline__col--left -->

                    <div class="process-timeline__col process-timeline__col--right">
                        <?php foreach ( $steps_right as $step ) :
                            $num   = ! empty( $step['step_number'] ) ? $step['step_number'] : '';
                            $title = ! empty( $step['step_title'] )  ? $step['step_title']  : '';
                            $body  = ! empty( $step['step_body'] )   ? $step['step_body']   : '';
                            $img   = ! empty( $step['step_image'] )  ? $step['step_image']  : null;
                        ?>
                            <div class="process-step process-step--right">
                                <div class="process-step__numrow process-step__numrow--right">
                                    <hr class="process-step__rule" aria-hidden="true">
                                    <span class="process-step__number"><?php echo esc_html( $num ); ?></span>
                                </div>
                                <div class="process-step__text">
                                    <h3 class="process-step__title"><?php echo esc_html( $title ); ?></h3>
                                    <p class="process-step__body"><?php echo esc_html( $body ); ?></p>
                                </div>
                                <?php if ( $img && is_array( $img ) ) : ?>
                                    <figure class="process-step__image-wrap">
                                        <img
                                            class="process-step__image"
                                            src="<?php echo esc_url( $img['url'] ); ?>"
                                            alt="<?php echo esc_attr( $img['alt'] ?: $title ); ?>"
                                            loading="lazy"
                                            width="<?php echo esc_attr( $img['width'] ); ?>"
                                            height="<?php echo esc_attr( $img['height'] ); ?>"
                                        >
                                    </figure>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div><!-- .process-timeline__col--right -->
                </div><!-- .process-timeline__columns -->

            <?php else : ?>
                <!-- Default timeline steps from Figma — editor overrides via ACF -->
                <?php
                $is_db = ( strpos( get_post_field( 'post_name', get_the_ID() ), 'design-and-build' ) !== false
                         || strpos( get_post_field( 'post_name', get_the_ID() ), '-db' ) !== false );

                $default_steps = [
                    [
                        'number' => '01',
                        'title'  => $is_db ? __( 'Design', 'meadan' ) : __( 'Plan Assessment &amp; Design Handover', 'meadan' ),
                        'body'   => $is_db
                            ? __( 'The Design stage involves your architect or building designer, as well as consultants, developing a design concept aligned to your brief, budget, site, and local council requirements. Meadan works closely with your design team throughout this stage to ensure the concept translates into a practical, buildable, and cost-effective outcome.', 'meadan' )
                            : __( 'The Plan Assessment and Design Handover stage is where we pick up from where you left off with your design professional and turn your concept into a practical, buildable project. Meadan works closely with you and your consultants to review your plans, refine layouts, and ensure every element is achievable on your site. With our extensive industry experience, we provide valuable input on construction methods, materials, and project logistics. Our team ensures the design aligns with local conditions, council requirements, and site-specifics across Sydney, Brisbane, and the Sunshine Coast. By carefully assessing and taking ownership of the design handover, we bridge the gap between concept and construction, providing a seamless transition that sets the stage for a smooth and efficient build.', 'meadan' ),
                    ],
                    [
                        'number' => '02',
                        'title'  => __( 'Pre-Construction &amp; Approvals', 'meadan' ),
                        'body'   => __( 'During the pre-construction phase, Meadan\'s experienced project team prepares the project for a smooth transition into construction. This includes coordinating documentation, engineering, council approvals, and consultant input to ensure everything is in place before work begins on site. Our industry expertise allows us to anticipate potential challenges early and implement practical solutions that support an efficient construction process. Through careful planning and coordination, we ensure the project is fully prepared and aligned with all regulatory requirements.', 'meadan' ),
                    ],
                    [
                        'number' => '03',
                        'title'  => __( 'Interiors &amp; Selections', 'meadan' ),
                        'body'   => __( 'The Interiors and Selections stage focuses on defining the finishes and design details that will shape the final look and feel of your project. Our Interior and Selections Team guide you to select materials, fixtures, fittings, and finishes that align with your vision while also performing well in the local environment. We conduct a series of selection appointments within our impressive Meadan showroom and other appointments will be held with our trusted suppliers. You will also get access to our Selections Portal at the time. By finalising selections early, we help ensure materials are ordered and scheduled correctly, supporting a smooth and well-organised construction process.', 'meadan' ),
                    ],
                    [
                        'number' => '04',
                        'title'  => __( 'Construction Phase', 'meadan' ),
                        'body'   => __( 'During construction, Meadan\'s experienced team manages every aspect of the build, ensuring the project is delivered safely, efficiently, and to the highest construction standards. Our project managers coordinate trades, suppliers, and consultants while maintaining clear communication with clients throughout the process. With extensive experience delivering projects across Sydney and South-East Queensland, we understand the importance of managing site conditions, weather considerations, and construction logistics to keep the project progressing as planned.', 'meadan' ),
                    ],
                    [
                        'number' => '05',
                        'title'  => __( 'Handover', 'meadan' ),
                        'body'   => __( 'The handover stage marks the completion of the project and the delivery of your finished spaces. Meadan conducts detailed inspections and quality reviews to ensure every aspect of the build meets our standards and the agreed project scope. We provide all relevant documentation, certifications, and guidance to support a smooth transition into occupancy. Our commitment to quality and attention to detail ensures clients receive a completed project built with care, expertise, and professionalism. Meadan also provides a 13-week period post-handover to address any minor issues whilst also honouring statutory major defect warranties of 6 years for NSW builds and 6 years and 6 months for Queensland clients and 2 years for minor defects in QLD and NSW.', 'meadan' ),
                    ],
                ];

                $default_left  = [ $default_steps[0], $default_steps[2], $default_steps[4] ];
                $default_right = [ $default_steps[1], $default_steps[3] ];
                ?>
                <div class="process-timeline__columns">
                    <div class="process-timeline__col process-timeline__col--left">
                        <?php foreach ( $default_left as $step ) : ?>
                            <div class="process-step process-step--left">
                                <div class="process-step__numrow">
                                    <span class="process-step__number"><?php echo esc_html( $step['number'] ); ?></span>
                                    <hr class="process-step__rule" aria-hidden="true">
                                </div>
                                <div class="process-step__text">
                                    <h3 class="process-step__title"><?php echo wp_kses_post( $step['title'] ); ?></h3>
                                    <p class="process-step__body"><?php echo esc_html( $step['body'] ); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="process-timeline__col process-timeline__col--right">
                        <?php foreach ( $default_right as $step ) : ?>
                            <div class="process-step process-step--right">
                                <div class="process-step__numrow process-step__numrow--right">
                                    <hr class="process-step__rule" aria-hidden="true">
                                    <span class="process-step__number"><?php echo esc_html( $step['number'] ); ?></span>
                                </div>
                                <div class="process-step__text">
                                    <h3 class="process-step__title"><?php echo wp_kses_post( $step['title'] ); ?></h3>
                                    <p class="process-step__body"><?php echo esc_html( $step['body'] ); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- .process-timeline__columns -->

            <?php endif; ?>

        </div><!-- .process-timeline__inner -->
    </section><!-- .process-timeline -->

    <!-- ── 4. Contact Section ─────────────────────────────────────────── -->
    <?php get_template_part( 'template-parts/page-contact-section' ); ?>

</main><!-- .site-main -->

<?php endwhile; ?>

<?php get_footer();
