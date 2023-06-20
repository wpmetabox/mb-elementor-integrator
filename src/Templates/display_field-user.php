<?php
$user = get_user_by( 'id', $data );
echo $user->data->display_name;
