<?php
$post = get_post( $data );
echo $post->post_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
