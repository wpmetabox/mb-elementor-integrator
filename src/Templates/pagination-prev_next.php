<?php
global $wp;
$url = home_url( $wp->request );
?>
<ul class="mbei-pagination">
	<?php
	// Prev Button
	if ( 1 === $page ) {
		echo sprintf( '<li class="prev"><span>%s</span></li>', $text_prev );
	} else {
		echo sprintf( '<li><a href="%s">%s</a></li>', ( 1 === $page - 1 ? $url : $url . '?mb_page=' . $page - 1 ), $text_prev );
	}
	// Next Button
	if ( $total === $page ) {
		echo sprintf( '<li class="next"><span>%s</span></li>', $text_next );
	} else {
		echo sprintf( '<li><a href="%s">%s</a></li>', $url . '?mb_page=' . ( $page + 1 ), $text_next );
	}
	?>
</ul>
