<?php
/**
 * Meadan — 404.php
 */
get_header(); ?>

<main class="site-main site-main--404" id="main">
    <div class="error-404">
        <p class="error-404__label">404</p>
        <h1 class="error-404__title"><?php esc_html_e( 'Page Not Found', 'meadan' ); ?></h1>
        <p class="error-404__description"><?php esc_html_e( 'The page you are looking for does not exist. It may have been moved or removed.', 'meadan' ); ?></p>
        <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( 'Return Home', 'meadan' ); ?>
        </a>
    </div>
</main>

<?php get_footer();
