<?php

class YouAreHere {
	
	function __construct() {
		$this->setup_vars();
		$this->setup_db();
		$this->setup_env();
		$this->setup_stories();
	}
	
	function setup_vars() {
		// Where are we?
		$protocol = $this->get_protocol();
		$this->request_url = parse_url($_SERVER['REQUEST_URI']);
		$this->api_url = "$protocol://{$_SERVER['HTTP_HOST']}{$this->request_url['path']}";
		$this->base_url = dirname($this->api_url);
		
		// Where will we save files to?
		$this->log_dir       = dirname(__DIR__) . '/log';
		$this->log_file      = "$this->log_dir/yah.log";
		$this->responses_dir = __DIR__ . '/responses';
	}
	
	function setup_db() {
		require_once __DIR__ . '/config.php';
		extract($database);
		if ($host == 'localhost') {
			$host = '127.0.0.1';
		}
		$dsn = "$driver:" .
		       "host=$host;" .
		       "dbname=$dbname;";
		$this->db = new PDO($dsn, $username, $password);
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}
	
	function setup_env() {
		
		// Baseline PHP configs
		ini_set('error_log', $this->log_file);
		ini_set('display_errors', true);
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
		if (!is_writable($this->responses_dir)) {
			chmod($this->response_dir, 0755);
		}
	}
	
