<?php

namespace MBEI\ThemeBuilder\Documents;

use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document;

class DGrid extends Theme_Section_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['condition_type']      = 'mb_grid';
		$properties['location']            = 'single';
		$properties['support_kit']         = true;
		$properties['support_site_editor'] = true;

		return $properties;
	}

	protected static function get_site_editor_type() {
		return 'mb_grid';
	}

	protected static function get_site_editor_thumbnail_url() {
		return plugin_dir_url(dirname(__DIR__) ) . 'assets/images/custom-grid.svg';
	}

	public function get_name() {
		return 'mb_grid';
	}

	public static function get_type() {
		return 'mb_grid';
	}

	public static function get_title() {
		return __( 'MB Grid', 'mb-elementor-integrator' );
	}

	protected static function get_editor_panel_categories() {
		$categories = [
			'mb-grid' => [
				'title' => __( 'Meta Box Grid', 'mb-elementor-integrator' ),
			],
		];
		return $categories + parent::get_editor_panel_categories();
	}

}
