<?php
use MBEI\GroupField;

if ( true === $return ) {
	echo GroupField::change_url_ssl( wp_get_attachment_image_src( $data, 'full' )[0] );
} else {
	echo wp_get_attachment_image( $data, 'full' );
}

