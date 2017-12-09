<?php include_once('header.php'); ?>
	<div class="had-container">
		<div class="rows">
			<div class="col s12">
				<div class="col s10">
					<!-- Dropdown Structure -->
					<?php echo $strChildMenu ?>
					<nav>
			    		<div class="nav-wrapper">
			      			<a href="#" class="brand-logo"><img src="<?php echo SITE_URL.DEFAULT_LOGO?>" width="70px" height="70px" class="responsive-img"/></a>
							<?php echo $strMainMenu?>
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
		 
			<?php echo $body; ?>
		</div>
	</div>
<?php include_once('footer.php');?>