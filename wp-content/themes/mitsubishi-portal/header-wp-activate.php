<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="wrapper">
  <div class="main">
    <main class="content">
      <p>
        <a href="https://www.mitsubishielectric.com.au/">
          <img class="img-fluid" width="280" src="<? POR_Core::instance()->helpers->the_assets_path( 'images/logo-au.svg' ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ) ?>">
        </a>
      </p>
