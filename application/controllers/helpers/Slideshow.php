
<?php

class Sc_Action_Helper_Slideshow extends Zend_Controller_Action_Helper_Abstract
{
	public $view;
	
    function direct($request_val = NULL)
    {
		$view = $this->getActionController()->view;
		$slideshow = new Application_Model_Slideshow;
		$slideshowMapper = new Application_Model_SlideshowMapper;
		$slideshowMapper->find($request_val,$slideshow);
		$slideshowData = new Application_Model_SlideshowData;
		$slideshowDataMapper = new Application_Model_SlideshowDataMapper;
		if(!empty($request_val)){

			$slideshowDataAll = $slideshowDataMapper->fetchAll($request_val);
			$images = array();
			foreach($slideshowDataAll as $CPD){
				list($width, $height) = getimagesize(APP_BASE_PATH.'/upload/images/'.$CPD->img); 
				if(Zend_Registry::get('mobile') == false){
					$images[] = '<a href="/upload/images/'.$CPD->img.'" class="slideImage" title="'.$CPD->desc.'"><img src="/image/?image=/upload/images/'.$CPD->img.'&width='.$slideshow->width.'&height='.$slideshow->height.'" alt="'.$CPD->desc.'" title="'.$CPD->desc.'" /></a>';
				} else {
					$images[] = '<a href="/upload/images/'.$CPD->img.'" class="slideImage" title="'.$CPD->desc.'"><img src="/image/?image=/upload/images/'.$CPD->img.'&width=300&height=200" alt="'.$CPD->desc.'" title="'.$CPD->desc.'" /></a>';
				}
			}
			
			if(Zend_Registry::get('mobile') == false){
				$slideshowOP = '<style type="text/css">
								#wrapper{
									position:relative;
									margin:0 auto;
									padding:4px;
								}
								#slider-wrapper {
									width:'.$slideshow->width.'px; /* Change this to your images width */
									height:'.$slideshow->height.'px; /* Change this to your images height */
								}
								
								#slider {
									position:relative;
									width:'.$slideshow->width.'px; /* Change this to your images width */
									height:'.$slideshow->height.'px; /* Change this to your images height */
									background:url(/js/nivo-slider/demo/images/loading.gif) no-repeat 50% 50%;
								}
								#slider img {
									position:absolute;
									top:0px;
									left:0px;
									display:none;
								}
								#slider a {
									border:0;
									display:block;
								}
								
								.nivo-controlNav {
									position:absolute;
									left:260px;
									bottom:0;
								}
								.nivo-controlNav a {
									display:block;
									width:22px;
									height:22px;
									background:url(/js/nivo-slider/demo/images/bullets.png) no-repeat;
									text-indent:-9999px;
									border:0;
									margin-right:3px;
									float:left;
								}
								.nivo-controlNav a.active {
									background-position:0 -22px;
								}
								
								.nivo-directionNav a {
									display:block;
									width:30px;
									height:30px;
									background:url(/js/nivo-slider/demo/images/arrows.png) no-repeat;
									text-indent:-9999px;
									border:0;
								}
								a.nivo-nextNav {
									background-position:-30px 0;
									right:15px;
								}
								a.nivo-prevNav {
									left:15px;
								}
								
								.nivo-caption {
									text-shadow:none;
									font-family: Helvetica, Arial, sans-serif;
								}
								.nivo-caption a { 
									color:#efe9d1;
									text-decoration:underline;
								}
								#slide-head{
									padding:5px;
									font-weight:700;
									width:'.$slideshow->width.'px; /* Change this to your images width */
									text-align:center;
								}
								</style>
								<script type="text/javascript">
								//<![CDATA[
								$(function() {
									$(\'#slider\').nivoSlider({
										pauseTime:3000, // How long each slide will show
										'.($slideshow->autoplay == 1 ? 'manualAdvance:false,' : 'manualAdvance:true,').'
										'.($slideshow->controlbar == 1 ? 'controlNav:true' : 'controlNav:false').'
									});
									$("a.slideImage").fancybox();
								});    //]]>
								</script>
								<div id="wrapper">
								<div id="slide-head">Click image to enlarge.</div>
								<div id="slider-wrapper">
								<div id="slider" class="nivoSlider">
							'.implode("\n",$images).'
							</div>
							</div>
							</div>';
			} else {
				$slideshowOP = '<div id="slider" class="nivoSlider">
							'.implode("\n",$images).'
							</div>';
			}
		}
	return $slideshowOP;
	}
	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}