<?php
/**
 * This is based on the code of S. Mohammed Alsharaf, http://www.zfsnippets.com/snippets/view/id/44. 
 * 
 * I still don't like this code, far too many properties, most of which are being directly accessed, 
 * a hardwired path for thumbnails, and a number of other annoyances. 
 * 
 * For this version of the Image view helper, I reworked it to support an array of configs that you pick from 
 * via parameter. You could easily pull the contents of the array from application.ini if you want. 
 * 
 * It also eliminates the need for paths in the configuration, and should work correctly with your Zend app 
 * being run in sub directories. 
 * 
 * I tried to clean up the code so the various paths to things are all set in the one location, which should 
 * make altering it for future development easier.
 * 
 * Basic usage is as follows: 
 * 
 * <?= $this->image('id_name', '/path/to/image.jpg', array('alt' => 'image alt'), 'preset_name'); ?>
 * 
 * 
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author     Cameron Germein (cameron@dhmedia.com.au)
 * @version        1.1
 */
 
class Zend_View_Helper_Image extends Zend_View_Helper_Abstract {
        protected $_name;
        protected $_thumb_config; 
        protected $_filesystem_path;
        protected $_img_src;
        protected $_thumb_src;
        protected $_full_image_path;
        protected $_file_name;
        protected $_thumb_file_name;
        protected $_image_dir_path;
        protected $_thumb_dir_path;
        protected $_thumbnail; //instance of Image;
        protected $_valid_mime = array ('image/png', 'image/jpeg', 'image/jpg', 'image/gif' );
        
        public function image($name, $image_path = null, $attribs = array(), $config = 'none') {
                //get details about the thumbnail to generate. 
                $this->_thumb_config = $this->_getConfig($config);
                
                //set path
                $this->_setPaths($image_path);
                
                //check that the image exists. 
                if (!is_file($this->_filesystem_path . '/' . $this->_full_image_path)) {
                        return false;
                }
                
                //check the image is valid
                $this->_checkImage(); 
                
                //set name
                $this->_name = $this->view->escape ( $name );
                
                //set attributes
                $this->_setAttributes ( $attribs );
                
                //generate thumbnail
                $this->_generateThumbnail();
                
                return $this->_render ();
        }
        
        protected function _setPaths($path) {
                $this->_filesystem_path = $_SERVER['DOCUMENT_ROOT'] . $this->view->baseUrl();
                $this->_full_image_path = $path;
                
                $parts = pathinfo($path);
                $this->_file_name = $parts['basename'];
                $this->_image_dir_path = $parts['dirname'];
                $this->_thumb_dir_path = $parts['dirname'] . '/thumbs';
                $this->_thumb_file_name = $this->_thumb_config['width'] . 'x' . $this->_thumb_config['height'] . '_' . $this->_file_name;
 
                $this->_img_src = $this->view->baseUrl () . $this->_image_dir_path . '/' . $this->_file_name;
                $this->_thumb_src = $this->view->baseUrl () . $this->_thumb_dir_path . '/' . $this->_thumb_file_name;
        }
        
        protected function _checkImage() {
                if (!$img_info = getimagesize($this->_filesystem_path . '/' . $this->_full_image_path)) {
                        //throw new Exception('Image is invalid!');
                        return false;
                }
                
                if (!in_array ($img_info['mime'], $this->_valid_mime)) {
                        throw new Exception('Image has invalid mime type!');
                }
        }
        
        
        
        protected function _setAttributes($attribs) {
                $alt = '';
                $class = '';
                $map = '';
                $class = '';
                if (isset ( $attribs ['alt'] )) {
                        $alt = 'alt="' . $this->view->escape ( $attribs ['alt'] ) . '" ';
                }
                
                if (isset ( $attribs ['title'] )) {
                        $title = 'title="' . $this->view->escape ( $attribs ['title'] ) . '" ';
                } else {
                        $title = 'title="' . $this->view->escape ( $attribs ['alt'] ) . '" ';
                }
                
                if (isset ( $attribs ['map'] )) {
                        $map = 'usemap="#' . $this->view->escape ( $attribs ['map'] ) . '" ';
                }
                
                if (isset ( $attribs ['class'] )) {
                        $class = 'class="' . $this->view->escape ( $attribs ['class'] ) . '" ';
                }
                $this->_attribs = $alt . $title . $map . $class;
        }
        
