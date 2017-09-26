<?php
/***********************************************************************/
/* Purpose 		: Company Location Management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_location';
	private $_strModuleName			= "Operation Location(s)";
	private $_strModuleForm			= "frmLocation";
	private $_intLocationType		= '1';
	private $_strModuleConstantArr	= array();
	
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
		
		/* Getting user role list */
		$strLocationArr['dataSet'] 				= $this->_getUserLocationDetails(0,0,'',false,false, $intCurrentPageNumber);
		$strLocationArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strLocationArr['pagination'] 			= getPagniation($this->_getUserLocationDetails(0,0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strLocationArr['moduleTitle']			= $this->_strModuleName.' (ZONE)';
		$strLocationArr['moduleForm']			= $this->_strModuleForm;
		$strLocationArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strLocationArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strLocationArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getLocationsDetailsByCode';
		$strLocationArr['strDataAddEditPanel']	= 'useRoleModel';
		$strLocationArr['strChildLabel']		= 'Regions';
		$strLocationArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strLocationArr['strParentCode']		= getEncyptionValue('0');
		$strLocationArr['intLocationTypeCode']	= getEncyptionValue('1');
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/locations', $strLocationArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the region.
	/*Inputs	: none.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function regions(){	
		/* variable initialization */
		$dataArr	= array();
		/* Getting current page number */
		$intCurrentPageNumber	= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		$intParentCode			= ($this->input->post('parent_code') != '') ? getDecyptionValue($this->input->post('parent_code')):(($this->input->post('txtParentLocationCode')!='')?getDecyptionValue($this->input->post('txtParentLocationCode')):0);
		$this->_intLocationType	= ($this->input->post('txtParentLocationType') != '') ? getDecyptionValue($this->input->post('txtParentLocationType')):2;
		
		/* Getting user role list */
		$strLocationArr['dataSet'] 				= $this->_getUserLocationDetails(0,$intParentCode,'',false,false, $intCurrentPageNumber);
		$strLocationArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strLocationArr['pagination'] 			= getPagniation($this->_getUserLocationDetails(0,$intParentCode,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strLocationArr['moduleTitle']			= $this->_strModuleName.' '.(((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['moduleTitle'])))?($this->_strModuleConstantArr['moduleTitle']):'REGION');
		$strLocationArr['moduleForm']			= $this->_strModuleForm;
		$strLocationArr['moduleUri']			= ((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['moduleUri'])))?$this->_strModuleConstantArr['moduleUri']:SITE_URL.'settings/'.__CLASS__.'/regions';
		$strLocationArr['deleteUri']			= ((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['deleteUri'])))?$this->_strModuleConstantArr['deleteUri']:SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strLocationArr['getRecordByCodeUri']	= ((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['getRecordByCodeUri'])))?$this->_strModuleConstantArr['getRecordByCodeUri']:SITE_URL.'settings/'.__CLASS__.'/getLocationsDetailsByCode';
		$strLocationArr['strChildLabel']		= ((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['strChildLabel'])))?$this->_strModuleConstantArr['strChildLabel']:'Citys';
		$strLocationArr['strDataAddEditPanel']	= 'useRoleModel';
		$strLocationArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strLocationArr['strParentCode']		= getEncyptionValue($intParentCode);
		$strLocationArr['intLocationTypeCode']	= ((!empty($this->_strModuleConstantArr)) && (isset($this->_strModuleConstantArr['intLocationTypeCode'])))?getEncyptionValue($this->_strModuleConstantArr['intLocationTypeCode']):getEncyptionValue('2');
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/locations', $strLocationArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the city.
	/*Inputs	: none.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function citys(){
		/* Variable initialization */
		$this->_strModuleConstantArr['moduleTitle']			= 'CITYS';
		$this->_strModuleConstantArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__.'/citys';
		$this->_strModuleConstantArr['strChildLabel']		= 'Areas';
		$this->_strModuleConstantArr['intLocationTypeCode']	= '3';
		
		/* Calling Location method as delegate */
		$this->regions();
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the Area.
	/*Inputs	: none.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function areas(){
		/* Variable initialization */
		$this->_strModuleConstantArr['moduleTitle']			= 'AREAS';
		$this->_strModuleConstantArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__.'/areas';
		$this->_strModuleConstantArr['strChildLabel']		= 'Branches';
		$this->_strModuleConstantArr['intLocationTypeCode']	= '4';
		
		/* Calling Location method as delegate */
		$this->regions();
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the Branch.
	/*Inputs	: none.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function branches(){
		/* Variable initialization */
		$this->_strModuleConstantArr['moduleTitle']			= 'BRANCHS';
		$this->_strModuleConstantArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__.'/branches';
		$this->_strModuleConstantArr['strChildLabel']		= '';
		$this->_strModuleConstantArr['intLocationTypeCode']	= '5';
		
		/* Calling Location method as delegate */
		$this->regions();
	}

	/**********************************************************************/
	/*Purpose 	: Get user roles details by code.
	/*Inputs	: None.
	/*Returns 	: User Role Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLocationsDetailsByCode(){
		/* Setting the location code */
		$intLocationCode 					= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strLocationArr						= array();
		/* Checking the Location Code shared */
		if($intLocationCode > 0){
			/* getting requested role code details */
			$strLocationArr					= $this->_getUserLocationDetails($intLocationCode);
			
			/* if record not found then do needful */
			if(empty($strLocationArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Variable initialization */
				$strReturnArr	= array();
				/* Iterating the loop */
				foreach($strLocationArr[0] as $strLocationArrKey => $strLocationArrValue){
					/* if location encryption column fund then do needful */
					if(in_array($strLocationArrKey, array('id','parent_code','location_type'))){
						/* Setting encryption key */
						$strReturnArr[$strLocationArrKey]	= getEncyptionValue($strLocationArrValue);
					}else{
						/* Setting default value */
						$strReturnArr[$strLocationArrKey]	= $strLocationArrValue;
					}
				}
				/* Removed used variables */
				unset($strLocationArr[0]);
				
				/* Return the JSON string */
				jsonReturn($strReturnArr, true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid location code code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the user role details.
	/*Inputs	: $pUserCodeCode :: User Role description,
				: $pIntParentCode :: parent Code,
				: $pStrUserRoleName :: User role name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserLocationDetails($pUserCodeCode = 0, $pIntParentCode = 0, $pStrUserRoleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strLocationArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode(),'parent_code'=>$pIntParentCode);
		
		/* if user role filter code is passed then do needful */
		if($pUserCodeCode < 0){
			/* Adding User Role code filter */
			$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode(), 'location_type'=>$this->_intLocationType);
		/* if role filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strLocation	= ($this->input->post('txtLocationDescription') != '') ? $this->input->post('txtLocationDescription') : '';
			
			if($strLocation != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strLocation));
			}
		}else{
			/* Getting status categories */
			if($pUserCodeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pUserCodeCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pUserCodeCode));
					unset($strWhereClauseArr['parent_code']);
				}
			}
		}
		
		/* filter by role name */
		if($pStrUserRoleName !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$pStrUserRoleName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pUserCodeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		/* Getting the status list */
		$strLocationArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strLocationArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the location details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setLocaions(){
		/* variable initialization */
		$strLocationDescription	= ($this->input->post('txtLocationDescription') != '')?$this->input->post('txtLocationDescription'):'';
		$intParentLocationCode	= ($this->input->post('txtParentLocationCode') != '')?getDecyptionValue($this->input->post('txtParentLocationCode')):0;
		$intLocationCode		= ($this->input->post('txtLocationCode') != '')?getDecyptionValue($this->input->post('txtLocationCode')):0;
		$blnEditRequest			= (($intLocationCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		$this->_intLocationType	= ($this->input->post('txtParentLocationType') != '') ? getDecyptionValue($this->input->post('txtParentLocationType')):1;
		
		if($blnSearch){
			switch($intParentLocationCode){
				case 1:
					$this->index();
					break;
				case 2:
					$this->regions();
					break;
			}
			exit;
		}

		/* Checking to all valid information passed */
		if(($strLocationDescription == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Fetching any location with same name */
		$strLocationArr 	= $this->_getUserLocationDetails($intLocationCode, $intParentLocationCode, $strLocationDescription, $blnEditRequest);
		
		/* if location already exists then do needful */
		if(!empty($strLocationArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Location is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
														'description'=>$strLocationDescription,
														'parent_code'=>$intParentLocationCode,
														'location_type'=>$this->_intLocationType,
														'company_code'=>$this->getCompanyCode(),
														'updated_by'=>$this->getUserCode()
													)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intLocationCode);
				/* Adding location in the database */
				$intLocationCode = $this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding location in the database */
				$intLocationCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Removed used variables */
			unset($strDataArr);
			/* checking last insert id / updated record count */
			if($intLocationCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Operation Location updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Operation Location added successfully.'), true);
				}
			}else{
				jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
			}
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
		$intLocationCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not role code pass then do needful */
		if($intLocationCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid Operation Location code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intLocationCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Operation Location deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
}