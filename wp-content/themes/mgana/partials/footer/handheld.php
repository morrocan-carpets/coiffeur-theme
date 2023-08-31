<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$mobile_footer_bar_items = mgana_get_option('header_mb_footer_bar_component', array());
$enable_header_mb_footer_bar = mgana_get_option('enable_header_mb_footer_bar');

if( mgana_string_to_bool($enable_header_mb_footer_bar) && !empty($mobile_footer_bar_items)): ?>
    <div class="footer-handheld-footer-bar">
        <div class="footer-handheld__inner">
            <?php
            foreach($mobile_footer_bar_items as $component){
                if(isset($component['type'])){
                    echo mgana_render_access_component($component['type'], $component, 'handheld_component');
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>