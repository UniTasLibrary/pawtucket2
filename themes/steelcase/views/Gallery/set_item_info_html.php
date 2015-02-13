<?php
		$va_access_values = caGetUserAccessValues($this->request);
		$t_lists = new ca_lists();
		print "(".$this->getVar("set_item_num")."/".$this->getVar("set_num_items").")<br/>";
		$t_object = $this->getVar("object");
?>
		<H4>{{{^ca_objects.preferred_labels.name}}}<?php
		if($t_object->get("ca_objects.creation_date")){
			print ", ";
			if(strtolower($t_object->get("ca_objects.creation_date")) == "unknown"){
				print "Date ".$t_object->get("ca_objects.creation_date");
			}else{
				print $t_object->get("ca_objects.creation_date");
			}
		}
?>
		</H4>
<?php
		$va_entities = $t_object->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array("creator"), "checkAccess" => $va_access_values));
		if(sizeof($va_entities)){
			$t_entity = new ca_entities();
			print "<H5>";
			foreach($va_entities as $va_entity){
				$t_entity->load($va_entity["entity_id"]);
				print caDetailLink($this->request, $t_entity->getLabelForDisplay(), "", "ca_entities", $t_entity->get("entity_id"));
				$vs_nationality = trim($t_entity->get("nationality", array("delimiter" => "; ", "convertCodesToDisplayText" => true)));
				$vs_dob = $t_entity->get("dob_dod");
				if(strtolower($vs_dob) == "unknown"){
					$vs_dob = "";
				}
				if($vs_nationality || $vs_dob){
					print " (".$vs_nationality;
					if($vs_nationality && $vs_dob){
						print ", ";
					} 
					print $vs_dob.")";
				}
				print "<br/>";
			}
			print "</H5>";
		}
?>
		<HR/>
<?php
		$t_list_item = new ca_list_items();
		if(trim($t_object->get("ca_objects.decorative_types", array("convertCodesToDisplayText" => true)))){
			print "<H6>Classification</H6>";
			$t_list_item->load($t_object->get("ca_objects.decorative_types"));
			print caNavLink($this->request, $t_list_item->get("ca_list_item_labels.name_singular"), "", "", "Browse", "Objects", array("facet" => "decorative_types_facet", "id" => $t_object->get("ca_objects.decorative_types")));
		}
		if(trim($t_object->get("ca_objects.documentation_types", array("convertCodesToDisplayText" => true)))){
			print "<H6>Classification</H6>";
			$t_list_item->load($t_object->get("ca_objects.documentation_types"));
			print caNavLink($this->request, $t_list_item->get("ca_list_item_labels.name_singular"), "", "", "Browse", "Objects", array("facet" => "documentation_types_facet", "id" => $t_object->get("ca_objects.documentation_types")));
		}
		if(trim($t_object->get("ca_objects.fine_art_types", array("convertCodesToDisplayText" => true)))){
			print "<H6>Classification</H6>";
			$t_list_item->load($t_object->get("ca_objects.fine_art_types"));
			print caNavLink($this->request, $t_list_item->get("ca_list_item_labels.name_singular"), "", "", "Browse", "Objects", array("facet" => "fine_art_types_facet", "id" => $t_object->get("ca_objects.fine_art_types")));
		}
?>
		{{{<ifdef code="ca_objects.dimensions_frame.display_dimensions_frame"><H6>Framed dimensions</H6>^ca_objects.dimensions_frame.display_dimensions_frame</ifdef>}}}
<?php
		if(!$t_object->get("ca_objects.dimensions_frame.display_dimensions_frame") && ($t_object->get("ca_objects.dimensions_frame.dimensions_frame_height") || $t_object->get("ca_objects.dimensions_frame.dimensions_frame_width") || $t_object->get("ca_objects.dimensions_frame.dimensions_frame_depth") || $t_object->get("ca_objects.dimensions_frame.dimensions_frame_length"))){
			print "<H6>Framed dimensions</H6>";
			$va_dimension_pieces = array();
			if($t_object->get("ca_objects.dimensions_frame.dimensions_frame_height")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions_frame.dimensions_frame_height");
			}
			if($t_object->get("ca_objects.dimensions_frame.dimensions_frame_width")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions_frame.dimensions_frame_width");
			}
			if($t_object->get("ca_objects.dimensions_frame.dimensions_frame_depth")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions_frame.dimensions_frame_depth");
			}
			if($t_object->get("ca_objects.dimensions_frame.dimensions_frame_length")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions_frame.dimensions_frame_length");
			}
			if(sizeof($va_dimension_pieces)){
				print join(" x ", $va_dimension_pieces);
			}
			if($t_object->get("ca_objects.dimensions_frame.dimensions_frame_weight")){
				print " ".$t_object->get("ca_objects.dimensions_frame.dimensions_frame_weight");
			}
		}
