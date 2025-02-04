<?php
/**
 * Plugin Name: MB Elementor Integration
 * Plugin URI:  https://metabox.io/plugins/mb-elementor-integrator/
 * Description: Integrates Meta Box and Elementor Page Builder.
 * Version:     2.2.3
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 *
 * Copyright (C) 2010-2025 Tran Ngoc Tuan Anh. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Prevent loading this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( file_exists( __DIR__ . '/vendor' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

if ( class_exists( 'MBEI\Loader' ) ) {
	new \MBEI\Loader();
}
