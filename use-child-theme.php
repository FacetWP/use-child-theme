<?php
/*
Plugin Name: Use Child Theme
Plugin URI: https://facetwp.com/
Description: Encourage use of child themes
Version: 0.1
Author: Matt Gibbs

Copyright 2016 Matt Gibbs

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'Use_Child_Theme' ) ) {

    class Use_Child_Theme
    {
        function __construct() {
            add_action( 'init', array( $this, 'init' ) );
        }


        function init() {
            $theme = wp_get_theme();
        }
    }

    new Use_Child_Theme();
}
