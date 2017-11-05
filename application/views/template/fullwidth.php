<?php include_once('header.php'); ?>
	<div class="had-container">
		<div class="rows">
			<div class="col s12">
				<div class="col s10">
					<!-- Dropdown Structure -->
					<?php echo $strChildMenu ?>
					<!-- ul id="dropdown1" class="dropdown-content">
					  <li><a href="#!">Lead</a></li>
					  <li class="divider"></li>
					  <li><a href="#!">Task</a></li>
					</ul>
					<ul id="dropdown2" class="dropdown-content">
					  <li><a href="#!">Company</a></li>
					  <li><a href="#!">Department</a></li>
					  <li class="divider"></li>
					  <li><a href="#!">Mail</a></li>
					  <li><a href="#!">SMS</a></li>
					  <li class="divider"></li>
					  <li><a href="<?php echo SITE_URL?>settings/modules">Modules</a></li>
					  <li><a href="<?php echo SITE_URL?>settings/modulesaccess">Modules Access</a></li>
					  <li class="divider"></li>
					  <li><a href="<?php echo SITE_URL?>settings/status">Lead Status</a></li>
					  <li><a href="<?php echo SITE_URL?>settings/leadattributes">Leads Attributes</a></li>
					  <li><a href="<?php echo SITE_URL?>settings/leadsource">Leads Sources</a></li>
					  <li class="divider"></li>
					  <li><a href="<?php echo SITE_URL?>settings/locations">Locations</a></li>
					  <li class="divider"></li>
					  <li><a href="<?php echo SITE_URL?>settings/userrole">User Role</a></li>
					  <li><a href="<?php echo SITE_URL?>settings/userprofiles">User Profile</a></li>
					  <li class="divider"></li>
					  <li><a href="#!">Integration</a></li>
					</ul-->
					<nav>
			    		<div class="nav-wrapper">
			      			<a href="#" class="brand-logo"><img src="<?php echo SITE_URL.DEFAULT_LOGO?>" width="70px" height="70px" class="responsive-img"/></a>
							<?php echo $strMainMenu?>
			  				<!--ul id="nav-mobile" class="hide-on-med-and-down">
			    				<li class="w100">&nbsp;</li>
			    				<li class="active"><a href="<?php echo SITE_URL?>dashboard">Dashboard</a></li>
			    				<li><a href="<?php echo SITE_URL?>leads/leads">Leads</a></li>
			    				<li><a href="<?php echo SITE_URL?>leads/tasks">Task</a></li>
			    				<!-- Dropdown Trigger ->
								<li><a class="dropdown-button" href="#!" data-activates="dropdown1">Reports<i class="material-icons right">arrow_drop_down</i></a></li>
								<li><a class="dropdown-button" href="#!" data-activates="dropdown2">Settings&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="material-icons right">arrow_drop_down</i></a></li>
			  				</ul-->

			  				<ul id="nav-mobile" class="right hide-on-med-and-down">
			  					<li><a href="sass.html"><i class="material-icons">search</i></a></li>
			  					<li><img src="<?php echo SITE_URL.DEFAULT_USER_IMG?>" class="responsive-img circle pt10" width="50px" height="50px"/></li>
								<li><a href="mobile.html"><i class="material-icons">more_vert</i></a></li>
			  				</ul>
						</div>
			  		</nav>
			  	</div>
			  	<div class="col s1"></div>
			</div>
		</div>
		<div class="main-container">
			<div class="row">
				<div class="col s6"><h5><?php echo $moduleTitle;?></h5></div>
				<div class="col s6 right" style="margin:-10 0 0 0 !important">
					<!-- Dropdown Trigger -->
					<a class='dropdown-button btn right w200 aActionContainer' href='javascript:void(0);' data-activates='dropdown1'><i class="material-icons"></i>Action</a>

					<!-- Dropdown Structure -->
					<ul id='dropdown1' class='dropdown-content dlActionList'>
						<?php if(!isset($noSearchAdd)){?>
							<?php if(($moduleForm != 'frmLeadReportSearch') && ($moduleForm != 'frmTaskReportSearch')){?>
								<li><a class="addItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',2);"><i class="material-icons">add_circle</i>Add New Lead</a></li>
								<li class="divider"></li>
							<?php }?>
							<li><a class="searchItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',3);"><i class="material-icons">search</i>Search</a></li>
							<li class="divider"></li>
						<?php }?>
						<?php if(($moduleForm == 'frmLeads') || ($moduleForm == 'frmTask')){?>
							<li><a href="javascript:void(0);" onclick="openEditModel('divlLeadTransfer','selected',4);"><i class="material-icons">transfer_within_a_station</i>Lead(s) Transfer</a></li>
							<li class="divider"></li>
							<li><a href="#!"><i class="material-icons">email</i>Mass Email</a></li>
							<li class="divider"></li>
							<li><a href="javascript:void(0);" onclick="openEditModel('divlLeadFolloupDetails','selected',4);"><i class="material-icons">update</i>Mass Update</a></li>
						<?php }?>
					</ul>
				</div>
			</div>
			<?php echo $body; ?>
			<?php echo getDeleteConfirmation($deleteUri);?>
			<?php echo getEditContentForm($getRecordByCodeUri);?>
			<?php echo getFormStrecture($moduleUri,'frmModuleSearch');?>
			<?php if(isset($strCustomUri)) { echo getFormStrecture($strCustomUri,'frmCustom');};?>
			<?php echo getFormStrecture('','frmDynamicEventDataSet');?>
			<span class="hide" name="txtSearchFilters" id="txtSearchFilters"><?php echo $strSearchArr?></span>
		</div>
	</div>
<?php include_once('footer.php');?>