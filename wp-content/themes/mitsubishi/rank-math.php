<?php
add_filter( 'rank_math/sitemap/enable_caching', '__return_false');
add_action( 'acf/init', 'load_custom_sitemap_urls' );
function load_custom_sitemap_urls () {
    if( !isset( $_GET['q'] ) ) return; 
    $q = $_GET['q'];
    $index_groups = get_field( 'index_group', 'option' );

    if( $index_groups ) :
        foreach( $index_groups as $index_group ) :
            $sitemap = $index_group[ 'sitemap' ];
            if( $sitemap . '-sitemap.xml' == $q ) {
                add_action( "rank_math/sitemap/{$sitemap}_content", 'add_custom_sitemap_url' );
            }
        endforeach;
    endif;

}

function add_custom_sitemap_url() {
    global $wp_filter, $wp_current_filter;
    $action = end( $wp_current_filter );
    $filter = $wp_filter[ $action ];
    $index_groups = get_field( 'index_group', 'option' );

    foreach( $index_groups as $index_group ) :
        $sitemap = $index_group[ 'sitemap' ];
        if( stripos( $action, $sitemap . '_content' ) !== false ) {
            $last_modified = $index_group[ 'last_modified' ];
            $atom_date = date( DATE_ATOM );
            if( $last_modified ) {
                $atom_date = date( DATE_ATOM, strtotime( $last_modified ) );
            }
            
            $urls = explode( PHP_EOL, $index_group[ 'urls' ] );
            $xml = '';
            foreach( $urls as $url ) {
                if( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                    $xml .= '<url>';
                    $xml .= "<loc>{$url}</loc>";
                    $xml .= "<lastmod>{$atom_date}</lastmod>";
                    $xml .= '</url>';
                }
            }
            return $xml;
        }
    endforeach;
}