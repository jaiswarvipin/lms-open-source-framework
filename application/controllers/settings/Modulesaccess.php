<?php
/***********************************************************************/
/* Purpose 		: Application module Access management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Modulesaccess extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_role';
	private $_strModuleName			= "Modules Access";
	private $_strModuleForm			= "frmModulesAccess";
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();
		/* Getting current page number */
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting modules access list */
		$strDataArr['dataSet'] 				= $this->_getModulesDetails(0,'',false,false, $intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getModulesDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getModuesAccessDetailsByCode';
		$strDataArr['strDataAddEditPanel']	= 'moduleAccessModel';
		$strDataArr['noSearchAdd']			= 'yes';
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strDataArr['strModuleArr']			= $this->_getModuleList();	
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/moduleaccess', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get module acesss details by code.
	/*Inputs	: None.
	/*Returns 	: Module Access details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuesAccessDetailsByCode(){
		/* Setting the module access code */
		$intRoleCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strModulesArr		= array();
		
		/* Checking the module access code shared */
		if($intRoleCode > 0){
			/* getting requested module code details */
			$strModulesArr	= $this->_getModulesAccessDetails($intRoleCode);

			/* if record not found then do needful */
			if(empty($strModulesArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strModulesArr, true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid role code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the module details.
	/*Inputs	: $pModuleCode :: Module code,
				: $pStrModuleName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getModulesDetails($pModuleCode = 0, $pStrModuleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strDataArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pModuleCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the module list */
		$strModuleArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		$strModuleArr[0]['role_code'] = $pModuleCode;
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strModuleArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting module access details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuesAccessDetails(){
		/* variable initialization */
		$intRoleCode		= ($this->input->post('txtRoleCode') != '')? getDecyptionValue($this->input->post('txtRoleCode')):0;
		$strModuleAccessArr	= ($this->input->post('txtModulename') != '')?$this->input->post('txtModulename'):array();
		$blnEditRequest		= (($intRoleCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr	= array();
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strModuleAccessArr == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Module(s) is not selected for same role.'), true);
		}
		
		/* Data Container */
		$strDataArr	= array(
								'table'=>'trans_module_access',
								'where'=>array('role_code'=>$intRoleCode),
								'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode()
											)
						);
						
		/* Deactiviting the data */
		$this->_objDataOperation->setUpdateData($strDataArr);
		
		/* Iterating the loop */
		foreach($strModuleAccessArr as $strModuleAccessArrKey => $strModuleAccessArrValue){
			/* if value is set then do needful */
			if($strModuleAccessArrValue != ''){
				/* Data Container */
				$strDataArr	= array(
										'table'=>'trans_module_access',
										'data'=>array(
														'role_code'=>$intRoleCode,
														'module_code'=>getDecyptionValue($strModuleAccessArrValue),
														'company_code'=>$this->getCompanyCode(),
														'updated_by'=>$this->getUserCode()
													)
								);
								
				/* Deactiviting the data */
				$this->_objDataOperation->setDataInTable($strDataArr);
			}
		}
			
		/* Removed used variables */
		unset($strDataArr);
		
		/* Return the array */
		jsonReturn(array('status'=>1,'message'=>'Module Access added successfully.'), true);
	}

	/**********************************************************************/
	/*Purpose 	: Getting module list.
	/*Inputs	: None.
	/*Returns 	: Module list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getModuleList(){
		/* Variable initialization */
		$strReturnArr	= array();
		
		/* Creatig module object */
		$moduleObj	= new Module($this->_objDataOperation, $this->getCompanyCode());
		/* getting module list array */
		$strModuleArr	= $moduleObj->getModulesByCode();
		/* Removed used variables */
		unset($moduleObj);
		
		/* if module array is not empty then do needful */
		if(!empty($strModuleArr)){
			/* iterating the loop */
			foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
				/* chceking for parent code */
				if(($strModuleArrValue['parent_code'] == 0) && (!isset($strReturnArr[$strModuleArrValue['id']]))){
					/* Setting the parent Modules */
					$strReturnArr[$strModuleArrValue['id']]['description']	= $strModuleArrValue['description'];
					$strReturnArr[$strModuleArrValue['id']]['id']	= $strModuleArrValue['id'];
				}else{
					/* set the result set */
					$strReturnArr[$strModuleArrValue['parent_code']]['child'][$strModuleArrValue['id']]	= $strModuleArrValue;
				}
			}
		}
		/* return Module */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Getting module access list by role code.
	/*Inputs	: pRoleCode = Role code.
	/*Returns 	: Module access list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getModulesAccessDetails($pRoleCode = 0){
		/* Variable initialization */
		$strReturnArr	= array();
		
		/* Creatig module object */
		$moduleObj	= new Module($this->_objDataOperation, $this->getCompanyCode());
		/* getting module list array */
		$strModuleArr	= $moduleObj->getModulesByRoleCode(array($pRoleCode));
		/* Removed used variables */
		unset($moduleObj);
		
		/* If module details found then do needful */
		if(!empty($strModuleArr)){
			/* Iterating the loop */
			foreach($strModuleArr as $strModuleArrKye => $strModuleArrValue){
				/* setting the value */
				$strReturnArr[]	= array('module_code'=>getEncyptionValue($strModuleArrValue['module_code']));
			}
		}
		
		/* Removed used variables */
		unset($strModuleArr);
		
		/* Return the module access details */
		return $strReturnArr;
	}
}