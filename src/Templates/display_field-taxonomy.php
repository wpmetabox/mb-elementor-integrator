<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$term = get_term( $data );
echo esc_html( $term->name );
