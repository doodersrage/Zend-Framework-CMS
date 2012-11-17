<?PHP
class ImageController extends Zend_Controller_Action {
   public function indexAction() {
	   
	   $this->_helper->layout()->disableLayout();
	   $this->_helper->viewRenderer->setNoRender(true);
	   
	   if($this->getRequest()->getParam('width') != ''){
		   $width = $this->getRequest()->getParam('width');
	   } else {
		   $width = 250;
	   }
	   
	   if($this->getRequest()->getParam('height') != ''){
		   $height = $this->getRequest()->getParam('height');
	   } else {
		   $height = 250;
	   }
	   
	   if($this->getRequest()->getParam('type') != ''){
	   		$type = $this->getRequest()->getParam('type');
	   } else {
	   		$type = 'png';
	   }

	   ob_start();
	   $savePath = APP_BASE_PATH.'/upload/thumbs/'.$height.'-'.$width.str_replace("/","-",$this->getRequest()->getParam('image'));
	   $origPath = APP_BASE_PATH.$this->getRequest()->getParam('image');
	   $fileTime = filemtime($savePath);
	   $datediff = filemtime($origPath);
	   
	   if(!file_exists($savePath)){
		   
		  $thumb = PhpThumbFactory::create(APP_BASE_PATH.$this->getRequest()->getParam('image'));
		  $thumb->resize($width, $height);
		  $thumb->save($savePath, $type);
	   } else {
		   if($fileTime < $datediff){
			  $thumb = PhpThumbFactory::create(APP_BASE_PATH.$this->getRequest()->getParam('image'));
			  $thumb->resize($width, $height);
			  $thumb->save($savePath, $type);
		   }
	   }
	   ob_end_clean();
	   
	   header('Content-type: image/'.($type=='jpg' ? 'jpeg' : $type));
	   $image   = file_get_contents($savePath);
	   echo $image;
   }
}