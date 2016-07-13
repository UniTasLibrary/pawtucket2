<?php
/* ----------------------------------------------------------------------
 * views/pageFormat/pageHeader.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2014 Whirl-i-Gig
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
	$va_lightboxDisplayName = caGetLightboxDisplayName();
	$vs_lightbox_sectionHeading = ucFirst($va_lightboxDisplayName["section_heading"]);
	$va_classroomDisplayName = caGetClassroomDisplayName();
	$vs_classroom_sectionHeading = ucFirst($va_classroomDisplayName["section_heading"]);
	
	# Collect the user links: they are output twice, once for toggle menu and once for nav
	$va_user_links = array();
	if($this->request->isLoggedIn()){
		$va_user_links[] = '<li role="presentation" class="dropdown-header">'.trim($this->request->user->get("fname")." ".$this->request->user->get("lname")).', '.$this->request->user->get("email").'</li>';
		$va_user_links[] = '<li class="divider nav-divider"></li>';
		if(caDisplayLightbox($this->request)){
			$va_user_links[] = "<li>".caNavLink($this->request, $vs_lightbox_sectionHeading, '', '', 'Lightbox', 'Index', array())."</li>";
		}
		if(caDisplayClassroom($this->request)){
			$va_user_links[] = "<li>".caNavLink($this->request, $vs_classroom_sectionHeading, '', '', 'Classroom', 'Index', array())."</li>";
		}
		$va_user_links[] = "<li>".caNavLink($this->request, _t('User Profile'), '', '', 'LoginReg', 'profileForm', array())."</li>";
		$va_user_links[] = "<li>".caNavLink($this->request, _t('Logout'), '', '', 'LoginReg', 'Logout', array())."</li>";
	} else {	
		if (!$this->request->config->get('dont_allow_registration_and_login') || $this->request->config->get('pawtucket_requires_login')) { $va_user_links[] = "<li><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, '', 'LoginReg', 'LoginForm', array())."\"); return false;' >"._t("Login")."</a></li>"; }
		if (!$this->request->config->get('dont_allow_registration_and_login')) { $va_user_links[] = "<li><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, '', 'LoginReg', 'RegisterForm', array())."\"); return false;' >"._t("Register")."</a></li>"; }
	}
	$vb_has_user_links = (sizeof($va_user_links) > 0);

?><!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	<?php print MetaTagManager::getHTML(); ?>
	<?php print AssetLoadManager::getLoadHTML($this->request); ?>


	<title><?php print (MetaTagManager::getWindowTitle()) ? MetaTagManager::getWindowTitle() : $this->request->config->get("app_display_name"); ?></title>
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
    		jQuery('#browse-menu').on('click mouseover mouseout mousemove mouseenter',function(e) { e.stopPropagation(); });
    	});
	</script>
<?php
	if(Debug::isEnabled()) {		
		//
		// Pull in JS and CSS for debug bar
		// 
		$o_debugbar_renderer = Debug::$bar->getJavascriptRenderer();
		$o_debugbar_renderer->setBaseUrl(__CA_URL_ROOT__.$o_debugbar_renderer->getBaseUrl());
		print $o_debugbar_renderer->renderHead();
	}
?>
</head>
<body>
	<nav class="navbar navbar-default yamm" role="navigation">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
<?php
	if ($vb_has_user_links) {
?>
				<button type="button" class="navbar-toggle navbar-toggle-user" data-toggle="collapse" data-target="#user-navbar-toggle">
					<span class="sr-only">User Options</span>
					<span class="glyphicon glyphicon-user"></span>
				</button>
<?php
	}
?>
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-main-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
<?php
				print caNavLink($this->request, caGetThemeGraphic($this->request, 'VHEC_LogoWordMark_Black.jpg'), "navbar-brand", "", "","");
?>
				<div class='collections'>Collections</div>
			</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
			<!-- bs-user-navbar-collapse is the user menu that shows up in the toggle menu - hidden at larger size -->
<?php
	if ($vb_has_user_links) {
?>
			<div class="collapse navbar-collapse" id="user-navbar-toggle">
				<ul class="nav navbar-nav">
					<?php print join("\n", $va_user_links); ?>
				</ul>
			</div>
<?php
	}
?>
			<div class="collapse navbar-collapse" id="bs-main-navbar-collapse-1">
<?php
	if ($vb_has_user_links) {
?>	
				<ul class="nav navbar-nav navbar-right" id="user-navbar">
					<li class="dropdown" style="position:relative;">
						<a href="#" class="dropdown-toggle icon" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span></a>
						<ul class="dropdown-menu"><?php print join("\n", $va_user_links); ?></ul>
					</li>
					<li class="dropdown" style="position:relative;">
						<a href="#" class="dropdown-toggle icon" data-toggle="dropdown"><span class="glyphicon glyphicon-info-sign"></span></a>
						<ul class="dropdown-menu">
<?php						
							print "<li>".caNavLink($this->request, 'About The Collections', '', '', 'About', 'Index')."</li>\n"; 
							print "<li>".caNavLink($this->request, 'Plan a Research Visit', '', '', 'About', 'Index')."</li>\n"; 							
							print "<li>".caNavLink($this->request, 'Use and Licensing', '', '', 'About', 'Index')."</li>\n"; 							
							print "<li>".caNavLink($this->request, 'Connect With Us', '', '', 'About', 'Index')."</li>\n"; 
							print "<li>".caNavLink($this->request, 'User Guides', '', '', 'About', 'userguide')."</li>\n"; 
							print "<li>".caNavLink($this->request, 'Project Funding', '', '', 'About', 'funding')."</li>\n";
?>						
						</ul>
					</li>					
				</ul>
<?php
	}
?>

				<form class="navbar-form navbar-right" role="search" action="<?php print caNavUrl($this->request, '', 'MultiSearch', 'Index'); ?>">
					<div class="formOutline">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Search the Collections" name="search">
						</div>
						<button type="submit" class="btn-search"><span class="glyphicon glyphicon-search"></span></button>
					</div>
					<div class='advSearch'><?php print caNavLink($this->request, _t("advanced search"), "", "", "Search", "advanced/objects"); ?></div>

				</form>

				<ul class="nav navbar-nav navbar-left">
<?php	
					
					print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Collections <span class='caret'></span></a>\n";
					print "<ul class='dropdown-menu'>\n";
					print "<li>".caNavLink($this->request, _t("Browse All"), "", "", "Browse", "landing")."</li>";			
					print "<li>".caNavLink($this->request, 'Archives', 'first', '', 'Archives', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'Library', '', '', 'Library', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'Museum', '', '', 'Museum', 'Index')."</li>\n";
					print "<li>".caNavLink($this->request, 'Testimony', '', '', 'Testimony', 'Index')."</li>\n";
					print "</ul>";
					print "</li>";	
								
					#print $this->render("pageFormat/browseMenu.php"); 	
					print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Resources <span class='caret'></span></a>\n";
					print "<ul class='dropdown-menu'>\n";
					print "<li>".caNavLink($this->request, 'For Educators', 'first', '', 'About', 'Index')."</li>\n"; 					
					print "<li>".caNavLink($this->request, 'For Students', '', '', 'About', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'For Researchers', '', '', 'About', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'CIC', 'first', '', 'About', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'Finding Aids', 'first', 'FindingAid', 'Collection', 'Index')."</li>\n";
					print "<li>".caNavLink($this->request, 'Research Guides', '', '', 'About', 'researchguide')."</li>\n"; 
					
					print "</ul>";
					print "</li>";	
					
					print "<li>".caNavLink($this->request, _t("Featured"), "", "", "Gallery", "featured")."</li>";				
					
					print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Exhibitions <span class='caret'></span></a>\n";
					print "<ul class='dropdown-menu'>\n";
					print "<li><a href='http://www.vhec.org/currentexhibits.html' target='_blank'>Current</a></li>\n";
					print "<li>".caNavLink($this->request, 'Past', '', '', 'About', 'Index')."</li>\n"; 
					print "<li>".caNavLink($this->request, 'Online', '', '', 'Gallery', 'Index')."</li>\n"; 
					print "</ul>";
					print "</li>";	
								
					
					print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Contribute <span class='caret'></span></a>\n";
					print "<ul class='dropdown-menu'>\n";
					print "<li><a href='https://www.canadahelps.org/dn/15211' target='_blank'>Support the VHEC</a></li>\n";
					print "<li>".caNavLink($this->request, 'Donate Materials', '', '', 'About', 'donate')."</li>\n"; 
					print "</ul>";
					print "</li>";														
					
					  
?>					

				</ul>

			</div><!-- /.navbar-collapse -->
		</div><!-- end container -->
	</nav>
				
<?php
	if (($this->request->getController() == "Museum") | (($this->request->getController() == "Browse") && ($this->request->getAction() == "museum"))) {
		print "<div class='container submenu museum '><div class='row'>";
		print "<div class='col-sm-12'>";
		print "<ul class='nav navbar-nav navbar-left'>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>About <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'The Museum Collection', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Information For Donors', 'last', '', 'About', 'donate/#museum')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Explore <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Advanced Search', 'first', '', 'Search', 'advanced/objects')."</li>\n";
		print "<li>".caNavLink($this->request, 'Browse', 'last', '', 'Browse', 'museum')."</li>\n"; 
		print "</ul>";
		print "</li>";		

		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Learn <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Museum Works in the Classroom', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Digital Museum', 'last', '', 'About', 'Index')."</li>\n"; 
		print "</ul>";
		print "</li>";

		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Research <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Using the Museum Collection', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Research Guides', 'last', '', 'About', 'researchguide/#museum')."</li>\n"; 
		print "</ul>";
		print "</li>";	
				
		print "</ul>";
		print "</div>";
		print "</div></div>";
	} 	elseif (($this->request->getController() == "Archives") | ($this->request->getController() == "Collection")) {
		print "<div class='container submenu museum '><div class='row'>";
		print "<div class='col-sm-12'>";
		print "<ul class='nav navbar-nav navbar-left'>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>About <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'The Archives', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Information for Donors', 'last', '', 'About', 'donate/archives')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Explore <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Finding Aids', 'first', 'FindingAid', 'Collection', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Advanced Search', '', '', 'Search', 'advanced/objects')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Browse', 'last', '', 'Browse', 'archives')."</li>\n"; 		
		print "</ul>";
		print "</li>";		
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Learn <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Archives in the Classroom', 'first', '', 'About', 'Index')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Digital Museum', 'last', '', 'About', 'Index')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Research <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Using the Archives', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Reseach Guides', 'last', '', 'About', 'researchguide/#archives')."</li>\n"; 
		print "</ul>";
		print "</li>";			
				
		print "</ul>";
		print "</div>";
		print "</div></div>";
	} elseif ($this->request->getController() == "Library") {
		print "<div class='container submenu museum library '><div class='row'>";
		print "<div class='col-sm-12'>";
		print "<ul class='nav navbar-nav navbar-left'>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>About <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Library Collection', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Information for Donors', 'last', '', 'About', 'donate/#library')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Explore <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Advanced Search', 'first', '', 'Search', 'advanced/objects')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Browse', 'last', '', 'Browse', 'library')."</li>\n"; 		
		print "</ul>";
		print "</li>";		
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Learn <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Library Resources in the Classroom', 'first last', '', 'About', 'Index')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Research <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Guide to Using the Library', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Research Request', '', '', 'About', 'Index')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Reseach Guides', 'last', '', 'About', 'researchguide/#library')."</li>\n"; 
		print "</ul>";
		print "</li>";			
				
		print "</ul>";
		print "</div>";
		print "</div></div>";	
	} elseif ($this->request->getController() == "Testimony") {
		print "<div class='container submenu museum testimony '><div class='row'>";
		print "<div class='col-sm-12'>";
		print "<ul class='nav navbar-nav navbar-left'>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>About <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'The Testimony Collection', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'History of the Collection', '', '', 'About', 'Index')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Holocaust Documentation Project Timeline', '', '', 'Featured', 'Index/theme/823')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Information for Donors', 'last', '', 'About', 'donate/#testimony')."</li>\n"; 
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Explore <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Advanced Search', 'first', '', 'Search', 'advanced/objects')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'Browse', 'last', '', 'Browse', 'testimony')."</li>\n"; 				
		print "</ul>";
		print "</li>";		
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Learn <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Testimony in the Classroom', 'first', '', 'About', 'Index')."</li>\n"; 
		print "<li>".caNavLink($this->request, 'CIC site', 'last', '', 'About', 'Index')."</li>\n"; 		
		print "</ul>";
		print "</li>";
		
		print "<li class='dropdown' style='position:relative;'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Research <span class='caret'></span></a>\n";
		print "<ul class='dropdown-menu'>\n";
		print "<li>".caNavLink($this->request, 'Using the Testimonies', 'first', '', 'About', 'Index')."</li>\n";
		print "<li>".caNavLink($this->request, 'Testimony Reseach Guides', 'last', '', 'About', 'researchguide/#testimony')."</li>\n"; 
		print "</ul>";
		print "</li>";			
				
		print "</ul>";
		print "</div>";
		print "</div></div>";	
	}
?>				
					
	<div class="container"><div class="row"><div class="col-xs-12">
		<div id="pageArea" <?php print caGetPageCSSClasses(); ?>>