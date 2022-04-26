<?php
global $wp;
$url = home_url( $wp->request );
?>
<ul class="mbei-pagination">
	<?php
	for ( $i = 1; $i <= $total; $i++ ) {
		if ( $i === $page ) {
			echo sprintf( '<li class="current"><span>%s</span></li>', $i );
		} else {
			echo sprintf( '<li><a href="%s">%s</a></li>', ( 1 === $i ? $url : $url . '?mb_page=' . $i ), $i );
		}
	}
	?>
</ul>
