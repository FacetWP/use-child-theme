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

        public $theme;
        public $child_theme;


        function __construct() {
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }


        function admin_init() {
            $this->theme = wp_get_theme();

            // Exit if this is a child theme
            if ( false !== $this->theme->parent() ) {
                return;
            }
            // Does the child theme exist?
            elseif ( $this->has_child_theme() ) {
                var_dump( 'USE THE CHILD THEME!' );
            }
            // Create a child theme
            else {
                $this->install_child_theme();
            }
        }


        /**
         * Does this theme have a child?
         */
        function has_child_theme() {
            $themes = wp_get_themes();
            $folder_name = basename( $this->theme->get_stylesheet_directory() );

            foreach ( $themes as $theme ) {
                if ( $folder_name == $theme->get( 'Template' ) ) {
                    $this->child_theme = $theme;
                    return true;
                }
            }

            return false;
        }


        /**
         * Activate child theme
         */
        function activate_child_theme() {
            $slug = basename( $this->child_theme->get_stylesheet_directory() );
            switch_theme( $slug );
        }


        /**
         * Create child theme
         */
        function install_child_theme() {
            $dir = $this->theme->get_stylesheet_directory() . '-child';

            $replacements = array(
                'theme_name' => $this->theme->get( 'Name' ) . ' Child',
                'theme_uri' => $this->theme->get( 'ThemeURI' ),
                'template' => basename( $this->theme->get_stylesheet_directory() ),
                'version' => '1.0',
            );

            $css = $this->style_css();
            foreach ( $replacements as $key => $str ) {
                $css = str_replace( '{' . $key . '}', $str, $css );
            }

            if ( wp_mkdir_p( $dir ) ) {
                file_put_contents( $dir . '/style.css', $css );
                file_put_contents( $dir . '/functions.php', $this->functions_php() );
            }
        }


        /**
         * Generate the style.css contents
         */
        function style_css() {
            ob_start();
?>
/*
Theme Name:     {theme_name}
Theme URI:      {theme_uri}
Template:       {template}
Version:        {version}
*/
<?php
            return ob_get_clean();
        }


        /**
         * Generate the functions.php contents
         */
        function functions_php() {
            ob_start();
?>
<?php echo '<'; ?>?php

function child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );
<?php
            return ob_get_clean();
        }
    }

    new Use_Child_Theme();
}
