<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

echo wp_kses_post( $this->display_icon( $data, $field ) );
