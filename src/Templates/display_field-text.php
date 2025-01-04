<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

echo wp_kses_post( $data );
