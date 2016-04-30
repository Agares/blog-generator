<?php

namespace Agares\BlogGenerator;

use Cocur\Slugify\Slugify;
use League\CommonMark\CommonMarkConverter;

class PostGenerator
{
	public function __construct()
	{
	}

	public function generatePost(\SplFileInfo $post, $outputPath)
	{
		$metadataFile = $this->openMetadataFile($post);
		$metadata = json_decode($metadataFile->fread($metadataFile->getSize()), true);
		
		$slugify = new Slugify();
		$slug = $slugify->slugify($metadata['title']);
		
		$filename = $metadata['published'] . '-' . $metadata['version'] . '-' . $slug . '.json';
		$postFile = $post->openFile();
		
		$commonmark = new CommonMarkConverter();
		$postMarkdown = '# ' . $metadata['title'] . PHP_EOL
			. $postFile->fread($postFile->getSize());
		$postContents = $commonmark->convertToHtml($postMarkdown);
		
		$finalPost = json_encode([
			'title' => $metadata['title'],
			'slug' => $slug,
			'published' => $metadata['published'],
			'version' => $metadata['version'],
			'html' => $postContents
		]);
		
		file_put_contents($outputPath . DIRECTORY_SEPARATOR . $filename, $finalPost);
	}

	private function openMetadataFile(\SplFileInfo $post) : \SplFileObject
	{
		$metadataFileInfo = new \SplFileInfo($post->getPathname() . '.meta.json');

		if (!$metadataFileInfo->isFile()) {
			throw new \InvalidArgumentException(sprintf('Metadata file %s for %s does not exist.', $metadataFileInfo->getFilename(), $post->getFilename()));
		}
		
		if(!$metadataFileInfo->isReadable()) {
			throw new \InvalidArgumentException(sprintf('Metadata file %s is not readable.', $metadataFileInfo->getFilename()));
		}

		return $metadataFileInfo->openFile();
	}
}