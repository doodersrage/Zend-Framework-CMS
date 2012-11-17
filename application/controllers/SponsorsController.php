<?php

class SponsorsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Sponsors');
    }

    public function indexAction()
    {
        if($this->_getParam('sponsor_name') == ''){
		  // load page content and header data
		  $request_val = str_replace('-',' ','Sponsors');
		  
		  if(!empty($request_val)){
			  $this->_helper->content($request_val);
		  }
		} else {
			$this->view->copy_text = '<div id="sponsors-info-wrap">
				<div class="sponsors-fill-left"><img src="images/altriavideoimage.jpg" width="523" height="399" alt="sponsor video" /></div>
				<div class="sponsors-fill-right">
				<img src="images/sponsors/altria.jpg" width="175" height="65" alt="altria" />
				<p>Optatia dio eaquaecto qui cus intis vendelita sectest ut que quis magnimus magnitio etus quodis veles eum apidendam re lab im am dit endus quament.</p>
			<p>Nem hari ra conseque non rerupis accaepernam, nihitae. Nequid molo quo blant aut enesse velest doluptatur simet presedit, is aliquia tiore, exerero odisit aspid quunt, sinvelliqui di alitate volupta as dolecest et eosandi psandio rumquatem idigend igentium alitae officabo. Hiciatum aut andaepr ovitate magnimp oritest excessusae.</p>
			  </div>
			  <div class="clear"></div>
			</div>';
		}
    }

}

