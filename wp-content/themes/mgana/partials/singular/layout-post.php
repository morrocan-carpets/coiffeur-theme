<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_page_title = apply_filters('mgana/filter/show_page_title', true);

$blog_post_title = mgana_get_option('blog_post_title', 'below');
$blog_pn_nav = mgana_get_option('blog_pn_nav', 'off');
$featured_images_single = mgana_get_option('featured_images_single', 'off');
$blog_social_sharing_box = mgana_get_option('blog_social_sharing_box', 'off');
$blog_comments = mgana_get_option('blog_comments', 'off');
$single_post_thumbnail_size = mgana_get_option('single_post_thumbnail_size', 'full');


$move_related_to_bottom = mgana_string_to_bool(mgana_get_option('move_blog_related_to_bottom', 'off'));

$page_header_layout = mgana_get_page_header_layout();

$title_tag = 'h1';
if($show_page_title){
    $title_tag = 'h2';
}
if($page_header_layout == 'hide'){
    $title_tag = 'h1';
}
$blog_post_title = 'none';
$blog_social_sharing_box = 'off';
?>
<article class="single-content-article single-post-article">

    <?php
    if($blog_post_title == 'above'){
        get_template_part('partials/entry/meta','single2');
        the_title( '<header class="entry-header"><'.$title_tag.' class="entry-title entry-title-single" itemprop="headline">', '</'.$title_tag.'></header>' );
    }

    if(mgana_string_to_bool($featured_images_single)){
        mgana_single_post_thumbnail($single_post_thumbnail_size);
    }

    if($blog_post_title == 'below'){
        get_template_part('partials/entry/meta','single2');
        the_title( '<header class="entry-header"><'.$title_tag.' class="entry-title entry-title-single" itemprop="headline">', '</'.$title_tag.'></header>' );
    }

    ?>

    <div class="entry clearfix"<?php mgana_schema_markup( 'entry_content' ); ?>>

        <?php do_action( 'mgana/action/before_single_entry' ); ?>
        <?php the_content();
        wp_link_pages( array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mgana' ),
            'after'  => '</div>',
        ) );

        edit_post_link( null, '<span class="edit-link hidden">', '</span>' );

        ?>
        <?php do_action( 'mgana/action/after_single_entry' ); ?>
    </div>

    <?php
    if($blog_post_title == 'none'){
        echo '<div class="clearfix"></div><div class="post-meta post-meta--default">';
        echo '<span>';
        echo esc_html__('Posted in', 'mgana');
        echo '</span>';
        the_category(', ');
        echo '</div>';
    }
    ?>

    <footer class="entry-footer"><?php
        the_tags('<div class="tags-list"><span class="text-color-three">' . esc_html_x( 'Tags:', 'front-view', 'mgana' ) . ' </span><span class="tags-list-item">' ,', ','</span></div>');

        if(mgana_string_to_bool($blog_social_sharing_box)){
            echo '<div class="la-sharing-single-posts">';
            echo sprintf('<span class="title-share">%s</span>', esc_html_x('Share with', 'front-view', 'mgana') );
            mgana_social_sharing(get_the_permalink(), get_the_title(), (has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : ''));
            echo '</div>';
        }

    ?></footer>

    <?php
    if(mgana_string_to_bool($blog_pn_nav)){
        the_post_navigation( array(
            'next_text' => '<span class="blog_pn_nav-text">'. esc_html__('Next Post', 'mgana') .'</span><span class="blog_pn_nav blog_pn_nav-left">%image</span><span class="blog_pn_nav blog_pn_nav-right"><span class="blog_pn_nav-title">%title</span><span class="blog_pn_nav-meta">%date - %author</span></span>',
            'prev_text' => '<span class="blog_pn_nav-text">'. esc_html__('Prev Post', 'mgana') .'</span><span class="blog_pn_nav blog_pn_nav-left">%image</span><span class="blog_pn_nav blog_pn_nav-right"><span class="blog_pn_nav-title">%title</span><span class="blog_pn_nav-meta">%date - %author</span></span>'
        ) );
    }

    if(!$move_related_to_bottom){
        get_template_part('partials/singular/related-posts');
    }

    // Display comments
    if ( (comments_open() || get_comments_number()) && mgana_string_to_bool($blog_comments)) {
        comments_template();
    }

    ?>

</article>