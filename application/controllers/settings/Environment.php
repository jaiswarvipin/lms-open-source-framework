<?php
/***********************************************************************/
/* Purpose 		: Application Environment Setting.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Environment extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user_config';
	private $_strModuleName			= "Environment";
	private $_strModuleForm			= "frmEnvironmentSetting";
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();
		/* Getting current page number */
		$intCurrentPageNumber	= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting environment list */
		$strRecordsetArr['dataSet'] 				= $this->_getEnvironmentData(0,'',false,false, $intCurrentPageNumber);
		$strRecordsetArr['intPageNumber'] 			= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strRecordsetArr['pagination'] 				= getPagniation($this->_getEnvironmentData(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strRecordsetArr['moduleTitle']				= $this->_strModuleName;
		$strRecordsetArr['moduleForm']				= $this->_strModuleForm;
		$strRecordsetArr['moduleUri']				= SITE_URL.'settings/'.__CLASS__;
		$strRecordsetArr['deleteUri']				= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strRecordsetArr['getRecordByCodeUri']		= SITE_URL.'settings/'.__CLASS__.'/getEnvironmentDetailsByCode';
		$strRecordsetArr['noSearchAdd']				= 'yes';
		$strRecordsetArr['strDataAddEditPanel']		= 'environmentModel';
		$strRecordsetArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the environment list */
		$dataArr['body']	= $this->load->view('settings/environment', $strRecordsetArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get the environment details by code.
	/*Inputs	: None.
	/*Returns 	: Environment Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getEnvironmentDetailsByCode(){
		/* Setting the environment code */
		$intEnvironmentCode 					= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$strRecordsetArr						= array();
		/* Checking the environment code */
		if($intEnvironmentCode > 0){
			/* getting requested environment code details */
			$strRecordsetArr						= $this->_getEnvironmentData($intEnvironmentCode);
			
			/* if record not found then do needful */
			if(empty($strRecordsetArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* based on requested parameters setting the drop down values */
				switch($strRecordsetArr[0]['key_description']){
					/* Region */
					case 'DEFAULT_REGION':
						$strRecordsetArr[0]['value_description'] =  $this->_objForm->getDropDown($this->getRegionDetails(),getEncyptionValue($strRecordsetArr[0]['value_description_oringial']));
						break;
					/* Branch */
					case 'DEFAULT_BRANCH':
						$strRecordsetArr[0]['value_description'] =  $this->_objForm->getDropDown($this->_getBranchByRegionCode(),getEncyptionValue($strRecordsetArr[0]['value_description_oringial']));
						break;
					/* Default user to whom lead goes */
					case 'DEFAULT_LEAD_ALLOCATED_TO':
						$strRecordsetArr[0]['value_description'] =  $this->_objForm->getDropDown($this->_getUserListByBranchCode(),getEncyptionValue($strRecordsetArr[0]['value_description_oringial']));
						break;
				}
				
				/* Return the JSON string */
				jsonReturn($strRecordsetArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid environment code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the environment details.
	/*Inputs	: $pIntEnvironmentCode :: Environment Code,
				: $pStrEnvironmentName :: Environment name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Environment Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getEnvironmentData($pIntEnvironmentCode = 0, $pStrEnvironmentName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strRecordsetArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if environment code is set the do needful */
		if((int)$pIntEnvironmentCode > 0){
			/* Setting filter clause */
			$strWhereClauseArr	= array_merge(array('id'=>$pIntEnvironmentCode), $strWhereClauseArr);
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pIntEnvironmentCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		

		/* Getting the environment list */
		$strRecordsetArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if record found then do needful */
		if(!empty($strRecordsetArr)){
			/* iterating the loop  */
			foreach($strRecordsetArr as $strRecordsetArrKey => $strRecordsetArrValue){
				if(isset($strRecordsetArrValue['key_description'])){
					/* Variable initialization */
					$strKeyName	= $strRecordsetArrValue['key_description'];
						
					/* Checking record existence */
					if(isset($strRecordsetArrValue['key_description'])){
						if($strKeyName == 'DEFAULT_REGION'){
							$strKeyName	= 'region_code';
						}else if($strKeyName == 'DEFAULT_BRANCH'){
							$strKeyName	= 'branch_code';
						}else if($strKeyName == 'DEFAULT_LEAD_ALLOCATED_TO'){
							$strKeyName	= 'lead_owner_name';
						}
						
						/* overriding the value */
						$strRecordsetArr[$strRecordsetArrKey]['value_description_oringial']	= $strRecordsetArrValue['value_description'];
					}
					$strRecordsetArr[$strRecordsetArrKey]['value_description']	= $this->getLeadAttributeDetilsByAttributeKey($strKeyName, $strRecordsetArr[$strRecordsetArrKey]['value_description']);
				}
			}
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strRecordsetArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the environment details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setEnvironment(){
		/* variable initialization */
		$strKeyDescription		= ($this->input->post('txtKeyDescription') != '')? $this->input->post('txtKeyDescription'):'';
		$strValueDescription	= ($this->input->post('txtValueDescription') != '')?getDecyptionValue(getDecyptionValue($this->input->post('txtValueDescription'))):0;
		$intEnvironmentCode		= ($this->input->post('txtEnvironmentCode') != '')?$this->input->post('txtEnvironmentCode'):0;
		$blnEditRequest			= (($intEnvironmentCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		
		/* Checking to all valid information passed */
		if(($strKeyDescription == '') || (($strValueDescription == 0) && ($intEnvironmentCode	!= 3))){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Setting where clause */
		$strWhereArr	= array('id'=>$intEnvironmentCode,'company_code'=>$this->getCompanyCode());
		
		/* if filter clause found then do needful */
		if(!empty($strWhereArr)){
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array('value_description'=>$strValueDescription),
										'where'=>$strWhereArr
									);
									
			/* updating environment in the database */
			$intEnvironmentCode = $this->_objDataOperation->setUpdateData($strDataArr);
		}
		 
		
		/* Removed used variables */
		unset($strDataArr);
		/* checking last insert id / updated record count */
		if($intEnvironmentCode > 0){
			/* Checking for edit request */
			if($blnEditRequest){
				jsonReturn(array('status'=>1,'message'=>'Environment Updated successfully.'), true);
			}else{
				jsonReturn(array('status'=>1,'message'=>'Environment added successfully.'), true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Delete the record from table of requested code.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function deleteRecord(){
		/* Variable initialization */
		$intEnvironmentCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not environment pass then do needful */
		if($intEnvironmentCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid environment code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intEnvironmentCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Environment deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get Branch list by selected region code
	/*Inputs	: None.
	/*Returns 	: Branch code array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getBranchByRegionCode(){
		/* variable initialization */
		$strRetrunArr	= array();
		/* get region code details of logger company */
		$strRegionArr	= $this->_objDataOperation->getDataFromTable(
																		array(
																				'table'=>$this->_strPrimaryTableName,
																				'column'=>array('value_description'),
																				'where'=>array('key_description'=>'DEFAULT_REGION','company_code'=>$this->getCompanyCode())
																			)
																);
		/* no region details found then do needful */
		if(empty($strRegionArr)){
			/* return complete drop down array of branch array*/
			return $this->getBranchDetails();
		}else{
			/* Get branch list filter by region code */
			$strBranchArr	= (array)$this->getBranchDetails(getEncyptionValue($strRegionArr[0]['value_description']));
			
			/* return selected region branch code as drop down array */
			return $strBranchArr;
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Get Branch list by selected region code
	/*Inputs	: None.
	/*Returns 	: Branch code array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserListByBranchCode(){
		/* variable initialization */
		$strUserArr 	= $strRetrunArr	= array();
		/* get branch code details of logger company */
		$strBranchCode	= $this->_objDataOperation->getDataFromTable(
																		array(
																				'table'=>$this->_strPrimaryTableName,
																				'column'=>array('value_description'),
																				'where'=>array('key_description'=>'DEFAULT_BRANCH','company_code'=>$this->getCompanyCode())
																			)
																);
		if(!empty($strBranchCode)){
			/* Location object creating */
			$locationObj = new Location($this->_objDataOperation, $this->getCompanyCode());
			
			/* if branch is not set then do needful */
			if($strBranchCode[0]['value_description'] == '0'){
				/* user array list */
				$strRetrunArr 	= $locationObj->getEmployeeByLocations(-1);
			}else{
				/* user array list */
				$strRetrunArr 	= $locationObj->getEmployeeByLocations(5,array($strBranchCode[0]['value_description']));
			}
			/* Removed used variable */
			unset($locationObj);
		}
		
		/* removed used variable */
		unset($strBranchCode);
		
		/* if user list is not empty then do needful */
		if(!empty($strRetrunArr)){
			/* Iterating the loop */
			foreach($strRetrunArr as $strRetrunArrKey => $strRetrunArrValue){
				/* Setting the user array */
				$strUserArr[getEncyptionValue($strRetrunArrValue['user_code'])]	= $strRetrunArrValue['user_name'];
			}
		}
		
		/* return user detail array */
		return $strUserArr;
	}
}