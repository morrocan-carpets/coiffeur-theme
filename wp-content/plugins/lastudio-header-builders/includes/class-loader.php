<?php
/**
 * Header Builder - Loader Class.
 *
 * @author LaStudio
 */
// don't load directly.
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}
if (!class_exists('LAHB_Loader')) {
    class LAHB_Loader
    {
        /**
         * Instance of this class.
         *
         * @since    1.0.0
         * @access    private
         * @var        LAHB_Loader
         */
        private static $instance;

        /**
         * Provides access to a single instance of a module using the singleton pattern.
         *
         * @return    object
         * @since    1.0.0
         */
        public static function get_instance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor.
         *
         * @since    1.0.0
         */
        protected function __construct() {
            spl_autoload_register(array( $this, 'load_dependencies' ));
        }

        /**
         * Loads all the LaStudio Header Builder dependencies.
         *
         * @since    1.0.0
         */
        private function load_dependencies($class) {
            if (strpos($class, LAHB::CLASS_PREFIX) !== false && !class_exists($class)) {
                $classFileName = 'class-' . str_replace(LAHB::FILE_PREFIX, '', str_replace('_', '-', strtolower($class))) . '.php';
                $path = LAHB::get_path() . 'includes/' . $classFileName;

                require_once $path;
            }
        }
    }
}
