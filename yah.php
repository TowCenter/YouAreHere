<?php

class YouAreHere {
	
	function __construct() {
		$this->setup_vars();
		//$this->setup_db();
		$this->setup_env();
		$this->setup_stories();
	}
	
	function setup_vars() {
		// Where are we?
		$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$this->request_url = parse_url($_SERVER['REQUEST_URI']);
		$this->api_url = "$protocol://{$_SERVER['HTTP_HOST']}{$this->request_url['path']}";
		$this->base_url = dirname($this->api_url);
		
		// Where will we save files to?
		$this->log_dir      = dirname(__DIR__) . '/log';
		$this->log_file     = "$this->log_dir/yah.log";
		$this->stories_dir  = __DIR__ . '/stories';
	}
	
	function setup_db() {
		$dsn = "pgsql:" .
		       "host=;" .
		       "dbname=;" .
		       "user=;" .
		       "port=5432;" .
		       "sslmode=require;" .
		       "password=";
		$this->db = new PDO($dsn);
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}
	
	function setup_env() {
		
		// Baseline PHP configs
		ini_set('display_errors', false);
		error_reporting(E_ALL);
		date_default_timezone_set('America/New_York');
		
		// Make sure we can write to the log dir
		if (!file_exists($this->log_dir)) {
			mkdir($this->log_dir);
		}
		
		if (is_writable($this->log_dir)) {
			//die("Please make $this->log_dir writable.");
			chmod($this->log_dir, 0755);
		}
		
		// Make sure we can write to the stories dir
		if (!is_writable($this->stories_dir)) {
			//die("Please make $this->stories_dir writable.");
			chmod($this->stories_dir, 0755);
		}
	}
	
	function setup_stories() {
		// Read known stories from the database
		$query = $this->db->exec("
			SELECT story_id, call_time, call_id, phone_number
			FROM stories
			ORDER BY call_time DESC
		");
		
		$this->stories = array();
		foreach ($query->fetchAll() as $story) {
			$this->anonymize_phone_number($story);
			$this->set_mp3_url($story);
		}
	}
	
	function handle_request() {
		$this->log_request();
		if (!empty($_REQUEST['get_stories'])) {
			header('Content-type: application/json');
			echo json_encode(array(
				'stories' => $this->stories
			));
		} else if (!empty($_REQUEST['get_config'])) {
			$config = print_r($_SERVER, true);
			$config['DB'] = getenv('DATABASE_URL');
			header('Content-type: application/json');
			echo json_encode(array(
				'stories' => $config
			));
		} else {
			header('Content-type: text/xml');
			echo '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n";
			echo "<Response>\n";
			if (isset($_REQUEST['save_story'])) {
				$this->save_story();
			} else if (isset($_REQUEST['play_story'])) {
				$index = intval($_REQUEST['play_story']);
				$this->play_story($index);
			} else {
				$this->record_story();
			}
			echo "</Response>\n";
		}
	}
	
	function get_stories() {
		$stories = array();
		foreach ($this->stories as $story) {
			$stories[] = array(
				'call_time' => $story[0],
				'phone_number' => $this->anonymize_phone_number($story[1]),
				'mp3_file' => $this->get_mp3_file($story[2])
			);
		}
		return $stories;
	}
	
	function anonymize_phone_number($story) {
		// Note: this is currently a very US-centric format, assumes 10 digits
		if (preg_match('/\d{4}$/', $story->phone_number, $matches)) {
			$story->phone_number = 'xxx-xxx-' . $matches[0];
		} else {
			$story->phone_number = 'xxx-xxx-xxxx';
		}
	}
	
	function set_mp3_url($story) {
		$story->mp3_url = "$this->base_url/stories/$this->call_id.mp3";
	}
	
	function log_request() {
		$vars = '';
		if (!empty($_REQUEST)) {
			$vars = trim(print_r($_REQUEST, true));
			$vars = "\n$vars";
		}
		error_log("{$_SERVER['REQUEST_METHOD']} {$this->request_url['path']}$vars");
	}
	
	function play_story($index = null) {
		if (!empty($this->stories[$index])) {
			$number = $index + 1;
			$id = $this->stories[$index][2];
			$this->say("Story number $number.");
			$this->play("$this->base_url/stories/$id.mp3");
			if ($index > 0) {
				$this->redirect(array(
					'play_story' => ($index - 1)
				));
				return;
			}
		}
		$this->say('You have heard all of the stories. Goodbye!');
		$this->hangup();
	}
	
	function save_story() {
		$fh = fopen($this->stories_file, 'a');
		fputcsv($fh, array(
			date('Y-m-d H:i:s'),
			$_POST['Caller'],
			$_POST['RecordingSid']
		));
		fclose($fh);
		$this->save_recording("{$_POST['RecordingUrl']}.mp3");
		if (!empty($this->stories)) {
			$this->say('Thank you for sharing your story.');
			$this->play_story(count($this->stories) - 1);
		} else {
			$this->say('You are the first one to share a story. Thanks for starting the conversation. Goodbye!');
			$this->hangup();
		}
	}
	
	function record_story() {
		$action = "$this->api_url?save_story=1";
		$this->say('Welcome to You Are Here. Please share a story with us. Press # when you finish.');
		echo "
			<Record
				maxLength=\"3600\"
				action=\"$action\"
				finishOnKey=\"#\" />
		";
	}
	
	function say($message) {
		echo "
	  	<Say voice=\"woman\">$message</Say>
		";
	}
	
	function play($recording) {
		echo "
	  	<Play>$recording</Play>
		";
	}
	
	function hangup() {
		echo "
	  	<Hangup/>
		";
	}
	
	function redirect($args) {
		$url = $this->api_url;
		if (is_array($args)) {
			$arg_pairs = array();
			foreach ($args as $key => $value) {
				$arg_pairs[] = urlencode($key) . '=' . urlencode($value);
			}
			$url .= '?' . implode('&', $arg_pairs);
		} else {
			$url .= "?$args";
		}
		echo "
	  	<Redirect method=\"POST\">$url</Redirect>
		";
	}
	
	function save_recording($url) {
		$curl = '/usr/bin/curl';
		exec("cd $this->stories_dir && $curl -O $url");
	}
}

$yah = new YouAreHere();
$yah->handle_request();
