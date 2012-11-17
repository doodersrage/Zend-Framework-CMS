<?php

class Sc_Action_Helper_Calendar extends Zend_Controller_Action_Helper_Abstract
{
	public $view;
	
    function direct()
    {
		$view = $this->getActionController()->view;
		$eventscal = new Application_Model_EventsCal;
		$eventscalMapper = new Application_Model_EventsCalMapper;
		$eventsTot = $eventscalMapper->fetchAll();
		
		if(Zend_Registry::get('mobile') == false){
			$view->minifyHeadLink()->appendStylesheet('/js/fullcalendar/fullcalendar.css');
			$view->headScript()->appendFile('/js/fullcalendar/fullcalendar.min.js');
			$eventsOp = "<script type='text/javascript'>
			
				$(document).ready(function() {
				
					var date = new Date();
					var d = date.getDate();
					var m = date.getMonth();
					var y = date.getFullYear();
					
					$('#calendar').fullCalendar({
						header: {
							left: 'prev,next today',
							center: 'title',
							right: 'month,basicWeek,basicDay'
						},
						editable: false,
						events: [";
	
			$eventsArr = array();
			foreach($eventsTot as $curEvent){
						
				// grab dynamic route if assigned
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
				if($curEvent->route_id){
					$routesMapper->find($curEvent->route_id,$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
					} else {
						$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$curEvent->name)).'/'.$curEvent->id.'/';
					}
				} else {
					$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$curEvent->name)).'/'.$curEvent->id.'/';
				}
			
				$eventsArr[] = "{
								title: '".str_replace("'","\'",$curEvent->name)."',
								start: new Date(".date('Y',$curEvent->start).", ".(date('n',$curEvent->start)-1).", ".date('j',$curEvent->start)."),
								end: new Date(".date('Y',$curEvent->finish).", ".(date('n',$curEvent->finish)-1).", ".date('j',$curEvent->finish)."),
								url: '".$lnkFnl."'
							}";
					
			}
			$eventsOp .= implode(',',$eventsArr);
			$eventsOp .= "
								  ]
							  });
							  
						  });
					  
					  </script><div id='calendar'></div>";
		} else {
			$eventsArr = array();
			
			foreach($eventsTot as $curEvent){
				$eventsOp = '<style>
								.eventLnk{
									width:100%;
									padding:3px 0;
									display:block;
								}
							</style>';	
				// grab dynamic route if assigned
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
				if($curEvent->route_id){
					$routesMapper->find($curEvent->route_id,$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
					} else {
						$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$curEvent->name)).'/'.$curEvent->id.'/';
					}
				} else {
					$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$curEvent->name)).'/'.$curEvent->id.'/';
				}
			
				$eventsArr[] = '<a class="eventLnk" href="'.$lnkFnl.'" title="'.$curEvent->name.'">'.$curEvent->name.' Start: '.date('Y',$curEvent->start).', '.(date('n',$curEvent->start)-1).', '.date('j',$curEvent->start).' Finish: '.date('Y',$curEvent->finish).', '.(date('n',$curEvent->finish)-1).', '.date('j',$curEvent->finish).'</a>';
					
			}
			$eventsOp .= implode("\n",$eventsArr);
			
		}
		$view->calendar = $eventsOp;
    }
	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}