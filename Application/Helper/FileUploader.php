<?php 
/*
 * Copyright 2022 Rochdi Wafik 
 * Version 1.0
 */


/**
 *  - This Class Is Used To Handel And Upload Files
 *  - This Class Gives You A Very Useful Methods To Control Files
 *  - This Class Can Upload Files In Realtime 
 *    For Example : Upload Images While Using An Editor  
 */

/**
 * ==============================================================================
 * Image Manipulation
 * ==============================================================================
 * - PHP is a server side language and will work only once the image is uploaded.
 * - So we cant manipulate the image if not uploaded yet.
 * - We have first to upload the image, then we manipulate the uploaded image,
 * - Then we replace the old image with the new manipulated image.
 */
class FileUploader
{

	public $upload_dir;
	public $upload_url;
	public $file_location;
	public $absolute_file_path;
	/**/
	private $types_allowed = [];
	private $hosts_allowed = [];
	/**/
	private $max_size;
	private $new_name;
	private $unique_name;
	private $filename;
	/**/
	private $resize=[];
	private $crop=[];
	/**/
	private $msg_lang;
	private $message;
	private $uploadOk = 1;

	// New 16-11-2024
	public $image_quality;
	public $file;

    
	/**
	 * Message Language
	 * ---------------------------------------------------------------------
	 * Set which language to print action result
	 * Example: 
	 * english: 'file uploaded successfolly'
	 * arabic: 'تم رفع الملف بنجاح'
	 * @todo
	 * - Return status codes instead of direct strings
	 * - So that the user can print his own messages
	 */
	function msg_language($lang = 'english'){
		$lang = strtolower($lang);
		$this->msg_lang = $lang;

	}

	/**
	 * Set Directory Where To Upload
	 * ---------------------------------------------------------------------
	 */
	function set_dir($dir)
	{
		$this->upload_dir = rtrim(str_replace('/', DS,  $dir), DS).DS;
		$this->upload_url = rtrim(str_replace(DS, '/', $dir),'/').'/';

		if(!is_dir($this->upload_dir)):
			$this->message = 'Unknown directory: '.$dir;
			$this->uploadOk = 0;
			return false;
		endif;
		
	}

	/**
	 * Set Hosts Allowed
	 * ---------------------------------------------------------------------
	 * @todo add details
	 */
	function set_origins($hosts)
	{
		if(is_array($hosts)){
			$this->hosts_allowed = $hosts;
		}else{
			$this->hosts_allowed = array($hosts);
		}
	}

	/**
	 * Set Max Size
	 * ---------------------------------------------------------------------
	 * @todo add size unit, like: KB,MB 
	 * example SizeUnit::MB
	 */
	function set_max_size($sizeMB = 1)
	{
		if($sizeMB == null){
			$this->message = 'Undefined max size ';
			$this->uploadOk = 0;
			return false;
		}
		$this->max_size = $sizeMB*(1048000);
	}


	/**
	 * Allow Specific Extensiens
	 * ---------------------------------------------------------------------
	 */
	function set_types($types)
	{
		if(!is_array($types)):
			$this->message = 'extensiens must be in array';
			$this->uploadOk = 0;
			return false;
		endif;
		$this->types_allowed = $types;
	}
	

	/**
	 * Compress Image Size
	 * ---------------------------------------------------------------------
	 * @todo explain required param
	 */
	function set_image_quality($quality = null)
	{
		if($quality != null){
			if(is_int($quality)){
				$this->image_quality = $quality;
			}else{
				$this->message = 'Image quality must be integer [0-100]';
				$this->uploadOk = 0;
				return false;
			}
		}
	}

	/**
	 * Rename uploaded file
	 * ---------------------------------------------------------------------
	 * - Rename file before upload
	 * - This help make file name unique, avoiding duplicated files
	 */
	function rename($name = null){
		$this->new_name = $name;
	}

	/**
	 * Get Message
	 * ---------------------------------------------------------------------
	 * @todo add details, we may need to rename to get_last_message()
	 */
	function get_message()
	{
		return $this->message;
	}

	/**
	 * Get Filename
	 * ---------------------------------------------------------------------
	 */
	function get_file_name()
	{
		return $this->filename;
	}

	/**
	 * Get Absolute File Path
	 * -------------------------------------------------------------------
	 * @return string example: /www/app/uploads/file.jpg
	 */
	function get_absolute_filepath(){
		return $this->upload_dir.DS.$this->filename;
	}

	/**
	 * Get File Location (after upload)
	 * ---------------------------------------------------------------------
	 * @todo add details
	 */
	function get_location()
	{
		return $this->file_location;
	}

	/**
	 * Set Unique Name
	 * ---------------------------------------------------------------------
	 * @todo add details
	 */
	function set_unique_name(){
		$this->unique_name = uniqid(mt_rand(),true);
	}

	/**
	 * Resize image
	 * ---------------------------------------------------------------------
	 * @todo add details, we may rename to resizeImage()
	 */
	function resize($width, $height, $quality=90, $auto_crop=true)
	{
		$this->resize = [
			"width" => $width,
			"height" => $height,
			"quality" => $quality,
			"auto_crop" => $auto_crop
		];
	}

	/**
	 * Crop Image
	 * ---------------------------------------------------------------------
	 * @todo add details, we may rename to cropImage()
	 * @deprecated paused temporary
	 */
	function crop($left=0, $top=0, $width="100%", $height = "100%")
	{
		$this->crop = [
			"left" => $left,
			"top" => $top,
			"width" => $width,
			"height" => $height
		];
	}

