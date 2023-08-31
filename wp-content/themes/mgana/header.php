<?php
/**
 * The Header for our theme.
 *
 * @package Mgana WordPress theme
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?><?php mgana_schema_markup( 'html' ); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <link rel="profile" href="//gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action('mgana/action/before_outer_wrap'); ?>

<div id="outer-wrap" class="site">

    <?php do_action('mgana/action/before_wrap'); ?>

    <div id="wrap">
        <?php

            do_action('mgana/action/before_header');

            if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
                do_action('mgana/action/header');
            }

            do_action('mgana/action/after_header');

        ?>

        <?php do_action('mgana/action/before_main'); ?>

        <main id="main" class="site-main"<?php mgana_schema_markup('main') ?>>
            <?php

                do_action('mgana/action/before_page_header');

                do_action('mgana/action/page_header');

                do_action('mgana/action/after_page_header');