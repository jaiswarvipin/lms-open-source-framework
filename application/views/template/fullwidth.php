<?php include_once('header.php'); ?>
	<div class="had-container">
		<div class="rows">
			<div class="col s12">
				<div class="col s10">
					<!-- Dropdown Structure -->
					<?php echo $strChildMenu ?>
					
					<nav>
			    		<div class="nav-wrapper">
			      			<a href="javascript:void(0);" class="brand-logo"><img src="<?php echo SITE_URL.DEFAULT_LOGO?>" width="70px" height="70px" class="responsive-img"/></a>
							<a href="javascript:void(0);" data-activates="mobile" class="button-collapse"><i class="material-icons">menu</i></a>
							<?php echo $strMainMenu?>
							<?php echo $strMobileMenu ?>
							<ul id="userSettings" class="dropdown-content">
								<li><a href="<?php echo SITE_URL?>login/lougout">Logout</a></li>
							</ul>
			  				<ul id="nav-mobile" class="right hide-on-med-and-down">
			  					<li><a href="javascript:void(0);"><i class="material-icons">search</i></a></li>
			  					<li><img src="<?php echo SITE_URL.DEFAULT_USER_IMG?>" class="responsive-img circle pt10 tooltipped" width="50px" height="50px" data-position="bottom" data-delay="50" data-tooltip="<?php echo $userName.' | '.$roleName?>"/></li>
								<li><a href="javascript:void(0);" data-activates='userSettings' class="dropdown-button"><i class="material-icons">more_vert</i></a></li>
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
					<?php $strAddNewItemLabel = ((isset($moduleCustomTitle))&&($moduleCustomTitle != '')?$moduleCustomTitle:$moduleTitle);?>
					<!-- Dropdown Structure -->
					<ul id='dropdown1' class='dropdown-content dlActionList'>
						<?php if(!isset($noSearchAdd)){?>
							<?php if(($moduleForm != 'frmLeadReportSearch') && ($moduleForm != 'frmTaskReportSearch')  && ($moduleForm != 'frmCompany')){?>
								<li><a class="addItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',2);"><i class="material-icons">add_circle</i>Add New <?php echo $strAddNewItemLabel?></a></li>
								<li class="divider"></li>
							<?php }?>
							<?php if($moduleForm != ''){?>
								<li><a class="searchItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',3);"><i class="material-icons">search</i>Search</a></li>
								<li class="divider"></li>
							<?php }?>
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