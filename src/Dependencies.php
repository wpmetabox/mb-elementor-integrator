<?php

namespace MBEI;

class Dependencies {

	public static function elementor( $pro = false ) {
		$elementor = true;

		if ( ! self::mb_is_plugin_active( 'elementor.php' ) ) {
			$elementor = false;
		}

		if ( true === $pro ) {
			if ( ! self::mb_is_plugin_active( 'elementor-pro.php' ) ) {
				$elementor = false;
			}
		}

		return $elementor;
	}

	private static function mb_clean_plugins( $mb_plugins ) {
		$results = [];
		foreach ( $mb_plugins as $mb_plugin ) {
			$folder              = '';
			$file                = '';
			list($folder, $file) = array_pad( explode( '/', $mb_plugin ), 2, '' );
			if ( ! $file ) {
				list($folder, $file) = array_pad( explode( '\\', $mb_plugin ), 2, '' ); // for windows
			}
			$results[] = $file;
		}
		return $results;
	}

	private static function mb_is_plugin_active( $plugin ) {
		$mb_plugins = self::mb_get_all_active_plugins();
		return in_array( $plugin, $mb_plugins );
	}

	private static function mb_get_all_active_plugins() {

		if ( function_exists( 'get_blog_option' ) ) {
			$mb_multi_site     = get_blog_option( get_current_blog_id(), 'active_plugins' );
			$mb_multi_site     = isset( $mb_multi_site ) ? $mb_multi_site : [];
			$mb_multi_sitewide = get_site_option( 'active_sitewide_plugins' );
			if ( is_array( $mb_multi_sitewide ) ) {
				foreach ( $mb_multi_sitewide as $key => $value ) {
					$mb_multi_site[] = $key;
				}
			}
			$mb_plugins = $mb_multi_site;
		} else {
			$mb_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		}

		return self::mb_clean_plugins( $mb_plugins );
	}

}
