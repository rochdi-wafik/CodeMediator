<?php 
class MimeTypeUtils{
    
    /**
     * -------------------------------------------------------------------------
     * Get Image MimeTypes
     * -------------------------------------------------------------------------
     */
    static function get_image_mimeTypes():array{
        return [
            'image/bmp'      => 'bmp',
            'image/x-bmp'    => 'bmp',
            'image/x-bitmap'                            => 'bmp',
            'image/x-xbitmap'                           => 'bmp',
            'image/x-win-bitmap'                        => 'bmp',
            'image/x-windows-bmp'                       => 'bmp',
            'image/ms-bmp'                              => 'bmp',
            'image/x-ms-bmp'                            => 'bmp',
            'application/bmp'                           => 'bmp',
            'application/x-bmp'                         => 'bmp',
            'application/x-win-bitmap'                  => 'bmp',
            'image/jpx'      => 'jp2',
            'image/jpm'      => 'jp2',
            'image/jpeg'     => 'jpeg',
            'image/pjpeg'    => 'jpeg',
            'image/png'      => 'png',
            'image/x-png'    => 'png',
            'image/gif'      => 'gif',
            'image/x-icon'                              => 'ico',
            'image/x-ico'    => 'ico',
            'image/vnd.microsoft.icon'                  => 'ico',
            'image/jp2'      => 'jp2',
            'image/webp'     => 'webp',
            'image/svg+xml'                             => 'svg',
            'image/tiff'     => 'tiff',
        ];
    }

    /**
     * -------------------------------------------------------------------------
     * Get Video MimeTypes
     * -------------------------------------------------------------------------
     */
    static function get_video_mimeTypes():array{
        return [
            'video/3gpp2'    => '3g2',
            'video/3gp'      => '3gp',
            'video/3gpp'     => '3gp',
            'video/webm'     => 'webm',
            'video/x-ms-wmv'                            => 'wmv',
            'video/x-ms-asf'                            => 'wmv',
            'video/x-msvideo'                           => 'avi',
            'video/msvideo'                             => 'avi',
            'video/avi'      => 'avi',
            'application/x-troff-msvideo'               => 'avi',
            'video/x-f4v'    => 'f4v',
            'video/x-flv'    => 'flv',
            'video/mp4'      => 'mp4',
            'video/mpeg'     => 'mpeg',
            'video/ogg'      => 'ogg',
            'video/quicktime'                           => 'mov',
            'video/x-sgi-movie'                         => 'movie',
            'video/mj2'      => 'jp2',
            'application/videolan'                      => 'vlc',
        ];
    }

    /**
     * -------------------------------------------------------------------------
     * Get Audio MimeTypes
     * -------------------------------------------------------------------------
     */
    static function get_audio_mimeTypes():array{
        return [
            'audio/x-acc'    => 'aac',
            'audio/ac3'      => 'ac3',
            'application/postscript'                    => 'ai',
            'audio/x-aiff'                              => 'aif',
            'audio/aiff'     => 'aif',
            'audio/x-au'     => 'au',
            'audio/x-wav'    => 'wav',
            'audio/wave'     => 'wav',
            'audio/wav'      => 'wav',
            'audio/x-flac'                              => 'flac',
            'audio/x-m4a'    => 'm4a',                 
            'audio/mp4'      => 'm4a',                 
            'application/vnd.mpegurl'                   => 'm4u',
            'audio/mpeg'     => 'mp3',
            'audio/mpg'      => 'mp3',
            'audio/mpeg3'    => 'mp3',
            'audio/mp3'      => 'mp3',
            'audio/ogg'      => 'ogg',
        ];
    }

    /**
     * -------------------------------------------------------------------------
     * Get Font MimeTypes
     * -------------------------------------------------------------------------
     */
    static function get_font_mimeTypes():array{
        return [
            'font/woff'      => 'woff',
            'font/woff2'     => 'woff2',
            'font/ttf'       => 'ttf',
            'font/otf'       => 'otf',
            'font/eot'       => 'eot',
            'font/svg'       => 'svg',

        ];
    }

    /**
     * -------------------------------------------------------------------------
     * Get Archive MimeTypes
     * -------------------------------------------------------------------------
     */
    static function get_archive_mimeTypes():array{
        return [
            // Archives
            'application/x-compressed'                  => '7zip',
            'application/x-zip'                         => 'zip',
            'application/zip'                           => 'zip',
            'application/x-zip-compressed'              => 'zip',
            'application/s-compressed'                  => 'zip',
            'multipart/x-zip'                           => 'zip',
            'application/x-rar'                         => 'rar',
            'application/rar'                           => 'rar',
            'application/x-rar-compressed'              => 'rar',
            'application/x-tar'                         => 'tar',
            'application/x-gtar'                        => 'gtar',
            'application/x-gzip'                        => 'gzip',
            'application/x-gzip-compressed'             => 'tgz',
        ];
    }


