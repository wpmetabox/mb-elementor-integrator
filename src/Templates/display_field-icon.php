<?php
$icons = array_column( $field['options'], 'label', 'value' );
if ( $icons[ $data ] ) {
	$pos = strpos( $icons[ $data ], '</svg>' );
	if ( $pos !== false ) {
		$icons[ $data ] = substr( $icons[ $data ], 0, $pos + strlen( '</svg>' ) );
		echo $icons[ $data ];
	} elseif ( $field['icon_css'] && is_string( $field['icon_css'] ) ) {
		if ( ! is_admin() ) {
			$handle = md5( $field['icon_css'] );
			wp_enqueue_style( $handle, $field['icon_css'], [], RWMB_VER );
		} else {
			add_action( 'elementor/preview/enqueue_styles', function() use ( $field ) {
				$handle = md5( $field['icon_css'] );
				wp_enqueue_style( $handle, $field['icon_css'], [], RWMB_VER );
			} );
		}
	} elseif ( is_callable( $field['icon_css'] ) && ! is_admin() ) {
		$field['icon_css']();
	} else {
		echo '<span class="' . $data . '"></span>';
	}
}