<?php
namespace MBEI\Tags\Post;

use Elementor\Core\DynamicTags\Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Post;
use MBEI\Traits\Fields\Text as TextField;

class Text extends Tag {
	use Base, Post, TextField;

	public function get_name() {
		return 'meta-box-text';
	}
}