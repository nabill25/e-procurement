<?php
/*
Copyright (c) <2016> <http://www.sanwebe.com>
License : http://opensource.org/licenses/MIT
*/
class ImageResize {
	private $generate_image_file;
	private $generate_thumbnails;
	private $image_max_size;
	private $thumbnail_size;
	private $thumbnail_prefix;
	private $destination_dir;
	private $thumbnail_destination_dir;
	private $save_dir;
	private $quality;
	private $random_file_name;
	private $config;
	private $file_count;
	private $image_width;
	private $image_height;
	private $image_type;
	private $image_size_info;
	private $image_res;
	private $image_scale;
	private $new_width;
	private $new_height;
	private $new_canvas;
	private $new_file_name;
	private $curr_tmp_name;
	private $x_offset; 
	private $y_offset;
	private $resized_response;
	private $thumb_response;
	private $unique_rnd_name;
	public $response;
	
	function __construct($config) {	
			//set local vars
			$this->generate_image_file = $config["generate_image_file"];
			$this->generate_thumbnails = $config["generate_thumbnails"];
			$this->image_max_size = $config["image_max_size"];
			$this->thumbnail_size = $config["thumbnail_size"];
			$this->thumbnail_prefix = $config["thumbnail_prefix"];
			$this->destination_dir = $config["destination_folder"];
			$this->thumbnail_destination_dir = $config["thumbnail_destination_folder"];
			$this->random_file_name = $config["random_file_name"];
			$this->quality = $config["quality"];
			$this->file_data = $config["file_data"];
			$this->file_count = count($this->file_data['name']);
	}
	
	//resize function
	public function resize(){
		$this->save_image();
		return $this->response;
	}
	
	//save image to destination
	private function save_image(){
		if(!file_exists($this->save_dir)){ //try and create folder if none exist
			if(!mkdir($this->save_dir, 0755, true)){
				throw new Exception($this->save_dir . ' - directory doesn\'t exist!');
			}
		}
		
		switch($this->image_type){//determine mime type
			case 'image/png': 
				imagepng($this->new_canvas, $this->save_dir.$this->new_file_name); imagedestroy($this->new_canvas); return $this->new_file_name; 
				break;
			case 'image/gif': 
				imagegif($this->new_canvas, $this->save_dir.$this->new_file_name); imagedestroy($this->new_canvas); return $this->new_file_name; 
				break;          
			case 'image/jpeg': case 'image/pjpeg': 
				imagejpeg($this->new_canvas, $this->save_dir.$this->new_file_name, $this->quality); imagedestroy($this->new_canvas); return $this->new_file_name; 
				break;
			default: 
				imagedestroy($this->new_canvas);
				return false;
		}
	}
	

}