<?php
/***********************************************************************/
/* Purpose 		: Request and Logger processing.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Requestprocess extends CI_Controller {
	/* variable deceleration */
	public $_objDataOperation				= null;
	public $_objForm						= null;
	private $_intUserCode					= 0;
	private $_intCompanyCode				= 0;
	private $_intAdminCode 					= 0;
	private $_strMainModule					= '';
	private $_strChildModule				= '';
	private $_strRegionArr					= array();
	private $_strBranchArr					= array();
	private $_leadAttriArr					= array();
	private $_strLeadSourceArr				= array();
	private $_strLeadStatusArr				= array();

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

		/* Process the logger request */
		$this->_doValidateRequest();

		/* Creating form helper object */
		$this->_objForm				= new Form();
	}

	/**********************************************************************/
	/*Purpose 	: Validating the current logger status.
	/*Inputs	: None.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doValidateRequest(){
		/*Variable initialization */
		$strCookiesCode	= '';

		/* Checking is valid cookie exists */
		if(isset($_COOKIE['_xAyBzCwD'])){
			/* Getting the valid logger code */
			$strCookiesCode	= $_COOKIE['_xAyBzCwD'];
		}

		/* If logger code is not found the do needful */
		if($strCookiesCode == ''){
			/* Destroy the all cookies */
			$this->_doDistryLoginCookie();

			/* redirecting to login */
			redirect(SITE_URL.'login', 'refresh');
		}else{
			/* getting logger details */
			$strLoggerArr 	= $this->_getLoggerDetails($strCookiesCode);
			
			/* Logger details not found then do needful */
			if(empty($strLoggerArr)){
				/* Destroy the all cookies */
				$this->_doDistryLoginCookie();
			}
			/* Processing the logger Object */
			$this->_doProcessLogger($strLoggerArr);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Process the logger data.
	/*Inputs	: $pStrLoggerDetailsArr	= Logger Details array.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doProcessLogger($pStrLoggerDetailsArr = array()){
		/* if logger object is empty then do needful */
		if(empty($pStrLoggerDetailsArr)){
			/* Destroy the all cookies */
			$this->_doDistryLoginCookie();

			/* redirecting to login */
			redirect(SITE_URL.'login', 'refresh');
		}
		/* Decoding the logger */
		$ObjStrLoggerDetails	= json_decode($pStrLoggerDetailsArr[0]['logger_data']);
		
		/* Logger variable declaration */
		$strLoggerName				= $ObjStrLoggerDetails->user_info->name;
		$this->_intUserCode			= $ObjStrLoggerDetails->user_info->id;
		$this->_intCompanyCode		= $ObjStrLoggerDetails->user_info->company_code;
		$this->_intAdminCode		= $ObjStrLoggerDetails->user_info->is_admin;
		$this->_strMainModule		= $ObjStrLoggerDetails->main_menu;
		$this->_strChildModule		= $ObjStrLoggerDetails->child_menu;
		$this->_strRegionArr		= (array)$ObjStrLoggerDetails->region;
		$this->_strBranchArr		= (array)$ObjStrLoggerDetails->branch;
		$this->_leadAttriArr		= (array)$ObjStrLoggerDetails->leadAttr;
		$this->_strLeadSourceArr	= (array)$ObjStrLoggerDetails->leadSource;
		$this->_strLeadStatusArr	= (array)$ObjStrLoggerDetails->leadStatus;
		
		/* Global variable declaration */
		$this->load->vars(array(
									'userName'		=>$strLoggerName,
									'strMainMenu'	=>$this->_strMainModule,
									'strChildMenu'	=>$this->_strChildModule
							)
						);

		/* removed used variables */
		unset($ObjStrLoggerDetails);
	}

	/**********************************************************************/
	/*Purpose 	: get logger user code.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getUserCode(){
		/* return user code */
		return $this->_intUserCode;
	}

	/**********************************************************************/
	/*Purpose 	: get logger company code.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getCompanyCode(){
		/* return company code */
		return $this->_intCompanyCode;
	}

	/**********************************************************************/
	/*Purpose 	: get user admin flag.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getAdminFlag(){
		/* return company code */
		return $this->_intAdminCode;
	}

	/**********************************************************************/
	/*Purpose 	: Distroy the existing logger cookies.
	/*Inputs	: None.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doDistryLoginCookie(){
		/* Creating logger object */
		$objLogger	= new Logger();
		/* Logger object registration request */
		$objLogger->doDistryLoginCookie();
		/* Removed used variable */
		unset($objLogger);
	}

	/**********************************************************************/
	/*Purpose 	: Getting the current logger details.
	/*Inputs	: $pStrCookiesCode :: Logger token code.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLoggerDetails($pStrCookiesCode = ''){
		/*Variable initialization */
		$strReturnArr	= array();
		
		/* If logger code is not found the do needful */
		if($pStrCookiesCode == ''){
			/* return empty set */
			return $strReturnArr;
		}
		/* getting the logger details */
		$strloggerArr	=  $this->_objDataOperation->getDataFromTable(array('table'=>'trans_logger','column'=>array('id','token','logger_data','user_code'),'where'=>array('token'=>$pStrCookiesCode)));

		/* Return the logger details */
		return $strloggerArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: get module access main menu.
	/*Inputs	: None.
	/*Returns	: Main Menu.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getMainModule(){
		/* return main menu */
		return $this->_strMainModule;
	}
	
	/**********************************************************************/
	/*Purpose 	: get child module access main menu.
	/*Inputs	: None.
	/*Returns	: Child  Menu.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getChildModule(){
		/* return main menu */
		return $this->_strChildModule;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get branch key array.
	/*Inputs	: None.
	/*Returns	: Branch Key as array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getBranchCodes(){
		/* return branch code as array */
		return array_keys($this->_strBranchArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get branch details.
	/*Inputs	: None.
	/*Returns	: Branch details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getBranchDetails(){
		/* return branch details */
		return $this->_strBranchArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get region details.
	/*Inputs	: None.
	/*Returns	: Region details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getRegionDetails(){
		/* return region details */
		return $this->_strRegionArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead attribute details.
	/*Inputs	: None.
	/*Returns	: Lead attributes details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadAttributeList(){
		/* return branch details */
		return $this->_leadAttriArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead source details.
	/*Inputs	: None.
	/*Returns	: Lead source details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadSource(){
		/* return branch details */
		return $this->_strLeadSourceArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead status details.
	/*Inputs	: None.
	/*Returns	: Lead status details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadStatus(){
		/* return lead status details */
		return $this->_strLeadStatusArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead status details.
	/*Inputs	: None.
	/*Returns	: Lead status details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadStatusInParentChildArr(){
		/* Variable initialization */
		$strReturnsArr	= array();
		/* Status error is empty then do needful */
		if(empty($this->_strLeadStatusArr)){
			/* Return array */
			return $strReturnsArr;
		}
		
		/* Iterating the loop */
		foreach($this->_strLeadStatusArr as $strLeadStatusArrKey => $strLeadStatusArrValue){
			/* Parent Status Code */
			if($strLeadStatusArrValue->parent_id == '-1'){
				/* Setting parent */
				$strReturnsArr[$strLeadStatusArrKey]['name']	 = $strLeadStatusArrValue->description;
			}else if(isset($strReturnsArr[getEncyptionValue($strLeadStatusArrValue->parent_id)])){
				$strParentCode	= getEncyptionValue($strLeadStatusArrValue->parent_id);
				/* Setting the child array */
				$strReturnsArr[$strParentCode]['child'][$strLeadStatusArrKey] = $strLeadStatusArrValue->description;///$strReturnsArr[getEncyptionValue($strLeadStatusArrValue->parent_id)]. ' - '.$strLeadStatusArrValue->description;
			}
		}
		/* Return status */
		return $strReturnsArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Module field array.
	/*Inputs	: $pStrModuleURL = Module URL.
	/*Returns	: Module list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuleAssociatedFieldByModuleURL($pStrModuleURL = ''){
		/* Variable initialization */
		$strReturnArr	= array();
		
		/* If module URL is empty then do needful */
		if($pStrModuleURL == ''){
			/* Return Empty Array */
			return $strReturnArr;
		}
		
		/* Creating lead object */
		$leadObj = new Lead($this->_objDataOperation, $this->getCompanyCode());
		/* Getting lead attributes */
		$strReturnArr	= $leadObj->getLeadAttributesListByModuleUrl($pStrModuleURL);
		/* Removed used variable */
		unset($leadObj);
		
		/* return Filed array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead Attribute details by attribute key.
	/*Inputs	: $pStrKeyValue :: attribute code,
				: $pStrValue :: Value
	/*Returns	: Lead Attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadAttributeDetilsByAttributeKey($pStrKeyValue = '', $pStrValue = ''){
		/* variable initialization */
		$strKeyValue	= '';
		
		/* checking is key value passed */
		if($pStrKeyValue == ''){
			/* Return the empty value */
			return $pStrValue;
		}
		/* Getting lead attribute array */
		$strLeadAttriArr	= $this->getLeadAttributeList();
		
		/* If lead array not found then do needful */
		if(empty($strLeadAttriArr)){
			/* return empty string */
			return $pStrValue;
		}
		/* if lead attribute found */
		if(isset($strLeadAttriArr[$pStrKeyValue])){
			if($strLeadAttriArr[$pStrKeyValue]->options == ''){
				$strKeyValue	= $pStrValue;
			}else{
				/* key value */
				$strKeyValue	= $strLeadAttriArr[$pStrKeyValue]->label;
			}
		}else {
			/* Getting predefine mandatory attributes of operations */
			switch($pStrKeyValue){
				case 'branch_code':
					$strKeyValue	= isset($this->_strBranchArr[getEncyptionValue($pStrValue)])?$this->_strBranchArr[getEncyptionValue($pStrValue)]:'-';
					break;
				case 'region_code':
					$strKeyValue	= isset($this->_strRegionArr[getEncyptionValue($pStrValue)])?$this->_strRegionArr[getEncyptionValue($pStrValue)]:'-';
					break;
				case 'leadSource':
					$strKeyValue	= isset($this->_strLeadSourceArr[getEncyptionValue($pStrValue)])?$this->_strLeadSourceArr[getEncyptionValue($pStrValue)]:'-';
					break;
				case 'lead_owner_code':
					$strKeyValue	= isset($this->_strLeadSourceArr[getEncyptionValue($pStrValue)])?$this->_strLeadSourceArr[getEncyptionValue($pStrValue)]:$pStrValue;
					break;
				case 'status_code':
					$strKeyValue	= isset($this->_strLeadStatusArr[getEncyptionValue($pStrValue)])?$this->_strLeadStatusArr[getEncyptionValue($pStrValue)]->description:'-';
					break;
				default:
					$strKeyValue	= $pStrValue;
			}
		}
		
		/* Return Value */
		return $strKeyValue;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get new panel view.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getNewLeadPanel(){
		/* Checking lead operation class is loaded */
		if ( ! class_exists('LeadsOperation')){
			/* If not then add include it */
           require_once(APPPATH.'controllers\leadsoperation\LeadsOperation.php');
        }

		/* Creating lead operation object */		
		$leadsOperationObj = new LeadsOperation();
		/* Getting new panel HTML */
		$strNewPanelHTML   =  $leadsOperationObj->getNewLeadPanel();
		/* Removed used variables */
		unset($leadsOperationObj);
		/* Return Add New Lead HTML */
		return $strNewPanelHTML;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead follow-up panel view.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadFollowDetails(){
		/* Checking lead operation class is loaded */
		if ( ! class_exists('LeadsOperation')){
			/* If not then add include it */
           require_once(APPPATH.'controllers\leadsoperation\LeadsOperation.php');
        }

		/* Creating lead operation object */		
		$leadsOperationObj = new LeadsOperation();
		/* Getting lead follow-up panel HTML */
		$strleadFolluUpPanelHTML   =  $leadsOperationObj->getLeadFollowDetails();
		/* Removed used variables */
		unset($leadsOperationObj);
		/* Return lead follow-up HTML */
		return $strleadFolluUpPanelHTML;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get module column as search panel.
	/* Inputs 	: $pStrColumnArray :: Column Array,
				: $pStrAction : Search method.
	/* Returns	: Search HTML of respective panel.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getColumnAsSearchPanel($pStrColumnArray, $pStrAction = ''){
		/* Creating widget panel */
		$widgetObj	 	= new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* get Search panel HTML */
		$strSearchHTML	= $widgetObj->getColumnAsSearchPanel(array_merge($pStrColumnArray,array('action'=>$pStrAction)));
		/* Removed used variables */
		unset($widgetObj);
		
		/* Return HTML */
		return $strSearchHTML;
	}
}