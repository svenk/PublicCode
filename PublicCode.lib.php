<?php

require_once dirname(__FILE__).'/../lib/CryptoLinks/CryptoLinks.class.php';

class PublicCode {
	public $codeviewer;
	public $has_main = false;

	public function __construct() {
		$this->codeviewer = new CodeViewer();
		$this->codeviewer->vars['page_url'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'No-REQUEST-URI-available';
	}

	private static $instance = null;
	public static function getInstance() {
		if (self::$instance === null)
			self::$instance = new PublicCode();
		return self::$instance;
	}

	/// Convenience function for setting the path to the public code viewer
	public static function setViewerPath($path) {
		self::getInstance()->codeviewer->baseurl = $path;
	}

	// static functions for class usage as quasi global array
	const MAIN = 1; // for $as_main_file
	const AUX = 2;
	public static function add($filename, $as_main_file_or_type=false) {
		$pc = self::getInstance();
		$realname = realpath($filename);
		if(!$realname) {
			// error or file does not exist. Do not append.
			return false;
		}
		$pc->codeviewer->files[] = $realname;

		// register as current file
		if($as_main_file_or_type == self::MAIN) {
			$pc->has_main = true;
			$pc->codeviewer->current_file_id = count($pc->codeviewer->files)-1;
		}
		if($as_main_file_or_type == self::AUX) {
			// todo: Wenn nur AUX vorhanden sind, keinen Sourceviewer anzeigen!
		}
	}

	// static functions for class usage as quasi global array
	public static function get_secure_link($id=Null) {
		$pc = self::getInstance();
		return $pc->has_main ? $pc->codeviewer->get_secure_link($id) : false;
	}

	public static function twig_helper($env, $context) {
		//var_dump($env);
		//var_dump($context);
		//exit;
	}
}

class CodeViewer extends CryptoLinker {
	public $baseurl = '/~koeppel/src/code-viewer/';
	const ALL_FILES = -1;

	public function get_file_data($id=self::ALL_FILES, $full_data=False) {
		// if no $id is given, retrieve from all.
		if($id===self::ALL_FILES) {
			return array_map(array($this, 'get_file_data'), range(0, count($this->files)-1));
		}

		if(!isset($this->files[$id])) {
			return array('error' => 'Can only have id < '.count($this->files));
		}

		$filename = $this->files[$id];
		$data = array(
			'basename' => basename($filename),
			'_filename' => $filename, // for testing
			'id' => $id,
			'link' => $this->get_secure_link($id),
		);
		if($full_data) {
			$data['code'] = highlight_file($filename, true);
		}

		return $data;
	}

	public function is_file_allowed($filename) {
		// The Codeviewer only allows introspection of files inside public_html.
		// This is another security mechanism

		return (
				preg_match('#^/home/koeppel/public_html/#', $filename) # only in public_html
			   ||   preg_match('#^/home/koeppel/UniOrdner/\d+\. Semester#', $filename) # or in public UniOrdner
			) && !preg_match('#\.\.|/\.#', $filename);  # and no directory acrobatics or dotfiles
	}

	public function check_current_id() {
		if(!isset(             $this->get[$this->display_field_name] ) ||
		   !isset($this->files[$this->get[$this->display_field_name]])) {
			$this->get[$this->display_field_name] = 0; // i.e. first page. Works most likely...
			#print "REDIRECTING ID:"; var_dump($this->get); exit;
			header('Location: ?'.http_build_query($this->get));
		}
		$this->current_file_id = $this->get[$this->display_field_name];
	}
}


