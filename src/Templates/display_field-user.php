<?php
$user = get_user_by( 'id', $data );
echo esc_html( $user->data->display_name );
