<?php
/**
 * Loop item template
 */
$banner_url = $this->__get_banner_image_src();
$title = wp_strip_all_tags($this->get_settings_for_display('title'));

?>
<figure class="lastudio-banner la-lazyload-image lastudio-effect-<?php $this->__html( 'effect', '%1$s' ); ?><?php
if( $this->get_settings_for_display( 'custom_height' ) ) {
    echo ' image-custom-height';
}
?>" data-background-image="<?php echo esc_url($banner_url); ?>"><?php
    $target = $this->__get_html( 'link_target', ' target="%1$s"' );
    $a_link = $this->__get_html( 'link', '<a href="%1$s" class="lastudio-banner__link"' . $target . '><span class="hidden">' );
    $a_link .= $title;
    $a_link .= '</span></a>';
    echo '<div class="lastudio-banner__overlay"></div>';
    echo $this->__get_banner_image();
    echo '<figcaption class="lastudio-banner__content">';
    echo '<div class="lastudio-banner__content-wrap">';
    $title_tag = $this->__get_html( 'title_tag', '%1$s' );

    $this->__html( 'title', '<' . $title_tag  . ' class="lastudio-banner__title">%1$s</' . $title_tag  . '>' );
    $this->__html( 'subtitle', '<div class="lastudio-banner__subtitle">%1$s</div>' );
    $this->__html( 'text', '<div class="lastudio-banner__text">%1$s</div>' );
    $this->__html( 'btn_text', '<button type="button" class="elementor-button elementor-size-md lastudio-banner__button lastudio-carousel__item-button">%1$s</button>' );

    echo '</div>';
    if(!lastudio_get_theme_support('lastudio-kit::banner')){
        echo $a_link;
    }
    echo '</figcaption>';
    if(lastudio_get_theme_support('lastudio-kit::banner')){
        echo $a_link;
    }
    ?></figure>