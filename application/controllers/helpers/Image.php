<?php
/**
 * @author     Mohamed Alsharaf
 * @category   Core
 * @package    Core_ViewHelper
 * @copyright  Copyright (c) 2008-2009 Mohamed Alsharaf.
 * @license    http://framework.zend.com/license/new-bsd  New BSD License
 * @version    0.0.2
 */
class Sc_Action_Helper_Image extends Zend_View_Helper_Abstract
{
    private $_name = null;
    private $_width = null;
    private $_height = null;
    private $_src = null;
    private $_imagePath = null;
    private $_fileName = null;
    private $_imgMime = null;
    private $_validMime = array(
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif'
    );

    public function direct($name, $imagePath=null,
                          $attribs = array(),
                          $action=null) {
        // set name
        $this->_name = $this->view->escape($name);

        // set path
        $this->_setImagepath($imagePath);

        // set attributes
        $this->_setAttributes($attribs);

        // add action to image (e.g. generate thumbnail)
        // default action set dimensions
        if(!$this->_setAction($action)) {
            $this->_setDimensions();
        }

        // render image
        return $this->_render();
    }

    /**
     * Return image relative path
     *
     * @return string
     */
    public function getImagePath() {
        return $this->_imagePath;
    }

    /**
     * Return image src
     *
     * @return string
     */
    public function getSrc() {
        return $this->_src;
    }

    /**
     * Return image width
     *
     * @return string
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Return image height
     *
     * @return string
     */
    public function getHeight() {
        return $this->_height;
    }

    /**
     * Return image name
     *
     * @return string
     */
    public function getImageName() {
        return $this->_fileName;
    }

    /**
     * Set new image after a specific action applied  on the current image
     *
     * @param string $path
     * @return self
     */
    public function setNewImage($path, $width = null, $height = null) {
        // set image new path
        $this->_setImagepath($path);

        if($width !== null) {
            $this->_width = $width;
        }

        if($height !== null) {
            $this->_height = $height;
        }
    }

    /**
     * render image html tag
     *
     * @return string
     */
    protected function _render() {
        $xhtml  = '<img src="' . $this->_src . '" ' .
                  $this->_attribs . ' id="' . $this->_name . '"';
        $xhtml .= !empty($this->_width)? ' width="' . $this->_width . '"' : '';
        $xhtml .= !empty($this->_height)? ' height="' . $this->_height . '"' : '';

        $endTag = " />";
        if (($this->view instanceof Zend_View_Abstract)
            && !$this->view->doctype()->isXhtml()) {
            $endTag= ">";
        }
        return $xhtml . $endTag;
   }

   /**
    * Retrieve image sizes and type
    * APPLICATION_PUBLIC constants needed for path to public root
    *
    * @todo add cache beacuse getimagesize() is expensive to use.
    * @return boolean
    */
   protected function _setDimensions() {
      // get image size
      $path = PUBLIC_PATH . $this->_imagePath;

      if(!$imgInfo = @getimagesize($path)) {
             return false;
      }
      // is image mime allowed
      if(!in_array($imgInfo['mime'] , $this->_validMime)) {
         return false;
      }
      // set image info
      $this->_imgMime = $imgInfo['mime'];
      $this->_height = $imgInfo[1];
      $this->_width  = $imgInfo[0];
      return true;
    }

    /**
     * Set image path
     *
     * @param string $path
     * @return self
     */
    protected function _setImagepath($path) {
        $this->_imagePath = $path;
        $this->_fileName = basename($path);
        $this->_src = $this->view->baseUrl($path, true);
        return $this;
    }

    /**
     * Set image attributes
     *
     * @param array $attribs
     * @return self
     */
    protected function _setAttributes($attribs) {
        $alt = '';
        $class = '';
        $map = '';
        $class  = '';
        if(isset($attribs['alt'])) {
            $alt = 'alt="' . $this->view->escape($attribs['alt']) . '" ';
        }

        if(isset($attribs['title'])) {
            $title = 'title="' . $this->view->escape($attribs['title']) . '" ';
        } else {
            $title = 'title="' . $this->view->escape($attribs['alt']) . '" ';
        }

        if(isset($attribs['map'])) {
            $map = 'usemap="#' . $this->view->escape($attribs['map']) . '" ';
        }

        if(isset($attribs['class'])) {
            $class = 'class="' . $this->view->escape($attribs['class']) . '" ';
        }
        $this->_attribs = $alt . $title . $map . $class;
        return $this;
    }

    /**
     * Set specific action your image. e.g. resize image, crop, etc...
     *
     * @param string $action
     * @return boolean
     */
    protected function _setAction($actionCallback) {
        if($actionCallback === null) {
            return false;
        }

        $options = null;
        $action = $actionCallback;
        if(is_array($actionCallback)) {
            $action = $actionCallback[0];
            $options = $actionCallback[1];
        }
        $actionClass = 'Core_ViewHelper_Html_Image_Action_' . ucfirst($action);
        $actionClass = new $actionClass($options);

        // if action class is not valid then return false
        if(!$actionClass instanceof Core_ViewHelper_Html_Image_ActionInterface) {
            return false;
        }
        return $actionClass->build($this);
    }
}


