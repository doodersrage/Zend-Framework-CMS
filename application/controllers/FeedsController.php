<?php

class FeedsController extends Zend_Controller_Action
{
    public function init()
    {
		$this->view->headTitle()->prepend('RSS Feeds');
    }
	
    public function indexAction()
    {
		// build industry news scoller
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('rss_feeds');
		$results = $db->fetchAll($select);
		
		$industry_news = '';
		foreach($results as $id => $item){
			if($this->_helper->urlcheck($item[url]) == 200){
			  $rssArr = $this->_helper->rss($item[url]);
			  
			  $curBlock = 1;
			  $curItem = 0;
			  $maxBlocks = 10;
			  
			  $industry_news .= '<div class="industryNewsBlock"><h1>'.$item[name].'</h1>';
			  foreach($rssArr['entries'] as $newsItem){
				  
				  $industry_news .= '<div class="industry-news-block">';
				  $industry_news .= '<div class="industry-news-item"> <a target="_blank" href="'.$newsItem['link'].'" rel="nofollow">'.substr($newsItem['title'],0,150).'...</a></div>
								<div class="new-more"> '.substr(strip_tags($newsItem['description']),0,250).'...</div>
								<div class="industry-news-border"></div>
							</div>';
				  if($curItem == $itemsPerBlock) {
					  $curBlock++;
				  }
				  if($curBlock > $maxBlocks) break;
			  }
			  $industry_news .= '</div>';
			} else {
			  $industry_news .= '<div class="industryNewsBlock"><h1>'.$item[name].'</h1>';
			  $industry_news .= '<div class="industry-news-block">';
			  $industry_news .= '<div class="industry-news-item"> <a target="_blank" href="'.$newsItem['link'].'" rel="nofollow">Unsable to connect!</a></div>
							<div class="new-more">We were unsable to connect to the selected RSS feed. Please try again later. </div>
							<div class="industry-news-border"></div>
						</div>';
			}
		}
		
		$this->view->industry_news = $industry_news;
	}
	
}

