<?php

namespace MBEI\Classes;

class Animation {

	private static function animations() {
		return [
			'default'      => [
				'label' => __( 'Default', 'mb-elementor-integrator' ),
				'html'  => '<div class="lds-ellipsis mbei-lazy-load-animation"><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div></div>',
			],
			'progress_bar' => [
				'label' => __( 'Progress Bar', 'mb-elementor-integrator' ),
				'html'  => '<div class="barload-wrapper  mbei-lazy-load-animation"><div class="barload-border mbei-ll-brcolor"><div class="barload-whitespace"><div class="barload-line mbei-ll-bgcolor"></div></div></div></div>',
			],
			'running_dots' => [
				'label' => __( 'Running Dots', 'mb-elementor-integrator' ),
				'html'  => '<div class="ballsload-container mbei-lazy-load-animation"><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div><div class="mbei-ll-bgcolor"></div></div>',
			],
			'ball_slide'   => [
				'label' => __( 'Ball Slide', 'mb-elementor-integrator' ),
				'html'  => '<div id="movingBallG" class="mbei-lazy-load-animation"><div class="movingBallLineG  mbei-ll-bgcolor"></div><div id="movingBallG_1" class="movingBallG mbei-ll-bgcolor"></div></div>',
			],
		];
	}

	public static function get_lazy_load_animations_html( $animation ) {
		$arrs = self::animations();
		return $arrs[ $animation ]['html'];
	}

	public static function get_lazy_load_animations_list() {
		$arrs = self::animations();
		foreach ( $arrs as $key => $arr ) {
			$options[ $key ] = $arr['label'];
		}
		return $options;
	}

}
