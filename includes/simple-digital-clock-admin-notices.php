<?php
/**
 * @version 0.1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'SDCW_Admin_Notices' ) ) {

    class SDCW_Admin_Notices {

        private static $_instance;
        private $admin_notices;
        const TYPES = 'error,warning,info,success';

        private function __construct() {
            $this->admin_notices = new stdClass();
            foreach (explode( ',', self::TYPES) as $type) {
                $this->admin_notices->{$type} = [];
            }
            add_action( 'admin_init', [ &$this, 'action_admin_init' ] );
            add_action( 'admin_notices', [ &$this, 'action_admin_notices' ] );
            add_action( 'admin_enqueue_scripts', [ &$this, 'action_admin_enqueue_scripts' ] );
        }

        public static function get_instance() {
            if ( ! ( self::$_instance instanceof self ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function action_admin_init() {
            echo "OK";
            $dismiss_option = filter_input( INPUT_GET, 'SDCW_dismiss', FILTER_SANITIZE_EMAIL );
            if ( is_string( $dismiss_option ) ) {
                update_option( "SDCW_dismissed_$dismiss_option", true );
                wp_die();
            }
        }

        public function action_admin_enqueue_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'simple-digital-clock-notify',
                SDCW_URL.'assets/admin/js/simple-digital-clock-notify.js',
                ['jquery']
            );
            wp_enqueue_script(
                'simple-digital-clock',
                SDCW_URL.'assets/public/js/simple-digital-clock.min.js',
            );
        }

        public function action_admin_notices() {
            foreach ( explode( ',', self::TYPES ) as $type ) {
                foreach ( $this->admin_notices->{$type} as $admin_notice ) {
                    $dismiss_url = add_query_arg([
                        'SDCW_dismiss' => $admin_notice->dismiss_option
                    ], admin_url() );
                    $screen = get_current_screen();
                    if (! get_option( "SDCW_dismissed_{$admin_notice->dismiss_option}" ) == '1' || strpos($screen->id, 'simple-digital-clock') !== false) {
                        ?><div class="notice is-dismissible simple-digital-clock-notice notice-<?php esc_attr_e($type, 'simple-digital-clock');
                            if ( $admin_notice->dismiss_option ) {
                                echo ' is-dismissible" data-dismiss-url="',esc_url( $dismiss_url ),'"';
                            } ?>>
                            <div class="simple-digital-clock-rate-notice-container">
                                <div class="logo-img">
                                    <img alt="<?php esc_attr_e(SDCW_NAME, 'simple-digital-clock'); ?>" src="<?php esc_attr_e(SDCW_URL.'assets/admin/img/icon.svg', 'simple-digital-clock'); ?>" style="width:96px">
                                </div>
                                <div>
                                    <h2>🥰 <?php esc_html_e('Please rate our free', 'simple-digital-clock'); ?>
                                    &laquo;<?php esc_html_e(SDCW_NAME, 'simple-digital-clock'); ?>&raquo;</h2>
                                    <hr>
                                    <p><?php esc_html_e('Your valuable feedback will help us improve.', 'simple-digital-clock'); ?><br><?php esc_html_e('It will only take a few minutes', 'simple-digital-clock'); ?>: <a href="https://wordpress.org/support/plugin/simple-digital-clock/reviews/#new-post" rel="noopener" target="_blank"><?php esc_html_e('Rate it now', 'simple-digital-clock'); ?></a> 👍</p>
                                    <p><a href="https://wordpress.org/support/plugin/simple-digital-clock/reviews/#new-post" rel="noopener" target="_blank"><img src="<?php esc_attr_e(SDCW_URL.'assets/admin/img/stars.png', 'simple-digital-clock'); ?>" alt="<?php esc_attr_e('Rating', 'simple-digital-clock'); ?>"></a></p>
                                </div>
                            </div>
                        </div>
                        <style>
                            .simple-digital-clock-rate-notice-container {
                                display: flex;
                                padding: 10px 0;
                            }
                            .simple-digital-clock-rate-notice-container .logo-img {
                                margin-right: 15px;
                            }
                            .simple-digital-clock-rate-notice-container h2 {
                                margin: 0;
                            }
                            .simple-digital-clock-rate-notice-container p {
                                padding: 0;
                                margin: 0;
                            }
                        </style><?php
                    }
                }
            }
        }

        public function error( $message, $dismiss_option = false ) {
            $this->notice( 'error', $message, $dismiss_option );
        }

        public function warning( $message, $dismiss_option = false ) {
            $this->notice( 'warning', $message, $dismiss_option );
        }

        public function success( $message, $dismiss_option = false ) {
            $this->notice( 'success', $message, $dismiss_option );
        }

        public function info( $message, $dismiss_option = false ) {
            $this->notice( 'info', $message, $dismiss_option );
        }

        private function notice( $type, $message, $dismiss_option ) {
            $notice = new stdClass();
            $notice->message = $message;
            $notice->dismiss_option = $dismiss_option;

            $this->admin_notices->{$type}[] = $notice;
        }
    }
}
