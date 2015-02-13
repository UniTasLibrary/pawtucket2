<?php
	$va_set_items = $this->getVar("set_items");
	$t_set = $this->getVar("set");
	$vb_write_access = $this->getVar("write_access");
	
	$va_views			= $this->getVar('views');
	$vs_current_view	= $this->getVar('view');
	
	$va_export_formats = $this->getVar('export_formats');
	$va_lightbox_display_name = caGetSetDisplayName();
	$vs_lightbox_display_name = $va_lightbox_display_name["singular"];
	$vs_lightbox_display_name_plural = $va_lightbox_display_name["plural"];
?>
<div id="lbViewButtons">
<?php
if(is_array($va_views) && sizeof($va_views)){
	foreach($va_views as $vs_view => $va_view_info) {
		if ($vs_current_view === $vs_view) {
			print '<a href="#" class="active"><span class="glyphicon '.$va_view_info['icon'].'"></span></a> ';
		} else {
			print caNavLink($this->request, '<span class="glyphicon '.$va_view_info['icon'].'"></span>', 'disabled', '*', '*', '*', array('view' => $vs_view, 'set_id' => $t_set->get("set_id"))).' ';
		}
	}
}
?>
</div>	
<H1>
	<?php print $t_set->getLabelForDisplay(); ?>
	<div class="btn-group">
		<i class="fa fa-gear bGear" data-toggle="dropdown"></i>
		<ul class="dropdown-menu" role="menu">
			<li><?php print caNavLink($this->request, _t("All %1", $vs_lightbox_display_name_plural), "", "", "Sets", "Index"); ?></li>
			<li class="divider"></li>
<?php
		if($vb_write_access){
?>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'setForm', array("set_id" => $t_set->get("set_id"))); ?>"); return false;' ><?php print _t("Edit Name/Description"); ?></a></li>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'shareSetForm', array()); ?>"); return false;' ><?php print _t("Share %1", ucfirst($vs_lightbox_display_name)); ?></a></li>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'setAccess', array()); ?>"); return false;' ><?php print _t("Manage %1 Access", ucfirst($vs_lightbox_display_name)); ?></a></li>
<?php
		}
?>
			<li><?php print caNavLink($this->request, _t("Start presentation"), "", "", "Sets", "Present", array('set_id' => $t_set->getPrimaryKey())); ?></li>
<?php
			if(is_array($va_export_formats) && sizeof($va_export_formats)){
				// Export as PDF links
				print "<li class='divider'></li>\n";
				print "<li class='dropdown-header'>"._t("Download PDF as:")."</li>\n";
				foreach($va_export_formats as $va_export_format){
					print "<li>".caNavLink($this->request, $va_export_format["name"], "", "", "Sets", "setDetail", array("view" => "pdf", "download" => true, "export_format" => $va_export_format["code"]))."</li>";
				}
			}
?>		
			<li class="divider"></li>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'setForm', array()); ?>"); return false;' ><?php print _t("New %1", ucfirst($vs_lightbox_display_name)); ?></a></li>
			<li class="divider"></li>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'userGroupForm', array()); ?>"); return false;' ><?php print _t("New User Group"); ?></a></li>
<?php
			if(is_array($this->getVar("user_groups")) && sizeof($this->getVar("user_groups"))){
?>
			<li><a href='#' onclick='caMediaPanel.showPanel("<?php print caNavUrl($this->request, '', 'Sets', 'userGroupList', array()); ?>"); return false;' ><?php print _t("Manage Your User Groups"); ?></a></li>
<?php
			}
?>
		</ul>
	</div><!-- end btn-group -->
</H1>
	<div class="row">
		<div class="col-sm-9 col-md-9 col-lg-8">
<?php
		if(sizeof($va_set_items)){
			print $this->render("Sets/set_detail_{$vs_current_view}_html.php");
		}else{
			print "<div class='row'><div class='col-sm-12'>"._t("There are no items in this %1", $vs_lightbox_display_name)."</div></div>";
		}
?>		
		</div><!-- end col 10 -->
		<div class="col-sm-3 col-md-2 col-md-offset-1 col-lg-3 col-lg-offset-1 activitycol">
<?php
			if(!$vb_write_access){
				print _t("<div class='warning'>You may not edit this set, you have read only access.</div>");
			}
			#if($t_set->get("access")){
			#	print _t("This set is public")."<br/><br/>";
			#}
			if($t_set->get("description")){
				print $t_set->get("description");
				print "<HR>";
			}			
			$va_comments = array_reverse($this->getVar("comments"));
?>
			<div>
				<form action="<?php print caNavUrl($this->request, "", "Sets", "saveComment"); ?>" id="addComment" method="post">
<?php
				if($vs_comment_error = $this->getVar("comment_error")){
					print "<div>".$vs_comment_error."</div>";
				}
?>
					<div class="form-group">
						<textarea name="comment" placeholder="add your comment" class="form-control"></textarea>
					</div><!-- end form-group -->
					<div class="form-group text-right">
						<button type="submit" class="btn btn-default btn-xs">Save</button>
					</div><!-- end form-group -->
					<input type="hidden" name="tablename" value="ca_sets">
					<input type="hidden" name="item_id" value="<?php print $t_set->get("set_id"); ?>">
				</form>
			</div>
<?php
			if(sizeof($va_comments)){
?>
			<div class="lbSetCommentHeader"><?php print sizeof($va_comments)." ".((sizeof($va_comments) == 1) ? _t("comment") : _t("comments")); ?></div>
<?php
				if(sizeof($va_comments)){
					$t_author = new ca_users();
					print "<div class='lbComments'>";
					foreach($va_comments as $va_comment){
						print "<small>";
						# --- display link to remove comment?
						if($vb_write_access || ($va_comment["user_id"] == $this->request->user->get("user_id"))){
							print "<div class='pull-right'>".caNavLink($this->request, "<i class='fa fa-times' title='"._t("remove comment")."'></i>", "", "", "Sets", "deleteComment", array("comment_id" => $va_comment["comment_id"], "set_id" => $t_set->get("set_id"), "reload" => "detail"))."</div>";
						}
						$t_author->load($va_comment["user_id"]);
						print $va_comment["comment"]."<br/>";
						print "<small>".trim($t_author->get("fname")." ".$t_author->get("lname"))." ".date("n/j/y g:i A", $va_comment["created_on"])."</small>";
						print "</small><HR/>";
					}
					print "</div>";
				}
		
			}
?>
		</div><!-- end col-md-2 -->
	</div><!-- end row -->