	/**
	 * Start Uploading
	 * ---------------------------------------------------------------------
	 * @todo add process steps
	 */
	function upload($input = null)
	{
		/**
		 * Get File Input Name
		 * IF not set => get current file (Realtime) 
		 */
		
		if($input == null){
			reset($_FILES);
		    $file = current($_FILES);
		}
		else{
			$file = $_FILES[$input];
		}

		/**/
		$file_path = $this->upload_dir.basename($file['name']);
		$file_url  = $this->upload_url.basename($file['name']);

		/**/

		// Check hosts allowed
		if(!empty($this->hosts_allowed)):
			if(isset($_SERVER['HTTP_ORIGIN'])){
				if(!in_array($_SERVER['HTTP_ORIGIN'], $this->hosts_allowed)){
					if($this->msg_lang == 'arabic'):
						$this->message = 'قام المسؤول بحظر الرفع من هذا الخادم';
					else:
						$this->message = 'This host origin is denied';
					endif;
					$this->uploadOk = 0;			
					return false;
				}
			}
		endif;

		// Check If File Selected
		if(empty($file['name'])):
			if($this->msg_lang == 'arabic'):
				$this->message = 'لم يتم اختيار أي ملف';
			else:
			    $this->message = 'File not selected!';
			endif;
			$this->uploadOk = 0;
			return false;
		endif;

		// Check file size
		if($this->max_size != null):
			if($file['size'] > $this->max_size):
				if($this->msg_lang == 'arabic'):
					$this->message = 'حجم الملف لا يجب أن يتعدى '.($this->max_size/1048000).'mb';
				else:
				    $this->message = 'File Must Be Less Than '.($this->max_size/1048000).'MB';
				endif;
				$this->uploadOk = 0;			
				return false;
			endif;
		endif;

		// Check extensiens allowed
		if(!empty($this->types_allowed)):
			$ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
			if(!in_array($ext, $this->types_allowed)){
				if($this->msg_lang == 'arabic'){
					$this->message = 'الامتداد '.$ext.' غير مدعوم';
				}else{
					$this->message = 'Type '.$ext.' not suported';
				}
				$this->uploadOk = 0;			
				return false;
			}
		endif;


		/**
		 * Upload File 
		 */

		if($this->uploadOk == 1):
			if(!is_uploaded_file($file['tmp_name'])){
				if($this->msg_lang == 'arabic'){
					$this->message = 'تعذر رفع الملف !';
				}else{
					$this->message = 'Upload file failed';	
				}
				$this->uploadOk = 0;			
				return false;
			}
			/**/
			if(!@copy($file['tmp_name'], $file_path)){
				if(!move_uploaded_file($file['tmp_name'], $file_path)){
					if($this->msg_lang == 'arabic'){
						$this->message = 'تعذر رفع الملف !';
					}else{
						$this->message = 'Upload file failed';	
					}	
					$this->uploadOk = 0;				
					return false;
				}
			}

			// check re-name
			if($this->new_name != null):
				$f_name = basename($file['name']);
				$f_type = pathinfo($file['name'], PATHINFO_EXTENSION);
				if(rename($this->upload_dir.$f_name, $this->upload_dir.$this->new_name.'.'.$f_type)){
					$file_url  = $this->upload_url.$this->new_name.'.'.$f_type;
				}else{
					if($this->msg_lang == 'arabic'){
						$this->message = 'تعذر تغيير إسم الملف';
					}else{
						$this->message = 'Failed Rename The File';
					}
					$this->uploadOk = 0;			
					return false;
				}
			endif;

			// check unique name
			if($this->unique_name != null):
				$f_name = basename($file['name']);
				$f_type = pathinfo($file['name'], PATHINFO_EXTENSION);
				if(rename($this->upload_dir.$f_name, $this->upload_dir.$this->unique_name.'.'.$f_type)){
					$file_url  = $this->upload_dir.$this->unique_name.'.'.$f_type;
				}
			endif;
		endif;

		/**/

		// Image Manipulation: @todo get class WideImage
		if($this->uploadOk == 1):
			/**
			 * Should Resize File
			 */
			if($this->resize!=null && !empty($this->resize)){
				$width = $this->resize['width'];
				$height = $this->resize['height'];
				$quality = $this->resize['quality'];
				$original_filepath = $this->get_absolute_filepath();
				$resizer = new ImageResizer($file_url);
				if($this->resize['auto_crop']){
					$resizer->resizeAutoCrop($width, $height);
				}else{
					$resizer->resize($width, $height);
				}
				// Replace old image with new resized image
				$resizer->save($original_filepath, $quality);
			}
		endif;

		if($this->uploadOk == 1):
			$this->file_location = base_url($file_url);
			$this->filename = basename($this->file_location);
			return true;
		endif;

	}


	/**
	 * Is File Uploaded
	 * ---------------------------------------------------------------------
	 */
	function is_uploaded()
	{
		return $this->uploadOk == 1 ? true : false;
	}

	/**
	 * Delete Uploaded File
	 * ---------------------------------------------------------------------
	 *  @todo function unset(): bool{}
	 */
	function unset()
	{
		$filepath = $this->upload_dir.basename($this->file['name']);

		if(!is_file($filepath) || !file_exists($filepath)){
			return false;
		}
		try{
			return @unlink($this->upload_dir.basename($this->file['name'])) ? true : false;
		}catch(Exception $e){
			return false;
		}
	}
	/**
	 * Delete Uploaded File
	 * ---------------------------------------------------------------------
	 * Just an alias of unset()
	 */
	function delete()
	{
		return $this->unset();
	}

}