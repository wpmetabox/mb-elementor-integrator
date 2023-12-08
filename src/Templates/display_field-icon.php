<?php
$icons = array_column( RWMB_Icon_Field::get_icons( $field ), 'svg', 'value' );
if ( ! empty( $icons[ $data ] ) ) {
	echo $icons[ $data ];
} else {
	RWMB_Icon_Field::enqueue_icon_font_style( $field );
	echo '<span class="' . $data . '"></span>';
}