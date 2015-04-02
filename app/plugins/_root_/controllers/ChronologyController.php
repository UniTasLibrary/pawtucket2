<?php
/* ----------------------------------------------------------------------
 * controllers/ChronologyController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
 	require_once(__CA_APP_DIR__."/plugins/_root_/controllers/BaseItineraController.php");
 	require_once(__CA_MODELS_DIR__."/ca_entities.php");
 
 	class ChronologyController extends BaseItineraController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			AssetLoadManager::register('slider');
 			AssetLoadManager::register('carousel');
 			
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		function Index() {
 			if (!is_array($va_entity_list = $this->request->session->getVar('itinera_entity_list'))) { $va_entity_list = array(); }
 			
			$pn_entity_id = $this->request->getParameter('id', pInteger);
			
			if ($pn_entity_id && !in_array($pn_entity_id, $va_entity_list)) { 
				$t_entity = new ca_entities($pn_entity_id);
				
				$vs_color = itineraGetUnusedColor($va_entity_list, $t_entity->get('ca_entities.color'));
				
				$va_entity_list[$vs_color] = $pn_entity_id; 
				
 				$this->request->session->setVar('itinera_entity_list', $va_entity_list);
			}
 			$this->view->setVar('entity_list', $va_entity_list);
 			
 			$this->render('Chronology/index_html.php');
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		function Get() {
 			if (!is_array($va_entity_list = $this->request->session->getVar('itinera_entity_list'))) { $va_entity_list = array(); }
 			$ps_mode = $this->request->getParameter('m', pString);
 			
 			$va_added_entity_ids = $va_removed_entity_ids = array();
 			if ($pn_entity_id = $this->request->getParameter('id', pInteger)) {
 				switch($ps_mode) {
 					case 'remove':
 						if(($vn_i = array_search($pn_entity_id, $va_entity_list)) !== false) {
							unset($va_entity_list[$vn_i]);
							$va_removed_entity_ids[] = $pn_entity_id;
						}
 						break;
 					default:
 						if (!in_array($pn_entity_id, $va_entity_list)) { 
 							$t_entity = new ca_entities($pn_entity_id);
 							
 							$vs_color = itineraGetUnusedColor($va_entity_list, $t_entity->get('ca_entities.color'));
							
 							$va_entity_list[$vs_color] = $pn_entity_id; 
 							$va_added_entity_ids[] = $pn_entity_id; 
 						}
 						break;	
 				}
 				
 				$this->request->session->setVar('itinera_entity_list', $va_entity_list);
 				 				
				$this->view->setVar('added_entity_ids', $va_added_entity_ids);
				$this->view->setVar('removed_entity_ids', $va_removed_entity_ids);
 			}
			

			$this->view->setVar('entity_list', $va_entity_list);
			
			if (sizeof($va_added_entity_ids) > 0) {
				$this->view->setVar('entity_id', $pn_entity_id);
				$this->view->setVar('entity_name', $t_entity ? $t_entity->get('ca_entities.preferred_labels.displayname') : '?');
			
				$this->view->setVar('entity_image', $t_entity ? $t_entity->get('ca_entities.agentMedia', array('version' => 'icon')) : '');
				
				$this->view->setVar('stops', $t_entity ? $t_entity->get('ca_tour_stops.stop_id', array('returnAsArray' => true)) : array());

				$this->render('Chronology/get_chronology_track_html.php');
			}
		}
 		# -------------------------------------------------------
 	}