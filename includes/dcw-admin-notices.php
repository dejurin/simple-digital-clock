<?php
/**
 * @version 0.1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'DCW_Admin_Notices' ) ) {

    class DCW_Admin_Notices {

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
            $dismiss_option = filter_input( INPUT_GET, 'DCW_dismiss', FILTER_SANITIZE_EMAIL );
            if ( is_string( $dismiss_option ) ) {
                update_option( "DCW_dismissed_$dismiss_option", true );
                wp_die();
            }
        }

        public function action_admin_enqueue_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'dcw-notify',
                DCW_URL.'assets/admin/js/dcw-notify.js',
                ['jquery']
            );
        }

        public function action_admin_notices() {
            foreach ( explode( ',', self::TYPES ) as $type ) {
                foreach ( $this->admin_notices->{$type} as $admin_notice ) {
                    $dismiss_url = add_query_arg([
                        'DCW_dismiss' => $admin_notice->dismiss_option
                    ], admin_url() );
                    $screen = get_current_screen();
                    if ( ! get_option( "DCW_dismissed_{$admin_notice->dismiss_option}" ) || strpos($screen->id, 'digital-clock-widget') !== false) {
                        ?><div class="notice is-dismissible dcw-notice notice-<?php echo $type;
                            if ( $admin_notice->dismiss_option ) {
                                echo ' is-dismissible" data-dismiss-url="' . esc_url( $dismiss_url );
                            } ?>">
                            <div class="dcw-rate-notice-container">
                                <div class="logo-img">
                                    <img alt="<?php echo DCW_NAME; ?>" src="<?php echo DCW_URL.'assets/admin/img/icon.svg'; ?>" style="width:96px">
                                </div>
                                <div>
                                    <h2>🥰 <?php _e('Please rate our free', 'digital-clock-widget'); ?>
                                    &laquo;<?php echo DCW_NAME; ?>&raquo;</h2>
                                    <hr>
                                    <p><?php _e('Your valuable feedback will help us improve.', 'digital-clock-widget'); ?><br><?php _e('It will only take a few minutes', 'digital-clock-widget'); ?>: <a href="https://wordpress.org/support/plugin/digital-clock-widget/reviews/#new-post" rel="noopener" target="_blank"><?php _e('Rate it now', 'digital-clock-widget'); ?></a> 👍</p>
                                    <p><a href="https://wordpress.org/support/plugin/digital-clock-widget/reviews/#new-post" rel="noopener" target="_blank"><img src="<?php echo DCW_URL.'assets/admin/img/stars.png'; ?>" alt="<?php _e('Rating', 'digital-clock-widget'); ?>"></a></p>
                                </div>
                            </div>
                        </div>
                        <style>
                            .dcw-rate-notice-container {
                                display: flex;
                                padding: 10px 0;
                            }
                            .dcw-rate-notice-container .logo-img {
                                margin-right: 15px;
                            }
                            .dcw-rate-notice-container h2 {
                                margin: 0;
                            }
                            .dcw-rate-notice-container p {
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