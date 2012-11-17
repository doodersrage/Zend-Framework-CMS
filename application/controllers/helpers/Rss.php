<?php

class Sc_Action_Helper_Rss extends Zend_Controller_Action_Helper_Abstract
{
    function direct($request_val)
    {
	  $feed = Zend_Feed_Reader::import($request_val);
	  $data = array(
		  'title'        => $feed->getTitle(),
		  'link'         => $feed->getLink(),
		  'dateModified' => $feed->getDateModified(),
		  'description'  => $feed->getDescription(),
		  'language'     => $feed->getLanguage(),
		  'entries'      => array(),
	  );
	   
	  foreach ($feed as $entry) {
		  $edata = array(
			  'title'        => $entry->getTitle(),
			  'description'  => $entry->getDescription(),
			  'dateModified' => $entry->getDateModified(),
			  'authors'       => $entry->getAuthors(),
			  'link'         => $entry->getLink(),
			  'content'      => $entry->getContent()
		  );
		  $data['entries'][] = $edata;
	  }
	  
	  $frontendOptions = array(
		   'lifetime' => 7200,
		   'automatic_serialization' => true
		);
		$backendOptions = array('cache_dir' => APP_BASE_PATH.'/cache/');
		$cache = Zend_Cache::factory(
			'Core', 'File', $frontendOptions, $backendOptions
		);
		 
		Zend_Feed_Reader::setCache($cache);
	  
	return $data;
    }
}