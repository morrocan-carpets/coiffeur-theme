<?php
/**
 * Outputs page article
 *
 * @package Mgana WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<div class="entry"<?php mgana_schema_markup( 'entry_content' ); ?>>

    <?php do_action( 'mgana/action/before_page_entry' ); ?>

	<?php the_content();

	wp_link_pages( array(
		'before' => '<div class="clearfix"></div><div class="page-links">' . esc_html__( 'Pages:', 'mgana' ),
		'after'  => '</div>',
	) );
	?>
    <div class="clearfix"></div>

    <?php do_action( 'mgana/action/after_page_entry' ); ?>

</div>