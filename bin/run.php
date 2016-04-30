<?php
use Agares\BlogGenerator\PostGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

	if($argc != 3) {
		echo 'Invalid arguments.' . PHP_EOL;
		echo 'Usage: ' . $argv[0] . ' [path_to_posts] [output_path]' . PHP_EOL;
		
		exit;
	}

	$postsDirectory = realpath($argv[1]);

	if($postsDirectory === false) {
		echo 'Invalid path to posts.' . PHP_EOL;
		
		exit;
	}

	$globIterator = new \GlobIterator($postsDirectory . DIRECTORY_SEPARATOR . '*.md');
	$postGenerator = new PostGenerator();
	foreach($globIterator as $post) {
		$postGenerator->generatePost($post, $argv[2]);
	}