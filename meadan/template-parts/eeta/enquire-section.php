<?php
$heading         = get_sub_field( 'heading' );
$subheading      = get_sub_field( 'subheading' );
$intro_text      = get_sub_field( 'intro_text' );
$benefits        = get_sub_field( 'benefits' );
$disclaimer_text = get_sub_field( 'disclaimer_text' );
$form_id         = get_sub_field( 'form_id' );
$bg_image        = get_sub_field( 'bg_image' );
$bg_style        = $bg_image ? ' style="background-image:url(' . esc_url( $bg_image['url'] ) . ');"' : '';
?>
<section class="eeta-enquire" id="enquire">
    <div class="eeta-enquire__copy">
        <div class="eeta-enquire__copy-inner">
            <?php if ( $heading ) : ?>
                <h2 class="eeta-enquire__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $subheading ) : ?>
                <p class="eeta-enquire__subheading"><?php echo esc_html( $subheading ); ?></p>
            <?php endif; ?>
            <?php if ( $intro_text ) : ?>
                <p class="eeta-enquire__intro"><?php echo esc_html( $intro_text ); ?></p>
            <?php endif; ?>
            <?php if ( $benefits ) : ?>
                <ul class="eeta-enquire__benefits">
                    <?php foreach ( $benefits as $benefit ) : ?>
                        <?php if ( ! empty( $benefit['benefit_text'] ) ) : ?>
                            <li class="eeta-enquire__benefit">
                                <span class="eeta-enquire__check" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="#00A651" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <?php echo esc_html( $benefit['benefit_text'] ); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ( $disclaimer_text ) : ?>
                <p class="eeta-enquire__disclaimer"><?php echo esc_html( $disclaimer_text ); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="eeta-enquire__form-panel"<?php echo $bg_style; ?>>
        <div class="eeta-enquire__form-container">
            <?php
            if ( $form_id && class_exists( 'GFCommon' ) ) {
                gravity_form( (int) $form_id, false, false, false, null, true );
            } elseif ( $form_id ) {
                echo '<p class="eeta-enquire__no-gf">Please install Gravity Forms and create form ID ' . esc_html( $form_id ) . '.</p>';
            }
            ?>
        </div>
    </div>
</section>