        protected function _generateThumbnail() {
                $full_thumb_path = $this->_filesystem_path . '/' . $this->_thumb_dir_path . '/' . $this->_thumb_file_name;
                
                //make sure the thumbnail directory exists. 
                if (!file_exists($this->_filesystem_path . '/' . $this->_thumb_dir_path)) { 
                        if (!mkdir($this->_filesystem_path . '/' . $this->_thumb_dir_path)) {
                                throw new Exception ('Cannot create thumbnail directory!');
                        };
                }
                
                //if the thumbnail already exists, don't recreate it. 
                if (file_exists ( $full_thumb_path )) {
                        $image = new Image();
                        $image->open($full_thumb_path);
                        $this->_thumbnail = $image;
                        return true;
                }
                
                // resize image
                $image = new Image();
                $image->open($this->_filesystem_path . $this->_full_image_path)
                          ->resize($this->_thumb_config['width'], $this->_thumb_config['height'] )
                          ->save($full_thumb_path, $this->_thumb_config['quality'] );
                $this->_thumbnail = $image;
                return true;
        }
        
        protected function _render() {
                $xhtml = '<a class="imgHlper" rel="imgHlper" href="' . $this->_img_src . '">
                                        <img width="' . $this->_thumbnail->getWidth() . '" height="' . $this->_thumbnail->getHeight() . '" src="' . $this->_thumb_src . '" ' . $this->_attribs . ' id="' . $this->_name . '"';
                
                $endTag = ' />';
                if (($this->view instanceof Zend_View_Abstract) && ! $this->view->doctype ()->isXhtml ()) {
                        $endTag = '>';
                }
                $xhtml .= $endTag . "</a>";
                return $xhtml;
        }
        
        protected function _getConfig($config) {
                $configs = array (
                        '300x300' => array (
                                'width' => 300, 
                                'height' => 300, 
                                'quality' => 100 
                        ),
                        '100x100' => array (
                                'width' => 100, 
                                'height' => 100, 
                                'quality' => 100 
                        ),
                        '54x89' => array (
                                'width' => 89, 
                                'height' => 54, 
                                'quality' => 100 
                        ),
                        '450x285' => array (
                                'width' => 450, 
                                'height' => 285, 
                                'quality' => 100 
                        ),
                        '320x240' => array (
                                'width' => 320, 
                                'height' => 240, 
                                'quality' => 100 
                        ),
                        'widescreen' => array (
                                'width' => 400, 
                                'height' => 200, 
                                'quality' => 100 
                        ),
                );
                if (!array_key_exists($config, $configs)) {
                        throw new Exception('Config does not exist!');
                }
                return $configs[$config];
        }
 
}
/**
 * This is based on the code of S. Mohammed Alsharaf, http://www.zfsnippets.com/snippets/view/id/44.
 * 
 * This class remains largely unchanged, although I used a much abbreviated piece of code for maintaining the aspect ratio. 
 * 
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author     Cameron Germein (cameron@dhmedia.com.au)
 * @version        1.0
 */
class Image {
        protected $_filename = '';
        protected $_image = '';
        protected $_width = '';
        protected $_height = '';
        protected $_mime_type = '';
        protected $_view = null;
        const IMAGETYPE_GIF = 'image/gif';
        const IMAGETYPE_JPEG = 'image/jpeg';
        const IMAGETYPE_PNG = 'image/png';
        const IMAGETYPE_JPG = 'image/jpg';
        
        public function setView($view) {
                $this->_view = $view;
                return $this;
        }
        
        public function open($filename) {
                $this->_filename = $filename;
                $this->_setInfo();
                
                switch($this->_mime_type) {
                        case self::IMAGETYPE_GIF :
                                $this->_image = imagecreatefromgif($this->_filename);
                                break;
                        case self::IMAGETYPE_JPEG :
                        case self::IMAGETYPE_JPG :
                                $this->_image = imagecreatefromjpeg($this->_filename);
                                break;
                        case self::IMAGETYPE_PNG :
                                $this->_image = imagecreatefrompng($this->_filename);
                                break;
                        default :
                                throw new Exception('Image extension is invalid or not supported.');
                                break;
                }
                return $this;
        }
        
        protected function _output($save_in = null, $quality, $filters = null) {
                switch ($this->_mime_type) {
                        case self::IMAGETYPE_GIF :
                                return imagegif ( $this->_image, $save_in );
                                break;
                        case self::IMAGETYPE_JPEG :
                        case self::IMAGETYPE_JPG :
                                $quality = is_null ( $quality ) ? 75 : $quality;
                                return imagejpeg ( $this->_image, $save_in, $quality );
                                break;
                        case self::IMAGETYPE_PNG :
                                $quality = is_null ( $quality ) ? 0 : $quality;
                                $filters = is_null ( $filters ) ? null : $filters;
                                return imagepng ( $this->_image, $save_in, $quality, $filters );
                                break;
                        default :
                                throw new Exception('Image cannot be created.');
                                break;
                }
        }
        
