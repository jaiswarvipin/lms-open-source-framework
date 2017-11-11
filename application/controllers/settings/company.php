<?php
/***********************************************************************/
/* Purpose 		: Company Request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_company';
	private $_strCompanyName		= "Company(s)";
	private $_strModuleForm			= "frmCompany";
	
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
		
		/* Getting company list */
		$strResponseArr['dataSet'] 				= $this->_getCompanyDetails(0,'',false,false, $intCurrentPageNumber);
		$strResponseArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strResponseArr['pagination'] 			= getPagniation($this->_getCompanyDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strResponseArr['moduleTitle']			= $this->_strCompanyName;
		$strResponseArr['moduleForm']			= $this->_strModuleForm;
		$strResponseArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strResponseArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strResponseArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getCompanyDetailsByCode';
		$strResponseArr['strDataAddEditPanel']	= 'moduleModel';
		$strResponseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/company', $strResponseArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get company details details by code.
	/*Inputs	: None.
	/*Returns 	: Company Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getCompanyDetailsByCode(){
		/* Setting the module code */
		$intCompanyCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strCompanyArr		= array();
		
		if($intCompanyCode > 0){
			/* getting requested company code details */
			$strCompanyArr	= $this->_getCompanyDetails($intCompanyCode);
			
			/* if record not found then do needful */
			if(empty($strCompanyArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strCompanyArr[0], true);
			}
			
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid company code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the company details.
	/*Inputs	: $pCompanyCode :: Company code,
				: $pStrName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getCompanyDetails($pCompanyCode = 0, $pStrName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strResponseArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* if module filter code is passed then do needful */
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strCompanyName			= ($this->input->post('txtCompanyName') != '')?$this->input->post('txtCompanyName'):'';
			
			if($strCompanyName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('name like'=>$strCompanyName));
			}
		}else{
			/* Getting company categories */
			if($pCompanyCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pCompanyCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pCompanyCode));
				}
			}
		}
		
		/* filter by company name and parent code */
		if($pStrName !=''){
			/* Adding company name as filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('name like'=>$pStrName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pCompanyCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the company list */
		$strtCompanyArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strtCompanyArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting company details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setCompanyDetails(){
		/* variable initialization */
		$intCompanyCode		= ($this->input->post('txtCompanyCode') != '')? $this->input->post('txtCompanyCode'):0;
		$strCompanyName		= ($this->input->post('txtCompanyName') != '')?$this->input->post('txtCompanyName'):'';
		$intIsActive		= ($this->input->post('rdoisActive') != '')?$this->input->post('rdoisActive'):0;
		$blnEditRequest		= (($intCompanyCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr	= array();
		
		/* Checking to all valid information passed */
		if(($strCompanyName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Company name field is empty.'), true);
		}
		
		/* Adding company name filter */
		$strWhereClauseArr	= array('name'=>$strCompanyName);
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding module code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intCompanyCode));
		}
		
		/* Checking enter module description is already register or not */
		$strLeadAttribueDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if module already exists then do needful */
		if(!empty($strLeadAttribueDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Company is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
													'name'=>$strCompanyName,
													'is_active'=>$intIsActive
												)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intCompanyCode);
				/* Updating lead details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding lead details in the database */
				$intCompanyCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* checking last insert id / updated record count */
			if($intCompanyCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Company details Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Company added successfully.'), true);
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
		$intCompanyCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if company code is not pass then do needful */
		if($intCompanyCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid company code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intCompanyCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested company deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}
		
		/* removed variables */
		unset($strUpdatedArr);
	}
}