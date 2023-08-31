<?php
/**
 * Loop item template
 */

use Elementor\Icons_Manager;

$title_tag       = $this->__get_html( 'title_html_tag', '%s' );
$sub_title_tag   = $this->__get_html( 'sub_title_html_tag', '%s' );
$front_side_icon = $this->get_settings_for_display('front_side_icon');
$back_side_icon  = $this->get_settings_for_display('back_side_icon');
?>
<div class="lastudio-animated-box <?php $this->__html( 'animation_effect', '%1$s' ); ?>">
	<div class="lastudio-animated-box__front">
		<div class="lastudio-animated-box__overlay"></div>
		<div class="lastudio-animated-box__inner">
			<?php
                if ( !empty( $front_side_icon['value'] ) ) {
                    echo '<div class="lastudio-animated-box__icon lastudio-animated-box__icon--front"><div class="lastudio-animated-box-icon-inner">';
                    Icons_Manager::render_icon( $front_side_icon, [ 'aria-hidden' => 'true' ] );
                    echo '</div></div>';
                }
			?>
			<div class="lastudio-animated-box__content">
			<?php
				$this->__html( 'front_side_title', '<' . $title_tag . ' class="lastudio-animated-box__title lastudio-animated-box__title--front">%1$s</' . $title_tag . '>' );
				$this->__html( 'front_side_subtitle', '<' . $sub_title_tag . ' class="lastudio-animated-box__subtitle lastudio-animated-box__subtitle--front">%1$s</' . $sub_title_tag . '>' );
				$this->__html( 'front_side_description', '<p class="lastudio-animated-box__description lastudio-animated-box__description--front">%1$s</p>' );
			?>
			</div>
		</div>
	</div>
	<div class="lastudio-animated-box__back">
		<div class="lastudio-animated-box__overlay"></div>
		<div class="lastudio-animated-box__inner">
			<?php
                if ( !empty( $back_side_icon['value'] ) ) {
                    echo '<div class="lastudio-animated-box__icon lastudio-animated-box__icon--back"><div class="lastudio-animated-box-icon-inner">';
                    Icons_Manager::render_icon( $back_side_icon, [ 'aria-hidden' => 'true' ] );
                    echo '</div></div>';
                }
			?>
			<div class="lastudio-animated-box__content">
			<?php
				$this->__html( 'back_side_title', '<' . $title_tag . ' class="lastudio-animated-box__title lastudio-animated-box__title--back">%1$s</' . $title_tag . '>' );
				$this->__html( 'back_side_subtitle', '<' . $sub_title_tag . ' class="lastudio-animated-box__subtitle lastudio-animated-box__subtitle--back">%1$s</' . $sub_title_tag . '>' );
				$this->__html( 'back_side_description', '<p class="lastudio-animated-box__description lastudio-animated-box__description--back">%1$s</p>' );
				$this->__glob_inc_if( 'action-button', array( 'back_side_button_link', 'back_side_button_text' ) );
			?>
			</div>
		</div>
	</div>
</div>