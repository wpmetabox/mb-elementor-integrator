<?php

namespace MBEI\Classes;

use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use Exception;
use DOMDocument;
use DOMXPath;

class Document {

	public static function get_document( $post_id ) {

		$document = null;

		try {
			$document = Plugin::$instance->documents->get( $post_id );
		} catch ( Exception $e ) {
			return null;
		}

		if ( ! empty( $document ) && ! $document instanceof Theme_Document ) {
			$document = null;
		}

		return $document;
	}

	public static function get_document_data( $args = [] ) {

		$args = wp_parse_args($args, [
			'widget_id' => '',
			'post_id'   => '',
			'theme_id'  => '',
		]);

		if ( empty( $args['widget_id'] ) || empty( $args['post_id'] ) || empty( $args['theme_id'] ) ) {
			return '';
		}

		$document       = Plugin::$instance->documents->get_doc_for_frontend( $args['post_id'] );
		$theme_document = Plugin::$instance->documents->get_doc_for_frontend( $args['theme_id'] );

		$data[] = self::get_element_data( $args['widget_id'], $theme_document->get_elements_data() );

		// Change the current post, so widgets can use `documents->get_current`.
		Plugin::$instance->documents->switch_to_document( $document );

		ob_start();
		$document->print_elements_with_wrapper( $data );
		$content = ob_get_clean();
		$return  = self::clean_response( $content, $args['widget_id'] );

		Plugin::$instance->documents->restore_document();
		return $return;
	}

	private static function get_element_data( $id, $data ) {

		foreach ( $data as $element ) {
			if ( isset( $element['id'] ) && $id === $element['id'] ) {
				return $element;
			} else {
				if ( count( $element['elements'] ) ) {
					$element_children = self::get_element_data( $id, $element['elements'] );
					if ( $element_children ) {
						return $element_children;
					}
				}
			}
		}
		return false;
	}

	private static function clean_response( $html, $id ) {
		$content = '';
		$dom     = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );
		$xpath  = new DOMXPath( $dom );
		$childs = $xpath->query( '//div[@data-id="' . $id . '"]/div[@class="elementor-widget-container"]/div/* | //div[@data-elementor-type="custom_grid"]' );
		foreach ( $childs as $child ) {
			$content .= $dom->saveHTML( $child );
		}
		return $content;
	}

}
