<?php
$heading  = get_sub_field( 'heading' );
$subheading = get_sub_field( 'subheading' );
$courses  = get_sub_field( 'courses' );
?>
<section class="eeta-upcoming">
    <div class="eeta-upcoming__content">
        <div class="eeta-upcoming__header">
            <?php if ( $heading ) : ?>
                <h2 class="eeta-upcoming__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $subheading ) : ?>
                <p class="eeta-upcoming__subheading"><?php echo esc_html( $subheading ); ?></p>
            <?php endif; ?>
        </div>
        <?php if ( $courses ) : ?>
            <div class="eeta-upcoming__courses">
                <?php foreach ( $courses as $course ) :
                    $status     = $course['status'] ?? '';
                    $year_label = $course['year_label'] ?? '';
                    $title      = $course['title'] ?? '';
                    $desc       = $course['description'] ?? '';
                    $is_active  = ( $status === 'ongoing' );
                ?>
                    <div class="eeta-upcoming__course">
                        <div class="eeta-upcoming__year-col">
                            <span class="eeta-upcoming__dot<?php echo $is_active ? ' eeta-upcoming__dot--active' : ''; ?>" aria-hidden="true"></span>
                            <span class="eeta-upcoming__year-label"><?php echo esc_html( $year_label ); ?></span>
                        </div>
                        <div class="eeta-upcoming__course-content">
                            <?php if ( $title ) : ?>
                                <h3 class="eeta-upcoming__course-title"><?php echo esc_html( $title ); ?></h3>
                            <?php endif; ?>
                            <?php if ( $desc ) : ?>
                                <div class="eeta-upcoming__course-desc">
                                    <?php
                                    $paragraphs = explode( "\n\n", $desc );
                                    foreach ( $paragraphs as $p ) {
                                        $p = trim( $p );
                                        if ( $p ) {
                                            echo '<p>' . nl2br( esc_html( $p ) ) . '</p>';
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