    /**
     * ------------------------------------------------------------------------
     * Is Image
     * ------------------------------------------------------------------------
     * @param filename absolute file name. i.e /.../foo.png
     * @param by_mimeType if true, use mimeType. false: use file extension
     */
    static function is_image($filename, $use_mimeType=true): bool{
        // Check by mimeType
        if($use_mimeType){
            $file_mimeType = mime_content_type($filename);
            $image_mimeTypes = array_map('strtolower',  array_keys(self::get_image_mimeTypes()));
            return in_array(strtolower($file_mimeType),  $image_mimeTypes);
        }
        // Check by extension
        $file_extension = pathinfo(basename($filename), PATHINFO_EXTENSION);
        $image_extensions = array_map('strtolower',  array_values(self::get_image_mimeTypes()));
        return in_array(strtolower($file_extension),  $image_extensions);
    }

    /**
     * ------------------------------------------------------------------------
     * Is Audio
     * ------------------------------------------------------------------------
     * @param filename absolute file name. i.e /.../foo.mp3
     * @param by_mimeType if true, use mimeType. false: use file extension
     */
    static function is_audio($filename, $use_mimeType=true): bool{
        // Check by mimeType
        if($use_mimeType){
            $file_mimeType = mime_content_type($filename);
            $audio_mimeTypes = array_map('strtolower',  array_keys(self::get_audio_mimeTypes()));
            return in_array(strtolower($file_mimeType),  $audio_mimeTypes);
        }
        // Check by extension
        $file_extension = pathinfo(basename($filename), PATHINFO_EXTENSION);
        $audio_extensions = array_map('strtolower',  array_values(self::get_audio_mimeTypes()));
        return in_array(strtolower($file_extension),  $audio_extensions);
    }

    /**
     * ------------------------------------------------------------------------
     * Is Video
     * ------------------------------------------------------------------------
     * @param filename absolute file name. i.e /.../foo.mp4
     * @param by_mimeType if true, use mimeType. false: use file extension
     */
    static function is_video($filename, $use_mimeType=true): bool{
        // Check by mimeType
        if($use_mimeType){
            $file_mimeType = mime_content_type($filename);
            $video_mimeTypes = array_map('strtolower',  array_keys(self::get_video_mimeTypes()));
            return in_array(strtolower($file_mimeType),  $video_mimeTypes);
        }
        // Check by extension
        $file_extension = pathinfo(basename($filename), PATHINFO_EXTENSION);
        $video_extensions = array_map('strtolower',  array_values(self::get_video_mimeTypes()));
        return in_array(strtolower($file_extension),  $video_extensions);
    }



    /**
     * ------------------------------------------------------------------------
     * Is Archive
     * ------------------------------------------------------------------------
     * @param filename absolute file name. i.e /.../foo.zip
     * @param by_mimeType if true, use mimeType. false: use file extension
     */
    static function is_archive($filename, $use_mimeType=true): bool{
        // Check by mimeType
        if($use_mimeType){
            $file_mimeType = mime_content_type($filename);
            $archive_mimeTypes = array_map('strtolower',  array_keys(self::get_archive_mimeTypes()));
            return in_array(strtolower($file_mimeType),  $archive_mimeTypes);
        }
        // Check by extension
        $file_extension = pathinfo(basename($filename), PATHINFO_EXTENSION);
        $archive_extensions = array_map('strtolower',  array_values(self::get_archive_mimeTypes()));
        return in_array(strtolower($file_extension),  $archive_extensions);
    }

    /**
     * ------------------------------------------------------------------------
     * Is Font
     * ------------------------------------------------------------------------
     * @param filename absolute file name. i.e /.../foo.ttf
     * @param by_mimeType if true, use mimeType. false: use file extension
     */
    static function is_font($filename, $use_mimeType=true): bool{
        // Check by mimeType
        if($use_mimeType){
            $file_mimeType = mime_content_type($filename);
            $font_mimeTypes = array_map('strtolower',  array_keys(self::get_font_mimeTypes()));
            return in_array(strtolower($file_mimeType),  $font_mimeTypes);
        }
        // Check by extension
        $file_extension = pathinfo(basename($filename), PATHINFO_EXTENSION);
        $font_extensions = array_map('strtolower',  array_values(self::get_font_mimeTypes()));
        return in_array(strtolower($file_extension),  $font_extensions);
    }
}