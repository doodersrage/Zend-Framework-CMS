<?php

class ResolutionsPrintController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Resolutions');
    }

    public function indexAction()
    {
		
		// reset plain page layout
		$this->_helper->layout->setLayout('plain');

		// walk through all available resolution options
		$resolutions = '';
		$this->view->op .= '<style>
								.offerBlockWrap{width:600px;}
								.offerBlock{padding:15px 0 15px 0;}
							</style>';
		$this->view->op .= '<div class="offerBlockWrap">
							<div style="position:relative;margin:0 auto;width:100%;">
							<div style="float:left;width:50%;">
							<div style="width:150px; margin:0 auto;"><img width="150" height="88" src="/upload/pages/Ynot-Resolution-Logo_300px.png" alt="" /></div>
							</div>
							<div style="float:left;width:50%;">
							<div style="width:146px; margin:0 auto;"><img width="146" height="88" src="/upload/pages/image/Ynot-Chop-Logo_300px.png" alt="" /></div>
							</div>
							<div style="clear:both;">&nbsp;</div>
							<!--/clearboth--></div>';
		if($_POST[friendsFamily] == 1){
			$resolutions .= 'Spend more time with family'."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Spend more time with family</h2>';
			$this->view->op .= '<p><img type="image" src="/image/?image=/upload/pages/image/2kidsFreeCoupon.jpg&amp;height=300&amp;width=600"></p>';
			$this->view->op .= '<ul>
					<li>Code: YR12A</li>
					<li>One per table, per customer, cannot be combined</li>
					<li>Cannot be sold, reproduced or exchanged.  Coupon must be present for redemption and relinquished at time of redemption.  One per customer.  Not valid with any other discount offer. No cash value. Lost or stolen coupons will not be replaced.  No photocopy, facsimile, or reproductions of this coupon will be accepted.   Expires Feb 28 2012.</li>
				</ul>';
			$this->view->op .= '</div>';
		}
		if($_POST[loseweight] == 1){
			$resolutions .= 'Lose Weight'."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Lose Weight</h2>';
			$this->view->op .= '<p><img type="image" src="/image/?image=/upload/pages/image/Ynot%20$2%20Off%20Chop_%23%282%29.jpg&amp;height=300&amp;width=600"></p>';
			$this->view->op .= '<ul>
					<li>Code: YR12B</li>
					<li>Cannot be sold, reproduced or exchanged.  Coupon must be present for redemption and relinquished at time of redemption.  One per customer.  Not valid with any other discount offer. No cash value. Lost or stolen coupons will not be replaced.  No photocopy, facsimile, or reproductions of this coupon will be accepted. Expires Feb 28 2012.</li>
				</ul>';
			$this->view->op .= '</div>';
		}
		if($_POST[exercise] == 1){
			$resolutions .= 'Exercise More'."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Exercise More</h2>';
			$this->view->op .= '<p><img alt="" src="/image/?image=/upload/pages/image/YnotChop1weekFreeCoupon_Onelife_%23.jpg&amp;height=300&amp;width=600" /></p>';
			$this->view->op .= '<p>Cannot be sold, reproduced or exchanged.  Coupon must be present for redemption and relinquished at time of redemption.  One per customer.  Not valid with any other discount offer. No cash value. Lost or stolen coupons will not be replaced.  No photocopy, facsimile, or reproductions of this coupon will be accepted. Expires Feb 28 2012.</p>';
			$this->view->op .= '</div>';
		}
		if($_POST[enjoylife] == 1){
			$resolutions .= 'Enjoy Life More'."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Enjoy Life More</h2>';
			$this->view->op .= '<p><img alt="" src="/upload/pages/Ynot-win50giftcard.png"></p>';
			$this->view->op .= '<ul>
					<li>Takes you to a submission page.</li>
					<li>Drawing  3/1/2012</li>
					<li>Automatically subscribes you to constant contact.</li>
				</ul>';
			$this->view->op .= '</div>';
		}
		if($_POST[helpothers] == 1){
			$resolutions .= 'Volunteer and Give to Charity'."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Volunteer and Give to Charity</h2>';
			$this->view->op .= '<p>Sign up for Polar Plunge on the Ynot team to help support charities!</p>
				 <center>
			     <p><a target="_blank" href="http://www.polarplunge.com/View/Page/Sign_Up">Polar Plunge Sign Up</a></p>';
			$this->view->op .= '</div>';
		}
		if($_POST[yourownresolutions] == 1){
			$resolutions .= 'Enter Your Own Resolution'."\n";
			$resolutions .= $_POST['resolution']."\n";
			$this->view->op .= '<div class="offerBlock">';
			$this->view->op .= '<h2><img width="125" height="88" src="/upload/pages/Check_140px.png" alt="checkbox" class="checkbox" /> Enter Your Own Resolution</h2>';
			$this->view->op .= '<p>'.$_POST['resolution'].'</p>';
			$this->view->op .= '<p><img type="image" src="/image/?image=/upload/pages/image/Ynot%20$2%20Off%20Chop_%23%282%29.jpg&amp;height=300&amp;width=600"></p>';
			$this->view->op .= '<ul>
					<li>Code:  YR12C</li>
					<li>Cannot be sold, reproduced or exchanged.  Coupon must be present for redemption and relinquished at time of redemption.  One per customer.  Not valid with any other discount offer. No cash value. Lost or stolen coupons will not be replaced.  No photocopy, facsimile, or reproductions of this coupon will be accepted. Expires Feb 28 2012.</li>
				</ul>';
			$this->view->op .= '</div>';
		}
		$this->view->op .= '</div>';
		
		if($resolutions != ''){
			
			$resolutions .= 'Name: '.$_POST['name']."\n";
			$resolutions .= 'Email: '.$_POST['email']."\n";

			// initialize mail handler
			$mail = new Zend_Mail();
			$mail->setBodyText($resolutions);
			$mail->setFrom(Zend_Registry::get('Contact Email'), 'YNOT Resolutions Form Submission');
			$mail->addTo(Zend_Registry::get('Contact Email'), 'Recipient');
			$mail->addTo('jarod@studiocenter.com', 'Recipient');
			$mail->addTo('rob@studiocenter.com', 'Recipient');
			$mail->setSubject('YNOT Resolutions Form Submission');
			$mail->send();
			
		} else {
			
			echo '<p>You have chosen no resolutions?</p>';
			
		}
	}
	
}