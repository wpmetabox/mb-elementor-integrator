<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

echo sprintf( '<a href="%s">%s</a>', $data, $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

