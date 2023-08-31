<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<header id="lastudio-header-builder" class="<?php echo mgana_header_classes(); ?>"<?php mgana_schema_markup( 'header' ); ?>>
    <?php

        $value = mgana_get_header_layout();

        if (class_exists('LAHB', false)) {
            do_action('lastudio/header-builder/render-output');
        }
        else {
            get_template_part('partials/header/content', $value);
        }
    ?>
</header>