<?php

if ( ! empty( $text_link ) ) {
	echo sprintf( '<a href="%s">%s</a>', $data, $text_link );
} else {
	echo sprintf( '<a href="%s">%s</a>', $data, $data );
}

