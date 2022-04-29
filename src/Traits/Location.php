<?php
/**
 * Skin trait for all object type that handle register widget location.
 */

namespace MBEI\Traits;

use ElementorPro\Modules\ThemeBuilder\Documents\Loop;
use ElementorPro\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Exception;

trait Location {

	public function get_id() {
		return 'mb_location';
	}

	public function get_class_full_name() {
		return '';
	}

	public function register_document() {
		$class_full_name = $this->get_class_full_name();
		if ( ! empty( $class_full_name ) ) {
			Plugin::elementor()->documents->register_document_type( $this->get_id(), $class_full_name );
			Source_Local::add_template_type( $this->get_id() );
		}
	}

}
