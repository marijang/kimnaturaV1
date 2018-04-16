<?php
namespace wp_gdpr\lib;

if ( ! class_exists( 'wp_gdpr\Log' ) ) {
	class Gdpr_Log {

		/**
		 * Session KEY for log
		 */
		const SESSION_LOG = 'appsaloon_log';
		/**
		 * table name without prefix
		 */
		const TABLE_NAME = 'appsaloon_log';

		//data variable
		private $data = array();

		private static $instance = null;

		public static function instance() {

			// Check if instance is already exists
			if(self::$instance == null) {
				self::$instance = new Gdpr_Log();
			}

			return self::$instance;

		}

		/**
		 * Creating of logging table
		 */
		public static function create_log_table() {
			global $wpdb;

			$table_name = $wpdb->prefix . self::TABLE_NAME;

			$query = 'CREATE TABLE ' . $table_name . ' (
				  id INT(11) NOT NULL AUTO_INCREMENT,
				  message_type VARCHAR(20) DEFAULT NULL,
				  message TEXT NOT NULL,
				  file VARCHAR(255) DEFAULT NULL,
				  function VARCHAR(40) DEFAULT NULL,
				  line VARCHAR(40) DEFAULT NULL,
				  timestamp DATETIME DEFAULT NULL,
				  PRIMARY KEY (id)
				)';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $query );

			static::info( 'Log table updated' );
		}

		/**
		 * Save message with type debug
		 *
		 * @param       $msg      string  Message to save
		 * @param bool $file string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param bool $line string  In which line
		 */
		public function debug( $msg, $file = false, $function = false, $line = false ) {
			$this->add( 'debug', $msg, $file, $function, $line );
		}

		/**
		 * Save message with type info
		 *
		 * @param       $msg      string  Message to save
		 * @param bool $file string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param bool $line string  In which line
		 */
		public function info( $msg, $file = false, $function = false, $line = false ) {
			$this->add( 'info', $msg, $file, $function, $line );
		}

		/**
		 * Save message with type warn
		 *
		 * @param       $msg      string  Message to save
		 * @param bool $file string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param bool $line string  In which line
		 */
		public function warn( $msg, $file = false, $function = false, $line = false ) {
			$this->add( 'warn', $msg, $file, $function, $line );
		}

		/**
		 * Save message with type error
		 *
		 * @param       $msg      string  Message to save
		 * @param bool $file string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param bool $line string  In which line
		 */
		public function error( $msg, $file = false, $function = false, $line = false ) {
			$this->add( 'error', $msg, $file, $function, $line );
		}

		/**
		 * Save message with type fatal
		 *
		 * @param       $msg      string  Message to save
		 * @param bool $file string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param bool $line string  In which line
		 */
		public function fatal( $msg, $file = false, $function = false, $line = false ) {
			$this->add( 'fatal', $msg, $file, $function, $line );
		}

		/**
		 * Save message
		 *
		 * @param       $msg_type string  The message type (debug, info, warn, error or fatal)
		 * @param       $msg      string  Message to save
		 * @param       $file     string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param       $line     string  In which line
		 */
		public function add( $msg_type, $msg, $file, $function, $line ) {
			$backtrace = debug_backtrace();

			$file      = ( $file === false ) ? $backtrace[1]['file'] : $file;
			$line      = ( $line === false ) ? $backtrace[1]['line'] : $line;
			$function  = ( $function === false ) ? $backtrace[2]['function'] : $function;
			$timestamp = current_time( 'mysql' );

			$this->log_to_data( $msg_type, $msg, $file, $function, $line, $timestamp );
		}

		/**
		 * Save message to session
		 *
		 * @param       $msg_type   string  The message type (debug, info, warn, error or fatal)
		 * @param       $msg        string  Message to save
		 * @param       $file       string  In which file did the call came from
		 * @param bool $function string  In which function
		 * @param       $line       string  In which line
		 * @param       $timestamp  string  Timestamp of the log
		 */
		public function log_to_data( $msg_type, $msg, $file, $function, $line, $timestamp ) {
			$this->data[] = array(
				'message_type' => $msg_type,
				'message'      => $msg,
				'file'         => $file,
				'function'     => $function,
				'line'         => $line,
				'timestamp'    => $timestamp
			);
		}

		/**
		 * Saving log records to database
		 * This function will be executed as the last PHP function.
		 */
		public static function log_to_database() {


		}
	}
}
