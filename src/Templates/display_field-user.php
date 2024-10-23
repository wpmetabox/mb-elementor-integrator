<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$user = get_user_by( 'id', $data );
echo $user->data->display_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
