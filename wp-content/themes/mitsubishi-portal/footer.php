<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

?>
</main><!-- .content -->

<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
  ?>
<?php } ?>

</div><!-- .main -->
</div><!-- .wrapper -->

<?php wp_footer(); ?>
</body>
</html>
