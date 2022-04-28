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
	// Numbers
	for ( $i = 1; $i <= $total; $i++ ) {
		if ( $i === $page ) {
			echo sprintf( '<li class="current"><span>%s</span></li>', $i );
		} else {
			echo sprintf( '<li><a href="%s">%s</a></li>', ( 1 === $i ? $url : $url . '?mb_page=' . $i ), $i );
		}
	}
	// Next Button
	if ( $total === $page ) {
		echo sprintf( '<li class="next"><span>%s</span></li>', $text_next );
	} else {
		echo sprintf( '<li><a href="%s">%s</a></li>', $url . '?mb_page=' . ( $page + 1 ), $text_next );
	}
	?>
</ul>
