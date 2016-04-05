<?php
/*
 * Use Child Theme
 * A drop-in to make it easy to use WordPress child themes
 * @version 0.4
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'Use_Child_Theme' ) ) {

    class Use_Child_Theme
    {

        public $theme;
        public $child_slug;


        function __construct() {
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }


        function admin_init() {

            // Exit if unauthorized
            if ( ! current_user_can( 'switch_themes' ) ) {
                return;
            }

            // Exit if dismissed
            if ( false !== get_transient( 'uct_dismiss_notice' ) ) {
                return;
            }

            $this->theme = wp_get_theme();

            // Exit if child theme
            if ( false !== $this->theme->parent() ) {
                return;
            }

            // Exit if no direct access
            if ( 'direct' != get_filesystem_method() ) {
                return;
            }

            add_action( 'wp_ajax_uct_activate', array( $this, 'activate_child_theme' ) );
            add_action( 'wp_ajax_uct_dismiss', array( $this, 'dismiss_notice' ) );
            add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        }


        function admin_notices() {

            // Show only on specific admin page(s) (default: Appearance > Editor)
            $admin_screen_ids = apply_filters( 'uct_admin_screen_ids', array( 'theme-editor' ) );
            $screen = get_current_screen();
            if ( ! isset( $screen->id ) || ! in_array( $screen->id, (array) $admin_screen_ids ) ) {
                return;
            }
?>
        <script>
        (function($) {
            $(function() {
                $(document).on('click', '.uct-activate', function() {
                    $.post(ajaxurl, { action: 'uct_activate' }, function(response) {
                        $('.uct-notice p').html(response);
                    });
                });

                $(document).on('click', '.uct-notice .notice-dismiss', function() {
                    $.post(ajaxurl, { action: 'uct_dismiss' });
                });
            });
        })(jQuery);
        </script>

        <div class="notice notice-error uct-notice is-dismissible">
            <p>
                <?php printf( esc_html__( 'Please use a %s child theme to make changes', 'use-child-theme' ), $this->theme->get( 'Name' ) ); ?>
                <a class="uct-activate" href="javascript:;"><?php esc_html_e( 'Activate now', 'use-child-theme' ); ?></a>
            </p>
        </div>
<?php
        }


        function dismiss_notice() {
            set_transient( 'uct_dismiss_notice', 'yes', apply_filters( 'uct_dismiss_timeout', 86400 ) );
            exit;
        }


        function has_child_theme() {
            $themes = wp_get_themes();
            $folder_name = $this->theme->get_stylesheet();
            $this->child_slug = $folder_name . '-child';

            foreach ( $themes as $theme ) {
                if ( $folder_name == $theme->get( 'Template' ) ) {
                    $this->child_slug = $theme->get_stylesheet();
                    return true;
                }
            }

            return false;
        }


        function activate_child_theme() {
            $parent_slug = $this->theme->get_stylesheet();

            // Create child theme
            if ( ! $this->has_child_theme() ) {
                $this->create_child_theme();
            }

            switch_theme( $this->child_slug );

            // Copy customizer settings, widgets, etc.
            $settings = get_option( 'theme_mods_' . $this->child_slug );

            if ( false === $settings ) {
                $parent_settings = get_option( 'theme_mods_' . $parent_slug );
                update_option( 'theme_mods_' . $this->child_slug, $parent_settings );
            }

            wp_die( esc_html__( 'All done!', 'use-child-theme' ) );
        }


        function create_child_theme() {
            $parent_dir = $this->theme->get_stylesheet_directory();
            $child_dir = $parent_dir . '-child';

            if ( wp_mkdir_p( $child_dir ) ) {
                $creds = request_filesystem_credentials( admin_url() );
                WP_Filesystem( $creds ); // we already have direct access

                global $wp_filesystem;
                $wp_filesystem->put_contents( $child_dir . '/style.css', $this->style_css() );
                $wp_filesystem->put_contents( $child_dir . '/functions.php', $this->functions_php() );

                if ( false !== ( $img = $this->theme->get_screenshot( 'relative' ) ) ) {
                    $wp_filesystem->copy( "$parent_dir/$img", "$child_dir/$img" );
                }
            }
            else {
                wp_die( esc_html__( 'Error: theme folder not writable', 'use-child-theme' ) );
            }
        }


        function style_css() {
            $name = $this->theme->get( 'Name' ) . ' Child';
            $uri = $this->theme->get( 'ThemeURI' );
            $parent = $this->theme->get_stylesheet();

            return "<?php
/*
Theme Name:     {$name}
Theme URI:      {$uri}
Template:       {$parent}
Version:        0.1
*/
";
        }


        function functions_php() {
            return "<?php

function child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );
";
        }
    }

    new Use_Child_Theme();
}
