<?php

namespace MBEI\Widgets\Skins;

use Elementor\Skin_Base;
use MBEI\Widgets\Skins\Post;

class Archive extends Post {

	private $pid;

	public function get_id() {
		return 'meta_box_skin_archive';
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/archive-posts/section_layout/before_section_end', [ $this, 'register_controls' ] );
	}

}
