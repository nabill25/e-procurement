<?php
############ Configuration ##############
$config["generate_image_file"]			= true;
$config["generate_thumbnails"]			= true;
$config["image_max_size"] 				= 500; //Maximum image size (height and width)
$config["thumbnail_size"]  				= 200; //Thumbnails will be cropped to 200x200 pixels
$config["thumbnail_prefix"]				= "thumb_"; //Normal thumb Prefix
$config["destination_folder"]			= 'uploads/'; //upload directory ends with / (slash)
$config["thumbnail_destination_folder"]	= 'uploads/'; //upload directory ends with / (slash)
$config["upload_url"] 					= "http://localhost/coba/ajax-upload-with-progressbar/uploads/"; 
$config["quality"] 						= 90; //jpeg quality
$config["random_file_name"]				= true; //randomize each file name


if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	exit;  //try detect AJAX request, simply exist if no Ajax
}


//specify uploaded file variable
$config["file_data"] = $_FILES["__files"]; 


if(move_uploaded_file($_FILES["__files"]["tmp_name"][0], "uploads/" . $_FILES["__files"]["name"][0]))
{}

		
?>