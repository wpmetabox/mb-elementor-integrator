<?php
namespace MBEI\Tags\Archive;

use Elementor\Core\DynamicTags\Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Archive;
use MBEI\Traits\Fields\Text as TextField;

class Text extends Tag {
	use Base, Archive, TextField;

	public function get_name() {
		return 'meta-box-archive-text';
	}
}