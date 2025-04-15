<?php
class ImageResizer{
    private $image_width;
    private $image_height;
    private $image_info;
    private $ratio;
    private $file;
    private $output_image;
    // Set the desired maximum width and height
    public $max_width = 200; // Adjust as needed
    public $max_height = 200; // Adjust as needed
    // Catch errors
    public $lastError=null;



    /**
     * ==========================================================================
     * Constructor
     * ==========================================================================
     * @param file using $_FILES['image']['tmp_name']; 
     * - Replace 'image' with the actual input field name
     */
    public function __construct($file) {
        $this->file = $file;
        $this->image_info  = getimagesize($file);
        $this->image_width =  $this->image_info[0];
        $this->image_height = $this->image_info[1];

        // Calculate the aspect ratio
        $this->ratio = $this->image_width / $this->image_height;
    }


    /**
     * ==========================================================================
     * Resize
     * ==========================================================================
     */
    public function resize(int $width, int $height): ?ImageResizer{
        $this->max_width = $width;
        $this->max_height = $height;

        // Calculate the new width and height based on the aspect ratio and maximum dimensions
        if ($this->image_width > $this->max_width || $this->image_height > $this->max_height) {
            if ($this->ratio > 1) { // Image is wider than tall
                $new_width = $this->max_width;
                $new_height = $this->max_width / $this->ratio;
            } else { // Image is taller than wide
                $new_width = $this->max_height * $this->ratio;
                $new_height = $this->max_height;
            }
        } else {
            $new_width = $this->image_width;
            $new_height = $this->image_height;
        }

        // Create a new image resource
        switch ($this->image_info['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($this->file);
                break;
            case 'image/png':
                $source = imagecreatefrompng($this->file);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($this->file);
                break;
            default:
                $this->lastError = "Invalid image type";
                return null;
        }

        // Create a new image resource with the new dimensions
        $destination = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);

        $this->output_image = $destination;
        // Free memory of the original image
        imagedestroy($source);
        return $this;
    }

    /**
     * ==========================================================================
     * Resize With Auto Crop
     * ==========================================================================
     */
    public function resizeAutoCrop(int $max_width, int $max_height): ?ImageResizer{

        // Calculate aspect ratios
        $desired_ratio = $max_width / $max_height;

        // Determine which dimension to base the scaling on
        if ($this->ratio > $desired_ratio) { 
            // Image is wider than the desired aspect ratio
            $temp_width = $max_height * $this->ratio; 
            $temp_height = $max_height;
        } else { 
            // Image is taller than the desired aspect ratio
            $temp_width = $max_width;
            $temp_height = $max_width / $this->ratio;
        }

        // Create image resource
        switch ($this->image_info['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($this->file);
                break;
            case 'image/png':
                $source = imagecreatefrompng($this->file);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($this->file);
                break;
            default:
                $this->lastError = "Invalid image type";
                return null; 
        }

        // Create a temporary image with the scaled dimensions
        $temp_image = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled($temp_image, $source, 0, 0, 0, 0, $temp_width, $temp_height, $this->image_width, $this->image_height);

        // Calculate the x and y coordinates for cropping
        $x = ($temp_width - $max_width) / 2;
        $y = ($temp_height - $max_height) / 2;

        // Create the final image with the desired dimensions
        $destination = imagecreatetruecolor($max_width, $max_height);
        imagecopy($destination, $temp_image, 0, 0, $x, $y, $max_width, $max_height);

        $this->output_image = $destination;
        imagedestroy($temp_image);

        return $this;

    }

    /**
     * ==========================================================================
     * Save To Path
     * ==========================================================================
     * @param dir_or_file_path if is dir, image will be saved in that dir
     *                         if is file, it will be replaced by this image
     */
    public function save(string $dir_or_file_path, int $quality=90){
        if(!is_dir($dir_or_file_path) && !is_file($dir_or_file_path)){
            $this->lastError = "Invalid directory or file path";
            return false;
        }

                
        // Save the resized image
        $output_path= null;
        if(is_dir($dir_or_file_path)){
            $output_path = $dir_or_file_path.DS.basename($this->file);
        }
        else{
            $output_path = $dir_or_file_path;
        }
        $is_success = imagejpeg($this->output_image, $output_path, $quality); // Save as JPEG with 90% quality
        if(!$is_success){
            $this->lastError = "Unable to save the output image";
            return false;
        }
        return true;
    }
}