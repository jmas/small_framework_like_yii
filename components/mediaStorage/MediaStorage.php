<?php

/**
 * Host images to www.imageshack.us
 * @author Maslakov Alexander (jmas.ukraine@gmail.com)
 */
abstract class MediaStorage
{
	public function get
}


function host_image( $image_filename )
{
	if( !file_exists($image_filename) )
		throw new Exception('Image file not found!');
	
	// Image hosting server settings
	$host    = 'www.imageshack.us';
	$api_key = 'OXVYLZQAe0745e5517833aeb9cdac21340bb1ef5';
	$path    = '/upload_api.php?optsize=0&key=' . $api_key;
	$referer = 'http://' . $host;

	// Upload files array
	$file_array = array(
		'fileupload' => $image_filename
	);
	
	// Boundary string
	$boundary = '--' . uniqid();
	
	// Build data for uploading
	$data = '--' . $boundary;
	
	foreach( $file_array as $name => $filename )
	{
		$content_file = join('', file($filename));

		$data .= "\r\n" .
				'Content-Disposition: form-data; name="'. $name .'"; filename="'. $filename .'"' . "\r\n" .
				'Content-Type: image/gif' . "\r\n\r\n" .

				$content_file . "\r\n" .
				'--' . $boundary;
	}
	
	$data .= '--';
	
	// Send data
    if( $fp = fsockopen($host, 80) )
	{
		/*
		$request = "POST $path HTTP/1.1\r\n"
				 . "Host: $host\r\n"
				 . "Referer: $referer\r\n"
				 . "Content-Type: multipart/form-data; boundary=". $boundary ."\r\n"
				 . "Connection: close\r\n\r\n"
				 . $data;
		
		fputs($fp, $request);
		*/
		
	    fputs($fp, "POST $path HTTP/1.1\r\n");
	    fputs($fp, "Host: $host\r\n");
	    fputs($fp, "Referer: $referer\r\n");
	    fputs($fp, "Content-Type: multipart/form-data; boundary=". $boundary ."\r\n");
	    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
	    fputs($fp, "Connection: close\r\n\r\n");
	    fputs($fp, $data);
		
		// Get result from server
	    $result = '';
		
	    while(!feof($fp))
	        $result .= fgets($fp, 128);
		
	    fclose($fp);
		
		// Parse result
	    $result = explode("\r\n\r\n", $result, 2);
		
	    $header = isset($result[0]) ? $result[0] : '';
	    $content = isset($result[1]) ? $result[1] : '';
		
		// If result valed - return image url
	    if( preg_match('/<image_link>(.*?)<\/image_link>/', $content, $m) ) {
			return $m[1];
		} else {
			return false;
		}
	}
	else
	{
		return false;
	}
}

?>
