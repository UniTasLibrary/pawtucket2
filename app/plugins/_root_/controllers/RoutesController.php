<?php
/* ----------------------------------------------------------------------
 * controllers/RoutesController.php
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
 
 	class RoutesController extends BaseItineraController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			AssetLoadManager::register('leaflet');
 			AssetLoadManager::register('slider');
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
 			
 			$this->render('Routes/index_html.php');
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

 			$this->render('Routes/get_html.php');
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		function GetMapData() {
 			$ps_entity_ids = $this->request->getParameter('ids', pString);	// if set return data for a specific entity rather than the list
 			$pa_entity_ids = $ps_entity_ids ? explode(';', $ps_entity_ids) : array();
 			if (!is_array($va_entity_list = $this->request->session->getVar('itinera_entity_list'))) { $va_entity_list = array(); }
 			
 			$va_map_data = array();
 			
 			$va_used_colors = array_flip($va_entity_list);
 			if (sizeof($pa_entity_ids) > 0) { 
 				// assign colors
 				$va_tmp = array();
 				foreach($pa_entity_ids as $vn_entity_id) {
 					$vs_color = itineraGetUnusedColor($va_entity_list, $va_used_colors[$vn_entity_id]);
 					$va_tmp[$vs_color] = $vn_entity_id;
 				}
 				$va_entity_list = $va_tmp; 
 			}
 			
 			if (sizeof($va_entity_list) > 0) {
 				$qr_res = caMakeSearchResult('ca_entities', array_values($va_entity_list));
 			
 				while($qr_res->nextHit()) {
 					$vn_entity_id = $qr_res->get('ca_entities.entity_id');
 					
 					$va_coord_list = array();
 					
 					if (is_array($va_stop_ids = $qr_res->get('ca_tour_stops.stop_id', array('returnAsArray' => true))) && sizeof($va_stop_ids)) {
 						$qr_stops = caMakeSearchResult('ca_tour_stops', $va_stop_ids);
 						while($qr_stops->nextHit()) {
 							$va_georefs = $qr_stops->get('ca_places.georeference', array('returnAsArray' => true));
 							$va_dates = $qr_stops->get('ca_tour_stops.tourStopDateSet.tourStopDateIndexingDate', array('returnAsArray' => true, 'rawDate' => true));
 							$vn_start = $vn_end = null;
 							foreach($va_dates as $va_date) {
 								$vn_start = $va_date['start'];
 								$vn_end = $va_date['end'];
 								break;
 							}
 							foreach($va_georefs as $vn_i => $va_coord) {
 								$vs_coord_proc = preg_replace("![\[\]]+!", "", $va_coord['georeference']);
 								$va_points = explode(';', $vs_coord_proc);
 								$va_parsed_points = array();
 								foreach($va_points as $vs_point) {
 									$va_parsed_points[] = explode(',', $vs_point);
 								}
 								
 								$vs_text = $qr_stops->getWithTemplate('<div class="travelerMapListArtistPopupImage">^ca_entities.agentMedia</div> <strong>^ca_entities.preferred_labels.name</strong><br/>^ca_tour_stops.preferred_labels.name</br>^ca_tour_stops.tourStopDateSet.tourStopDateDisplayDate<br/>^ca_tour_stops.tour_stop_description<br/><br/><ifdef code="ca_list_items.preferred_labels"><em>Source: ^ca_list_items.preferred_labels</em></ifdef>');
 								
 								if (sizeof($va_points) > 1) {
 									$va_coord_list[] = array('type' => 'polygon', 'text' => $vs_text, 'coordinates' => $va_parsed_points, 'start' => $vn_start, 'end' => $vn_end);
 								} else {
 									$va_coord_list[] = array('type' => 'point', 'text' => $vs_text, 'coordinates' => $va_parsed_points[0], 'start' => $vn_start, 'end' => $vn_end);
 								}
 							}
 						}
 					}
 					
 					
 					$va_map_data[$vn_entity_id] = array(
 						'id' => $vn_entity_id,
 						'name' => $qr_res->get('ca_entities.preferred_labels.displayname'),
 						'color' => $va_used_colors[$vn_entity_id],
 						'stops' => $va_coord_list
 					);
 				}
 			}
 			
 			
 			$this->view->setVar('entity_list', $va_entity_list);
 			$this->view->setVar('map_data', $va_map_data);
 			
 			$this->render('Routes/get_map_data_json.php');
 		}
 		# -------------------------------------------------------
 	}