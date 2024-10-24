<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

printf( '<a href="%s">%s</a>', esc_url( $data ), esc_html( $data ) );