/**
 * @author     Mohamed Alsharaf
 * @category   Core
 * @package    Core_ViewHelper
 * @copyright  Copyright (c) 2008-2009 Mohamed Alsharaf.
 * @license    http://framework.zend.com/license/new-bsd  New BSD License
 * @version    0.0.2
 */
interface Core_ViewHelper_Html_Image_ActionInterface
{
    public function build($imageInstance);
}


/**
 * @author     Mohamed Alsharaf
 * @category   Core
 * @package    Core_ViewHelper
 * @copyright  Copyright (c) 2008-2009 Mohamed Alsharaf.
 * @license    http://framework.zend.com/license/new-bsd  New BSD License
 * @version    0.0.2
 */
class Core_ViewHelper_Html_Image_Action_Thumbnail
    implements Core_ViewHelper_Html_Image_ActionInterface
{
    public function build($imageInstance) {
        // add your code to generate thumbnail
        return $this->_test($imageInstance);
    }

    /**
     * Test method to generate the thumbnail
     *
     * @return boolean
     */
    private function _test($imageInstance) {
        // dir to where you want to save the thumbnail image
        $relativePath = dirname($imageInstance->getImagePath()) . '/thumbs/';
        $dir = PUBLIC_PATH . '/' . $relativePath;

        // create the directory if it does not exist
        clearstatcache();
        if(!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        // name of the image based on the size of the thumbnail
        // @todo the sizes can be in config file/database. for not its hard coded
        $newFileName = '100x100' . '_'  . $imageInstance->getImageName();
        $thumbPath = $dir . $newFileName;

        // if thumbnail exists then set new image and return false
        if(file_exists($thumbPath)) {
            $imageInstance->setNewImage($relativePath . $newFileName);
            return false;
        }

        // resize image
        $image = new Moo_Image();
        // open original image to resize it
        // set the thumnail sizes
        // set new image path and quality
        $image->open(PUBLIC_PATH . $imageInstance->getImagePath())
              ->resize(100, 100)
              ->save($thumbPath, 75);

        // pass new image details to image view helper
        $imageInstance->setNewImage($relativePath . $newFileName, $image->getWidth(), $image->getHeight());
        return true;
    }
}
##
<?php echo $this->image('name', 'img/products/1.jpg', array('alt' => 'image alt'), 'thumbnail'); ?>

## Image class
<?php
/**
 * @copyright  2009, S. Mohammed Alsharaf
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author     S. Mohammed Alsharaf (satrun77@hotmail.com)
 * @link       http://www.safitech.com
 * @version    1.0
 */
class My_Image
{
	protected $_filename = '';
	protected $_image = '';
	protected $_width = '';
	protected $_height = '';
	protected $_mimeType = '';
	protected $_view = null;
	const IMAGETYPE_GIF = 'gif';
	const IMAGETYPE_JPEG = 'jpeg';
	const IMAGETYPE_PNG = 'png';
	const IMAGETYPE_JPG ='jpg';
	
	public function setView($view)
	{
		$this->_view = $view;
	return $this;	
	}
	
	protected function _newDimension($forDim,$maxWidth,$maxHeight)
	{
	  if ($this->_width > $maxWidth)
	  {
		  $ration = $maxWidth/$this->_width;
		  $newwidth = round($this->_width*$ration);
		  $newheight = round($this->_height*$ration);
		  if ($newheight > $maxHeight)
		  {
			$ration = $maxHeight/$newheight;
			$newwidth = round($newwidth*$ration);
			$newheight = round($newheight*$ration);

			if($forDim == 'w')
				return $newwidth;
			else
				return $newheight;
		  }
		  else
		  {
			if($forDim == 'w')
				return $newwidth;
			else
				return $newheight;
		  }
	  }
	  else if ($this->_height > $maxHeight)
	  {
		$ration = $maxHeight/$this->_height;
		$newwidth = round($this->_width*$ration);
		$newheight = round($this->_height*$ration);
		if ($newwidth > $maxWidth)
		{
			$ration = $maxWidth/$newwidth;
			$newwidth = round($newwidth*$ration);
			$newheight = round($newheight*$ration);
			if($forDim == 'w')
				return $newwidth;
			else
				return $newheight;
		}
		else
		{
			if($forDim == 'w')
				return $newwidth;
			else
				return $newheight;
		}
	  }
	  else
	  {
		if($forDim == 'w')
			return $this->_width;
		else
			return $this->_height;
	  }
	}
	
	public function open($filename)
	{
		$this->_filename = $filename;
		$this->_setInfo();
		switch($this->_mimeType) {
			case self::IMAGETYPE_GIF:
				$this->_image = imagecreatefromgif($this->_filename);
			break;
			case self::IMAGETYPE_JPEG:
			case self::IMAGETYPE_JPG:
				$this->_image = imagecreatefromjpeg($this->_filename);
			break;
			case self::IMAGETYPE_PNG:
				$this->_image = imagecreatefrompng($this->_filename);
			break;
			default:
			throw new Exception('Image extension is invalid or not supported.');
			break;
		}
	return $this;
	}

	protected function _output($_saveIn=null, $_quality, $_filters=null)
	{
		switch($this->_mimeType) {
			case self::IMAGETYPE_GIF:
				return imagegif($this->_image, $_saveIn);
			break;
			case self::IMAGETYPE_JPEG:
			case self::IMAGETYPE_JPG:
				$_quality = is_null($_quality)? 75 : $_quality;
				return imagejpeg($this->_image, $_saveIn, $_quality);
			break;
			case self::IMAGETYPE_PNG:
				$_quality = is_null($_quality)? 0 : $_quality;
				$_filters = is_null($_filters)? null : $_filters;
				return imagepng($this->_image, $_saveIn, $_quality, $_filters);
			break;
			default:
				throw new Exception('Image cannot be created.');
			break;
		}
	}
	
	public function display($_quality=null, $_filters=null)
	{
		if($this->_view instanceof Zend_View) {
			$this->_view->getResponse()->setHeader('Content-Type', $this->_mimeType);
		} else {
			header('Content-Type', $this->_mimeType);
		}
		return $this->_output(null,$_quality, $_filters);
	}
	
	public function save($_saveIn=null, $_quality=null, $_filters=null)
	{
		return $this->_output($_saveIn,$_quality, $_filters);
	}
	
    public function __destruct()
    {
        @imagedestroy($this->_image);
    }
	
	protected function _setInfo()
	{
		$imgSize = @getimagesize($this->_filename);
		if(!$imgSize) {
			throw new Exception('Could not extract image size.');
		} elseif($imgSize[0] == 0 || $imgSize[1] == 0) {
			throw new Exception('Image has dimension of zero.');
		}
		$this->_width = $imgSize[0];
		$this->_height = $imgSize[1];
		$this->_mimeType = $imgSize['mime'];
	}
	
	public function getWidth()
	{
		return $this->_width;
	}
	
	public function getHeight()
	{
		return $this->_height;
	}
	
	protected function _refreshDimensions()
	{
		$this->_height = imagesy($this->_image);
		$this->_width = imagesx($this->_image);
	}

	/**
	 * If image is GIF or PNG keep transparent colors
	 * 
	 * @credit http://github.com/maxim/smart_resize_image/tree/master
	 * @param $image src of the image
	 * @return the modified image
	 */
	protected function _handleTransparentColor($image=null)
	{
		$image = is_null($image)? $this->_image : $image;
		
	    if ( ($this->_mimeType == self::IMAGETYPE_GIF) || ($this->_mimeType == self::IMAGETYPE_PNG) ) {
      		$trnprt_indx = imagecolortransparent($this->_image);
 
      		// If we have a specific transparent color
      		if ($trnprt_indx >= 0) {
		        // Get the original image's transparent color's RGB values
		        $trnprt_color    = imagecolorsforindex($this->_image, $trnprt_indx);
		 
		        // Allocate the same color in the new image resource
		        $trnprt_indx    = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
		 
		        // Completely fill the background of the new image with allocated color.
		        imagefill($image, 0, 0, $trnprt_indx);
		 
		        // Set the background color for new image to transparent
		        imagecolortransparent($image, $trnprt_indx);
      		} elseif ($this->_mimeType == self::IMAGETYPE_PNG) {
	 			// Always make a transparent background color for PNGs that don't have one allocated already
		        // Turn off transparency blending (temporarily)
		        imagealphablending($image, false);
		 
		        // Create a new transparent color for image
		        $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
		 
		        // Completely fill the background of the new image with allocated color.
		        imagefill($image, 0, 0, $color);
		 
		        // Restore transparency blending
		        imagesavealpha($image, true);
      		}
     	return $image;
    	}		
	}
	
	/**
	 * Resize image based on max width and height
	 * 
	 * @param integer $maxWidth
	 * @param integer$maxHeight
	 * @return resized image
	 */
	public function resize($maxWidth, $maxHeight)
	{
		if ($this->_width < $maxWidth && $this->_height < $maxHeight) {
			$this->_handleTransparentColor();
			return $this;
		}
		
		$newWidth = $this->_newDimension('w', $maxWidth, $maxHeight);
		$newHeight = $this->_newDimension('h', $maxWidth, $maxHeight);
			
  	 	$newImage = imagecreatetruecolor( $newWidth, $newHeight );
    	$this->_handleTransparentColor($newImage);
    	imagecopyresampled($newImage, $this->_image, 0, 0, 0, 0, $newWidth, $newHeight, $this->_width, $this->_height);
    
    	$this->_image = $newImage;
    	$this->_refreshDimensions();
 	return $this;
	}
}