        public function display($quality = null, $filters = null) {
                if ($this->_view instanceof Zend_View) {
                        $this->_view->getResponse ()->setHeader ( 'Content-Type', $this->_mime_type );
                } else {
                        header ( 'Content-Type', $this->_mime_type );
                }
                return $this->_output ( null, $quality, $filters );
        }
        
        public function save($save_in = null, $quality = null, $filters = null) {
                return $this->_output ( $save_in, $quality, $filters );
        }
        
        public function __destruct() {
                @imagedestroy ( $this->_image );
        }
        
        protected function _setInfo() {
                $img_size = @getimagesize ( $this->_filename );
                if (!$img_size) {
                        throw new Exception ( 'Could not extract image size.' );
                } elseif ($img_size[0] == 0 || $img_size[1] == 0) {
                        throw new Exception ( 'Image has dimension of zero.' );
                }
                $this->_width = $img_size[0];
                $this->_height = $img_size[1];
                $this->_mime_type = $img_size['mime'];
        
        }
        
        public function getWidth() {
                return $this->_width;
        }
        
        public function getHeight() {
                return $this->_height;
        }
        
        protected function _refreshDimensions() {
                $this->_height = imagesy ( $this->_image );
                $this->_width = imagesx ( $this->_image );
        }
        
        /**
         * If image is GIF or PNG keep transparent colors
         * 
         * @credit http://github.com/maxim/smart_resize_image/tree/master
         * @param $image src of the image
         * @return the modified image
         */
        protected function _handleTransparentColor($image = null) {
                $image = is_null ( $image ) ? $this->_image : $image;
                
                if (($this->_mime_type == self::IMAGETYPE_GIF) || ($this->_mime_type == self::IMAGETYPE_PNG)) {
                        $trnprt_indx = imagecolortransparent ( $this->_image );
                        
                        // If we have a specific transparent color
                        if ($trnprt_indx >= 0) {
                                // Get the original image's transparent color's RGB values
                                $trnprt_color = imagecolorsforindex ( $this->_image, $trnprt_indx );
                                
                                // Allocate the same color in the new image resource
                                $trnprt_indx = imagecolorallocate ( $image, $trnprt_color ['red'], $trnprt_color ['green'], $trnprt_color ['blue'] );
                                
                                // Completely fill the background of the new image with allocated color.
                                imagefill ( $image, 0, 0, $trnprt_indx );
                                
                                // Set the background color for new image to transparent
                                imagecolortransparent ( $image, $trnprt_indx );
                        } elseif ($this->_mime_type == self::IMAGETYPE_PNG) {
                                // Always make a transparent background color for PNGs that don't have one allocated already
                                // Turn off transparency blending (temporarily)
                                imagealphablending ( $image, false );
                                
                                // Create a new transparent color for image
                                $color = imagecolorallocatealpha ( $image, 0, 0, 0, 127 );
                                
                                // Completely fill the background of the new image with allocated color.
                                imagefill ( $image, 0, 0, $color );
                                
                                // Restore transparency blending
                                imagesavealpha ( $image, true );
                        }
                        return $image;
                }
        }
        
        /**
         * Resize image based on max width and height
         * 
         * @param integer $maxWidth
         * @param integer $maxHeight
         * @return resized image
         */
        public function resize($max_width, $max_height) {
                if ($this->_width < $max_width && $this->_height < $max_height) {
                        $this->_handleTransparentColor ();
                        return $this;
                }
                
                //maintain the aspect ratio of the image. 
                $ratio_orig = $this->_width/$this->_height;
 
                if ($max_width/$max_height > $ratio_orig) {
                        $max_width = $max_height*$ratio_orig;
                } else {
                        $max_height = $max_width/$ratio_orig;
                }
                
                //$newWidth = $this->_newDimension ( 'w', $maxWidth, $maxHeight );
                //$newHeight = $this->_newDimension ( 'h', $maxWidth, $maxHeight );
                
                $new_image = imagecreatetruecolor ( $max_width, $max_height );
                $this->_handleTransparentColor ( $new_image );
                imagecopyresampled ( $new_image, $this->_image, 0, 0, 0, 0, $max_width, $max_height, $this->_width, $this->_height );
                
                $this->_image = $new_image;
                $this->_refreshDimensions();
                return $this;
        }
}
