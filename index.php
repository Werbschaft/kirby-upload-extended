<?php

require_once("lib/Tinify/Exception.php");
require_once("lib/Tinify/ResultMeta.php");
require_once("lib/Tinify/Result.php");
require_once("lib/Tinify/Source.php");
require_once("lib/Tinify/Client.php");
require_once("lib/Tinify.php");

function uploadExtended($file) {
	
	$rename = option('werbschaft.uploadExtended.rename');
	$excludeCharacters = option('werbschaft.uploadExtended.excludeCharacters');
	$kirbyResize = option('werbschaft.uploadExtended.kirbyResize');
	$maxWidth = option('werbschaft.uploadExtended.maxWidth');
	$maxHeight = option('werbschaft.uploadExtended.maxHeight');
	$quality = option('werbschaft.uploadExtended.quality');
	$excludeTemplates = option('werbschaft.uploadExtended.excludeTemplates');
	$excludePages = option('werbschaft.uploadExtended.excludePages');
	$tinyPng = option('werbschaft.uploadExtended.tinyPng');
	$tinyPngKey = option('werbschaft.uploadExtended.tinyPngKey');
	$tinyPngResize = option('werbschaft.uploadExtended.tinyPngResize');
	$tinyPngResizeMethod = option('werbschaft.uploadExtended.tinyPngResizeMethod');
	$uploadLimit = option('werbschaft.uploadExtended.uploadLimit');
	$uploadLimitMegabyte = option('werbschaft.uploadExtended.uploadLimitMegabyte');
	$debug = option('werbschaft.uploadExtended.debug');
	
	$message = '';
	$excluded = false;

	if ( !empty($excludeTemplates) || !empty($excludePages) ) {
		
		$excluded = true;
		
		if( !empty($excludeTemplates) ) {
			
			$excluded .= in_array( $file->page()->intendedTemplate(), $excludeTemplates );
			
		}
		
		if (!empty($excludePages) ) {
			
			$excluded .= in_array( $file->page()->uid(), $excludePages );
			
		}
		
	}
	
	if ( $rename == true ) {
		
		$fileName = str_replace($excludeCharacters,'-',$file->name(),$count);
		
		if ( $count > 0 ) {
			
			$file = $file->changeName($fileName);
			$message .= ' The file was renamed according to the specifications. ';
			
		}
		
		
	}

	if ( !$excluded ) {
		
		if ( $file->isResizable() ) {
		
			// RESIZE THE IMAGE
			
			if ( $kirbyResize == true ) {
			
				if( $file->width() > $maxWidth || $file->height() > $maxHeight ) {
					
					if ( $file->width() > $maxWidth ) {
						$message .= ' The file is with ' . $file->width() . ' pixels wider than the allowed ' . $maxWidth . ' pixels and was reduced accordingly. ';
					}
					
					if ( $file->height() > $maxHeight ) {
						$message .= ' The file is with ' . $file->height() . ' pixels higher than the allowed ' . $maxHeight . ' pixels and was reduced accordingly. ';
					}
					
					try {
						
						kirby()->thumb($file->root(), $file->root(), [
							'width' => $maxWidth,
							'height' => $maxHeight,
							'quality' => $quality
						]);
						
					} catch (Exception $e) {
						
						$message .= $e->getMessage();
						
					}
				}
			
			}
	
			// CHECK FOR TINY PNG
			
			if ( $tinyPng == true ) {
				
				try {
					
					\Tinify\setKey($tinyPngKey);
					\Tinify\validate();
					$compressionsCount = \Tinify\compressionCount() + 1;
					$message .= ' There have already been ' . $compressionsCount . ' compressions made with the TinyPNG API key this month. 500 are free per Key. ';
					
				} catch(\Tinify\Exception $e) {
					
					$message .= ' The Tiny PNG Api Key is not correct. Disable Tiny PNG or enter a valid key. ';
					
				}
				
				// UPLOAD AND MINIFY
				
				$fileName = $file->name();
				
				if ( $file->page() ) {
					
					$path = $file->page()->root();
					
				} else {
					
					$path = site()->root();
					
				}
				
				try {
					
					$source = \Tinify\fromFile($file->root());
					
					// RESIZE THROUGH TINY PNG
					
					if ( $tinyPngResize == true ) {
						
						$source = $source->resize([
							"method" => $tinyPngResizeMethod,
							"width" => $maxWidth,
							"height" => $maxHeight
						]);
						
					}
					
					$source->toFile( $path . '/' . $fileName . '.' . $file->extension() );
					$message .= ' The file was optimized by TinyPNG. ';
		
				} catch(\Tinify\Exception $e) {
					
					$message .= $e->getMessage();
					
				}
				
			}
			
		} else {
			
			if ( $uploadLimit == true ) {
				
				$uploadLimitBytes = $uploadLimitMegabyte * 1048576;
				
				$fileSizeBytes = $file->size();
				$fileSizeNice = $file->niceSize();
					
				if ( $fileSizeBytes > $uploadLimitBytes ) {
				
					$file->delete();
				
					$message .= ' The file is with ' . $fileSizeNice . ' too big for the upload and was deleted because the limit for files is ' . $uploadLimitMegabyte . ' MB. ';
				
				}
				
			}

		}

	}
	
	if ( $debug == true ) {
		
		throw new Exception($message);
		
	}
	
}

Kirby::plugin('werbschaft/uploadExtended', [
	'options' => [
		'rename' => true,
		'excludeCharacters' => ['_','__','___','--','---'],
		'kirbyResize' => true,
		'maxWidth' => 2000,
		'maxHeight' => 2000,
		'quality' => 100,
		'debug' => false,
		'tinyPng' => true,
		'tinyPngKey' => "insert-here",
		'tinyPngResize' => false,
		'tinyPngResizeMethod' => 'thumb',
		'excludeTemplates' => [],
		'excludePages' => [],
		'uploadLimit' => true,
		'uploadLimitMegabyte' => 5
	],
	'hooks' => [
		'file.create:after' => function ($file) {
			uploadExtended($file);
		},
		'file.replace:after' => function ($newFile, $oldFile) {
			uploadExtended($newFile);
		}
	]
]);
