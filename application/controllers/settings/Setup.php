<?php
/***********************************************************************/
/* Purpose 		: Application Environment Setting.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller{
	/* variable deceleration */
	private $_objDataOperation		= null;
	private $_strPrimaryTableName	= 'master_user_config';
	private $_strModuleName			= "Environment";
	private $_strModuleForm			= "frmEnvironmentSetting";
	private $_isAdmin				= 0;
	private $_intCompanyCode		= 0;
	private $_blnSetupStatus		= false;
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* CI call execution */
		parent::__construct();

		/* Creating model comment instance object */
		$this->_objDataOperation	= new Dbrequestprocess_model();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$strRecordsetArr	= array();
		
		/* getting logger details */
		$strLoggerArr 	= $this->_getLoggerDetails();
		$strSetupArr	= $this->_setSetupParameterAndDescription();
		$blnStatus		= true;
		
		/* Checking for instruction array */
		if(!empty($strSetupArr)){
			/* Iterating the loop */
			foreach($strSetupArr as $strSetupArrKey => $strSetupArrValue){
				/* checking for each item status */
				if(($blnStatus)){
					/* Setting the value */
					$blnStatus	= $strSetupArrValue['status'];
					/* terminate the loop */
					break;
				}
			}
		}
		
		
		/* if setup status is TRUE then do needful */
		if($blnStatus){
			/* Updating the configuration status */
			$this->_objDataOperation->setUpdateData(array('table'=>'master_company','data'=>array('is_setup_configured'=>1),'where'=>array('id'=>$this->_intCompanyCode)));
			/* Redirect to the login page */
			redirect(SITE_URL);
		}
		
		/* Load the environment list */
		$dataArr['body']	= $this->load->view('settings/setup', array('dataArr'=>$strSetupArr), true);
		
		/* Loading the template for browser rending */
		$this->load->view(BLANK_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	
	/**********************************************************************/
	/*Purpose 	: Getting the current logger details.
	/*Inputs	: None.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLoggerDetails(){
		/*Variable initialization */
		$strCookiesCode	= '';

		/* Checking is valid cookie exists */
		if(isset($_COOKIE['_xAyBzCwD'])){
			/* Getting the valid logger code */
			$strCookiesCode	= $_COOKIE['_xAyBzCwD'];
		}
		
		/* If logger code is not found the do needful */
		if($strCookiesCode == ''){
			/* redirecting to login */
			redirect(SITE_URL.'login');
		}
		
		/*Variable initialization */
		$strReturnArr	= array();
		
		/* getting the logger details */
		$strloggerArr	=  $this->_objDataOperation->getDataFromTable(array('table'=>'trans_logger','column'=>array('id','token','logger_data','user_code'),'where'=>array('token'=>$strCookiesCode)));
		
		/* Decoding the logger */
		$ObjStrLoggerDetails	= json_decode($strloggerArr[0]['logger_data']);
		$this->_intCompanyCode	= $ObjStrLoggerDetails->user_info->company_code;
		$this->_isAdmin			= $ObjStrLoggerDetails->user_info->is_admin;
		/* Global variable declaration */
		$this->load->vars(array(
									'userName'		=>$ObjStrLoggerDetails->user_info->user_name,
									'roleName'		=>$ObjStrLoggerDetails->user_info->role_name,
									'strMainMenu'	=>$ObjStrLoggerDetails->main_menu,
									'strChildMenu'	=>$ObjStrLoggerDetails->child_menu
							)
						);
		
		/* removed used variable */
		unset($strloggerArr, $ObjStrLoggerDetails);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setup the parameter description.
	/*Inputs	: None.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setSetupParameterAndDescription(){
		/* id normal user then do needful */
		if($this->_isAdmin == 0){
			return array('message'=>'Working environment is not set by Company / System Administrator. Kindly get touch with them. Once suggested setup / configuration steps done by them, system will start working automatically. In this you might be needs to get login one more time.');
		}
		
		/* return the list */
		return array(
						1=>array(
									'label'=>'Lead Source',
									'description'=>'This will helps you to identify, by which sources lead are came in. Lead Sources like Website, Facebook, Linkedin,Referral, Others, etc....<br/><b>How to setup:</b> Settings > Lead Source',
									'status'=>$this->_checkSource()
								),
						2=>array(
									'label'=>'Lead Status',
									'description'=>'To classified the lead current status, like active, closed or converted to prospect and its intermediate state.<br/><b>How to setup:</b> Settings > Lead Status',
									'status'=>$this->_checkStatus()
								),
						3=>array(
									'label'=>'Location',
									'description'=>'This will helps system to identity the where the business unit / entities are located. On based configured location, classifying user and this reporting structure. Location are configured based on ZONE (NOrth) - REGION (UP) - CITY (Lucknow) - LOCATION (BARANANKI) - BRANCH (BARK001) <br/><b>How to setup:</b> Settings > Locations',
									'status'=>$this->_checkLocation()
								),
						4=>array(
									'label'=>'Roles',
									'description'=>'This will helps system to identity user access / rights classification based on the configured roles.<br/><b>How to setup:</b> Settings > User Role',
									'status'=>$this->_checkRoles()
								),
						5=>array(
									'label'=>'User',
									'description'=>'Add new user in the system, associated with configured role, system role, location and reporting structure. <br/><b>How to setup:</b> Settings > User Profiles',
									'status'=>$this->_checkUser()
								),
						6=>array(
									'label'=>'Lead Attributes',
									'description'=>'Most import part, now you can configure the lead attributes like name, email, contact no, pan etc. Yes this subject to change business requirement. Using this configured lead attributes employee are enroll / update  the lead information.  <br/><b>How to setup:</b> Settings > Lead Attributes',
									'status'=>$this->_checkLeadAttributes()
								),
						7=>array(
									'label'=>'Lead attribute association on module',
									'description'=>'Once lead attribute is configured; pot now its time to associated the added lead attributes to modules. Like on lead / task / reports  module which lead attributes which get displayed.<br/><b>How to setup:</b> Settings > Module',
									'status'=>$this->_checkLeadAttributesModelAssocation()
								),
						8=>array(
									'label'=>'Module Access',
									'description'=>'Once role based setup, after that you can control the application feature / menu access / visibility gets controlled. <br/><b>How to setup:</b> Settings > Module Access',
									'status'=>$this->_checkModulesAccess()
								),
					);
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking lead source.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkSource(){
		/* getting lead source details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_lead_source','column'=>array('id'),'where'=>array('company_code'=>$this->_intCompanyCode),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking lead status.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkStatus(){
		/* getting lead source details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_status','column'=>array('id'),'where'=>array('company_code'=>array(1,$this->_intCompanyCode)),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking location.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkLocation(){
		/* getting lead location details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_location','column'=>array('location_type'),'where'=>array('company_code'=>$this->_intCompanyCode),'group'=>array('location_type')));
		/* Return the status */
		return (!empty($strDataSet) && (count($strDataSet) >= 5))?true:false;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking Roles.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkRoles(){
		/* getting lead location details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_role','column'=>array('id'),'where'=>array('company_code'=>$this->_intCompanyCode),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking users.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkUser(){
		/* getting lead location details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_user','column'=>array('id'),'where'=>array('company_code'=>$this->_intCompanyCode),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking lead attributes.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkLeadAttributes(){
		/* getting lead attributes details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_lead_attributes','column'=>array('id'),'where'=>array('company_code'=>$this->_intCompanyCode),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking lead module attributes association.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkLeadAttributesModelAssocation(){
		/* getting lead attributes details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(
																	array(
																			'table'=>array('mater_module_lead_attribute','master_lead_attributes'),
																			'join'=>array('','mater_module_lead_attribute.attri_code = master_lead_attributes.id'),
																			'column'=>array('mater_module_lead_attribute.id'),
																			'where'=>array('company_code'=>$this->_intCompanyCode),
																			'limit'=>0,
																			'offset'=>0
																		)
																);
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Checking module access to roles access.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkModulesAccess(){
		/* getting module access to roles details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'trans_module_access','column'=>array('id'),'where'=>array('company_code'=>$this->_intCompanyCode),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
}