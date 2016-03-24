<?php
/*
 * Use Child Theme
 * A drop-in to make it easy to use WordPress child themes
 * @version 0.2
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'Use_Child_Theme' ) ) {

    class Use_Child_Theme
    {

        public $theme;
        public $child_theme;
        public $show_notice = false;


        function __construct() {
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }


        /**
         * Get the wheels turning
         */
        function admin_init() {

            // Exit if unauthorized
            if ( ! current_user_can( 'switch_themes' ) ) {
                return;
            }

            add_action( 'admin_notices', array( $this, 'admin_notices' ) );
            add_action( 'wp_ajax_uct_activate', array( $this, 'activate_child_theme' ) );

            $this->theme = wp_get_theme();

            // Exit if child theme
            if ( false !== $this->theme->parent() ) {
                return;
            }
            // Does child theme exist?
            elseif ( $this->has_child_theme() ) {
                $this->show_notice = true;
            }
            // Create child theme
            else {
                $this->install_child_theme();
            }
        }


        /**
         * Show admin notices
         */
        function admin_notices() {
            if ( $this->show_notice ) {
?>
        <script>
        (function($) {
            $(function() {
                $(document).on('click', '.uct-activate', function() {
                    $.post(ajaxurl, {
                        action: 'uct_activate'
                    }, function(response) {
                        $('.uct-activate').closest('p').html(response);
                    });
                });
            });
        })(jQuery);
        </script>

        <div class="notice notice-error is-dismissible">
            <p>Please use the <?php echo $this->theme->get( 'Name' ); ?> child theme <a class="uct-activate" href="javascript:;">Activate now &raquo;</a></p>
        </div>
<?php
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
            $child_slug = basename( $this->child_theme->get_stylesheet_directory() );
            switch_theme( $child_slug );

            // Copy customizer settings, widgets, etc.
            $settings = get_option( 'theme_mods_' . $child_slug );
            if ( false === $settings ) {
                $parent_slug = basename( $this->theme->get_stylesheet_directory() );
                $parent_settings = get_option( 'theme_mods_' . $parent_slug );
                update_option( 'theme_mods_' . $child_slug, $parent_settings );
            }

            wp_die( 'All done!' );
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

                if ( is_readable( $this->theme->get_stylesheet_directory() . '/screenshot.png' ) ) {
                    copy( $this->theme->get_stylesheet_directory() . '/screenshot.png',
                        $dir . '/screenshot.png'
                    );
                }
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