?>				
		{{{<ifdef code="ca_objects.dimensions.display_dimensions"><H6>Dimensions</H6>^ca_objects.dimensions.display_dimensions</ifdef>}}}
<?php
		if(!$t_object->get("ca_objects.dimensions.display_dimensions") && ($t_object->get("ca_objects.dimensions.dimensions_height") || $t_object->get("ca_objects.dimensions.dimensions_width") || $t_object->get("ca_objects.dimensions.dimensions_depth") || $t_object->get("ca_objects.dimensions.dimensions_length"))){
			print "<H6>Dimensions</H6>";
			$va_dimension_pieces = array();
			if($t_object->get("ca_objects.dimensions.dimensions_height")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions.dimensions_height");
			}
			if($t_object->get("ca_objects.dimensions.dimensions_width")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions.dimensions_width");
			}
			if($t_object->get("ca_objects.dimensions.dimensions_depth")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions.dimensions_depth");
			}
			if($t_object->get("ca_objects.dimensions.dimensions_length")){
				$va_dimension_pieces[] = $t_object->get("ca_objects.dimensions.dimensions_length");
			}
			if(sizeof($va_dimension_pieces)){
				print join(" x ", $va_dimension_pieces);
			}
			if($t_object->get("ca_objects.dimensions.dimensions_weight")){
				print " ".$t_object->get("ca_objects.dimensions.dimensions_weight");
			}
		}
		
		if($va_materials = $t_object->get("ca_objects.material", array("returnAsArray" => true, "convertCodesToDisplayText" => false))){
			$i = 0;
			$va_materials_display = array();
			foreach($va_materials as $vn_material_id => $va_material){
				$vs_material = $t_lists->getItemFromListForDisplayByItemID("material", $va_material["material"]);
				if(trim($vs_materials)){
					$va_materials_display[] = caNavLink($this->request, $vs_material, "", "", "Browse", "Objects", array("facet" => "material_facet", "id" => $va_material["material"]));
				}
			}
			if(sizeof($va_materials_display)){
				print "<H6>"._t("Material")."</H6>";
				print join(", ", $va_materials_display);
			}
		}
?>
		{{{<ifdef code="ca_objects.description"><H6>Description</H6>^ca_objects.description</ifdef>}}}
<?php				
		if(trim($t_object->get("ca_objects.dimensions_frame.display_dimensions_frame")) || $t_object->get("ca_objects.dimensions_frame.dimensions_frame_height") || $t_object->get("ca_objects.dimensions.dimensions_height") || trim($t_object->get("ca_objects.dimensions.display_dimensions")) || trim($t_object->get("ca_objects.material")) || trim($t_object->get("description"))){
			print "<HR/>";
		}
		if($va_styles = $t_object->get("ca_objects.styles_movement", array("returnAsArray" => true, "convertCodesToDisplayText" => false))){
			$vs_style_display = "";
			$i = 0;
			foreach($va_styles as $vn_style_id => $va_style){
				$i++;
				$vs_style_movement = $t_lists->getItemFromListForDisplayByItemID("styles_movement", $va_style["styles_movement"]);
				if(trim($vs_style_movement)){
					$vs_style_display .= caNavLink($this->request, $vs_style_movement, "", "", "Browse", "Objects", array("facet" => "styles_movement_facet", "id" => $va_style["styles_movement"]));
					if($i < sizeof($va_styles)){
						$vs_style_display .= ", ";
					}
				}
			}
			if($vs_style_display){
				print "<H6>"._t("Style/Movement")."</H6>";
				print $vs_style_display;
			}
		}
		$va_collections = $t_object->get("ca_collections", array("returnAsArray" => true, "checkAccess" => $va_access_values, "sort" => "ca_collections.preferred_labels.name"));
		if(sizeof($va_collections)){
			print "<H6>Collection".((sizeof($va_collections) > 1) ? "s" : "")."</H6>";
			foreach($va_collections as $va_collection){
				print caDetailLink($this->request, $va_collection["name"], "", "ca_collections", $va_collection["collection_id"])."<br/>";
			}
		}
		if($va_themes = $t_object->get("ca_objects.steelcase_themes", array("returnAsArray" => true, "convertCodesToDisplayText" => false))){
			$i = 0;
			$vs_theme_display = "";
			foreach($va_themes as $vn_theme_id => $va_theme){
				$i++;
				$vs_theme = $t_lists->getItemFromListForDisplayByItemID("custom_subject", $va_theme["steelcase_themes"]);
				if(trim($vs_theme)){
					$vs_theme_display .= caNavLink($this->request, $vs_theme, "", "", "Browse", "Objects", array("facet" => "theme_facet", "id" => $va_theme["steelcase_themes"]));
					if($i < sizeof($va_themes)){
						$vs_theme_display .= ", ";
					}
				}
			}
			if($vs_theme_display){
				print "<H6>"._t("Themes")."</H6>";
				print $vs_theme_display;
			}
		}
		if($vs_style_display || $vs_theme_display || sizeof($va_collections)){
			print "<HR/>";
		}
