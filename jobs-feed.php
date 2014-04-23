<?php
include_once('vendor/autoload.php');

class Job {
	
	public $url;
	public $title;
	public $company;
	public $location;
	public $affiliate = false;
	
	public function __construct($url, $title, $company, $location) {
		$this->url = $url;
		$this->title = $title;
		$this->company = $company;
		$this->location = $location;
		
		if(strpos($this->url, '/job/view?') > 0) {
			$this->affiliate = true;
		}
	}
}

class Parser {
	
	public $url;
	
	public function __construct($url) {
		$this->url = $url;
	}
	
	public function getJobs($limit = 5, $location = false, $include_affalate = true) {
		$url = $this->url.'?l='.($location ? urlencode($location) : null).'&limit='.$limit;
		
		try {
			
			$curl = new Curl($url);
			
			// If something happened, (which it shouldn't) throw an exception
			if (!$curl) {
				throw new Exception("I couldn't create a Curl object. Was PHP compiled with cURL?");
			}
			
			$curl->httpheader = array('X-OOCurl-Version: ' . Curl::VERSION);
			
			$response = $curl->exec();
			
			if (!$response) {
				throw new Exception("I couldn't fetch the response.");
			}
			
			if(!preg_match_all('/<a target="_top" href="(.*'.($include_affalate ? '\/job\/' : '\/job\/[^view\?]').'.*)" style="\'\+lnk\+\'">(.*)<\/a><br>(.*)<\/td>/', $response, $matches)) {
				throw new Exception("No jobs were found. Is the url configured properly?");
			}
			
			//All matches are in $matches, [1] is url, [2] is job title, [3] is company - location
			$jobs = array();
			
			for($i=0; $i<count($matches[1]); $i++) {
				$split = explode(' - ', $matches[3][$i]);
				$jobs[] = new Job($matches[1][$i], $matches[2][$i], $split[0], $split[1]);
			}
			
			return $jobs;
			
		} catch (Exception $e) {
			// There was a problem! What happened?
			printf("Oh Noes!\n");
			printf("%s\n", $e->getMessage());
			if ( $curl )
				printf( "cURL error: %s\n", $curl->errno());
		}
		
	}
}