	function setup_stories() {
		// Query for all the possible stories
		$query = $this->db->query("
			SELECT *
			FROM stories
		");
		
		// Store each possible story by incoming phone number
		$this->stories = array();
		foreach ($query->fetchAll() as $story) {
			$this->stories[$story->story_phone_number] = $story;
			if (!empty($_REQUEST['Called']) &&
			    $_REQUEST['Called'] == $story->story_phone_number) {
				$this->curr_story = $story;
			}
		}
	}
	
	function handle_request() {
		$this->log_request();
		if (!empty($_GET['get'])) {
			if ($_GET['get'] == 'stories') {
				$this->get_stories();
			} else if ($_GET['get'] == 'responses') {
				$this->get_responses();
			} else if ($_GET['get'] == 'mp3s') {
				$this->get_mp3s();
			}
		} else if (!empty($_GET['twilio'])) {
			$this->handle_call();
		} else {
			$this->show_help();
		}
	}
	
	function get_stories() {
		header('Content-type: application/json');
		echo json_encode(array(
			'stories' => $this->stories
		));
	}
	
	function get_responses() {
		header('Content-type: application/json');
		if (empty($_GET['story'])) {
			$json = array(
				'error' => 'Please specify a ‘story’ ID parameter.',
				'example' => 'yah.php?get=responses&story=1'
			);
		} else {
			$responses = $this->load_responses($_GET['story']);
			$json = array(
				'responses' => $responses
			);
		}
		echo json_encode($json);
	}
	
	function get_mp3s() {
		$mp3s = array();
		$select = $this->db->query("
			SELECT id, story_id, twilio_id, mp3_url
			FROM responses
			WHERE mp3_downloaded = 0
			ORDER BY created
		");
		$update = $this->db->prepare("
			UPDATE responses
			SET mp3_downloaded = ?
			WHERE id = ?
		");
		while ($response = $select->fetch()) {
			if ($this->download_mp3($response)) {
				$status = 1;
			} else {
				$status = 2; // Error downloading
			}
			$update->execute(array(
				$status,
				$response->id
			));
			$mp3s[] = array(
				'download_url' => $response->mp3_url,
				'filename' => $this->get_mp3_filename($response),
				'status' => $status
			);
		}
		header('Content-type: application/json');
		echo json_encode(array(
			'mp3s' => $mp3s
		));
	}
	
	function handle_call() {
		header('Content-type: text/xml');
		echo '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n";
		echo "<Response>\n";
		if ($_GET['twilio'] == 'save_response') {
			$this->save_response();
		} else if ($_GET['twilio'] == 'play_response' &&
		           isset($_GET['response_index'])) {
			$index = intval($_GET['response_index']);
			$this->play_response($index);
		} else {
			$this->record_response();
		}
		echo "</Response>\n";
	}
	
	function show_help() {
		header('Content-type: application/json');
		echo json_encode(array(
			'hello' => 'This is a self-documenting API.',
			'possible_queries' => array(
				'?get=stories' => 'Retrieve a list of known stories.',
				'?get=responses&story=[story ID]' => 'Retrieve a list of story responses.',
				'?get=mp3s' => 'Download any pending MP3s from Twilio (good to cron-job this one)',
				'?twilio=1' => 'Twilio POST endpoint'
			)
		));
	}
	
	function get_protocol() {
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			return 'https';
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
			return $_SERVER['HTTP_X_FORWARDED_PROTO'];
		} else {
			return 'http';
		}
	}
	
	function load_responses($story_id = null) {
		
		// Set story_id automatically
		if (empty($story_id)) {
			if (!empty($this->curr_story)) {
				$story_id = $this->curr_story->id;
			} else {
				return array();
			}
		}
		
		// Get existing responses from the database
		$query = $this->db->prepare("
			SELECT id, story_id, phone_number, twilio_id, duration, created
			FROM responses
			WHERE story_id = ?
			  AND mp3_downloaded = 1
			ORDER BY created
		");
		$query->execute(array(
			$story_id
		));
		
		$responses = array();
		foreach ($query->fetchAll() as $response) {
			$this->anonymize_phone_number($response);
			$this->set_mp3_url($response);
			$responses[] = $response;
		}
		
		return $responses;
	}
	
	function anonymize_phone_number($story) {
		// Note: this is currently a very US-centric format, assumes 10 digits
		if (preg_match('/\d{4}$/', $story->phone_number, $matches)) {
			$story->phone_number = 'xxx-xxx-' . $matches[0];
		} else {
			$story->phone_number = 'xxx-xxx-xxxx';
		}
	}
	
	function set_mp3_url($response) {
		$filename = $this->get_mp3_filename($response);
		$response->mp3_url = "$this->base_url/responses/$filename";
	}
	
	function log_request() {
		$vars = '';
		if (!empty($_REQUEST)) {
			$vars = trim(print_r($_REQUEST, true));
			$vars = "\n$vars";
		}
		error_log("{$_SERVER['REQUEST_METHOD']} {$this->request_url['path']}$vars");
	}
	
	function play_response($index = null) {
		if (!isset($this->responses)) {
			$this->responses = $this->load_responses();
		}
		if (!empty($this->responses[$index])) {
			$response = $this->responses[$index];
			$number = $index + 1;
			$this->say("Response number $number.");
			$filename = $this->get_mp3_filename($response);
			$this->play("$this->base_url/responses/$filename");
			if ($index < count($this->responses) - 1) {
				$this->redirect(array(
					'twilio' => 'play_response',
					'response_index' => ($index + 1)
				));
				return;
			}
		}
		$this->say('You have heard all of the responses. Goodbye!');
		$this->hangup();
	}
	
	function save_response() {
		if (!isset($this->responses)) {
			$this->responses = $this->load_responses();
		}
		$query = $this->db->prepare("
			INSERT INTO responses
			(story_id, phone_number, mp3_url, twilio_id, duration, created)
			VALUES (?, ?, ?, ?, ?, ?)
		");
		$query->execute(array(
			$this->curr_story->id,
			$_POST['Caller'],
			$_POST['RecordingUrl'] . '.mp3',
			$_POST['RecordingSid'],
			$_POST['RecordingDuration'],
			date('Y-m-d H:i:s')
		));
		if (!empty($this->responses)) {
			$this->say('Thank you for sharing your response.');
			$this->play_response(0);
		} else {
			$this->say('You are the first one to respond. Thanks for starting the conversation. Goodbye!');
			$this->hangup();
		}
	}
	
	function record_response() {
		$action = "$this->api_url?twilio=save_response";
		$this->say('Hello, please share your response with us. Press # when you finish.');
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
			$url .= '?' . implode('&amp;', $arg_pairs);
		} else {
			$url .= "?$args";
		}
		echo "
	  	<Redirect method=\"POST\">$url</Redirect>
		";
	}
	
	function download_mp3($response) {
		$curl = '/usr/bin/curl';
		$url = $response->mp3_url;
		$filename = $this->get_mp3_filename($response);
		exec("cd $this->responses_dir && $curl -o $filename $url");
		return file_exists("$this->responses_dir/$filename");
	}
	
	function get_mp3_filename($response) {
		return "$response->story_id-$response->id-$response->twilio_id.mp3";
	}
}

$yah = new YouAreHere();
$yah->handle_request();