?>
		{{{<ifdef code="ca_objects.idno"><H6>Steelcase Number</H6>^ca_objects.idno</ifdef>}}}			
<?php
		$va_storage_locations = $t_object->get("ca_storage_locations", array("returnAsArray" => true, "checkAccess" => $va_access_values));
		if(sizeof($va_storage_locations)){
			$t_location = new ca_storage_locations();
			$t_relationship = new ca_objects_x_storage_locations();
			$vn_now = date("Y.md");
			$va_location_display = array();
			foreach($va_storage_locations as $va_storage_location){
				$t_relationship->load($va_storage_location["relation_id"]);
				$va_daterange = $t_relationship->get("effective_daterange", array("rawDate" => true, "returnAsArray" => true));
				if(is_array($va_daterange) && sizeof($va_daterange)){
					foreach($va_daterange as $va_date){
						break;
					}
					#print $vn_now." - ".$va_date["effective_daterange"]["start"]." - ".$va_date["effective_daterange"]["end"];
					if(is_array($va_date)){
						if(($vn_now > $va_date["effective_daterange"]["start"]) && ($vn_now < $va_date["effective_daterange"]["end"])){
							# --- only display the top level from the hierarchy
							$va_hierarchy_ancestors = array_reverse(caExtractValuesByUserLocale($t_location->getHierarchyAncestors($va_storage_location["location_id"], array("includeSelf" => 1, "additionalTableToJoin" => "ca_storage_location_labels", "additionalTableSelectFields" => array("name")))));
							foreach($va_hierarchy_ancestors as $va_ancestor){
								$va_location_display[] = caNavLink($this->request, $va_ancestor["name"], "", "", "Browse", "Objects", array("facet" => "storage_location_facet", "id" => $va_ancestor["location_id"]));
								break;
							}
						}
					}
				}else{
					# --- only display the top level from the hierarchy
					$va_hierarchy_ancestors = array_reverse(caExtractValuesByUserLocale($t_location->getHierarchyAncestors($va_storage_location["location_id"], array("includeSelf" => 1, "additionalTableToJoin" => "ca_storage_location_labels", "additionalTableSelectFields" => array("name")))));
					foreach($va_hierarchy_ancestors as $va_ancestor){
						$va_location_display[] = caNavLink($this->request, $va_ancestor["name"], "", "", "Browse", "Objects", array("facet" => "storage_location_facet", "id" => $va_ancestor["location_id"]));
						break;
					}
					#$vs_location_display .= $va_storage_location["name"]."<br/>";
				}
			}
			if(sizeof($va_location_display)){
				print "<H6>Location</H6>".join(", ", $va_location_display);
			}
		}
?>
		{{{<ifdef code="ca_objects.credit_line"><H6>Credit</H6>&copy; ^ca_objects.credit_line</ifdef>}}}
<?php				
		if($vs_location_display || $t_object->get("ca_objects.idno") || trim($t_object->get("ca_objects.credit_line"))){
			print "<HR/>";
		}

?>
<?php print caDetailLink($this->request, _t("VIEW RECORD"), '', 'ca_objects',  $this->getVar("object_id")); ?>