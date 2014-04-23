<?php
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

include_once('jobs-feed.php');

$parser = new Parser('http://jobs.tech.co/jobroll/v1');

$jobs = $parser->getJobs(intval($_GET['limit']), (isset($_GET['location']) ? $_GET['location'] : false), (isset($_GET['affiliate']) ? true : false));

$feed = new Feed();

$channel = new Channel();
$channel
    ->title("Tech Cocktail Jobs Feed")
    ->description("Tech Cocktail Jobs Feed")
    ->url('http://jobs.tech.co/')
    ->appendTo($feed);
if(!empty($jobs)) {
	foreach($jobs as $job) {
		
		// RSS item
		$item = new Item();
		$item
		    ->title($job->title.' ('.$job->company.') - '.$job->location)
		    ->description($job->title.' ('.$job->company.') - '.$job->location)
		    ->url($job->url)
		    ->appendTo($channel);
	}
	echo $feed;
}

/*
http://........../example.php?limit=10
http://........../example.php?limit=10&location=Las Vegas, NV
http://........../example.php?limit=10&affiliate=anything
http://........../example.php?limit=10&affiliate=anything&location=Las Vegas, NV
*/