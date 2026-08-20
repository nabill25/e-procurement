<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
	function downloadBase64($filename, $filepath, $base64_encoded_file_data) {
	    // Prevents run-out-of-memory issue
	    if (ob_get_level()) {
	        ob_end_clean();
	    }

		// Decodes encoded data
		$decoded_file_data = base64_decode($base64_encoded_file_data);

		// Writes data to the specified file
		file_put_contents($filepath, $decoded_file_data);

		header('Expires: 0');
		header('Pragma: public');
		header('Cache-Control: must-revalidate');
		header('Content-Length: ' . filesize($filepath));
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		readfile($filepath);
	    
	    // Deletes the temp file
		if (file_exists($filepath)) {
			unlink($filepath);	
		}
	}
?>
