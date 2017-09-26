<?php
/***********************************************************************/
/* Purpose 		: Application lead status.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_status';
	private $_strModuleName			= "Status";
	private $_strModuleForm			= "frmStatus";
	
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
		$strFormName			= 'frmStatus';
		/* Getting status list */
		$strStatusArr['strStatusCategories'] 	= $this->_objForm->getDropDown(getArrByKeyvaluePairs($this->_getStatus(STATUS_CATEGORIES_CODE),'id','description'));
		$strStatusArr['strParentStatus'] 		= $this->_objForm->getDropDown(getArrByKeyvaluePairs($this->_getStatus(0, 0),'id','description'));
		$strStatusArr['dataSet'] 				= $this->_getStatus(0,-1,'',false,false, $intCurrentPageNumber);
		$strStatusArr['intPageNumber'] 			= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strStatusArr['pagination'] 			= getPagniation($this->_getStatus(0,-1,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strStatusArr['moduleTitle']			= $this->_strModuleName;
		$strStatusArr['moduleForm']				= $this->_strModuleForm;
		$strStatusArr['moduleUri']				= SITE_URL.'settings/'.__CLASS__;
		$strStatusArr['deleteUri']				= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strStatusArr['getRecordByCodeUri']		= SITE_URL.'settings/'.__CLASS__.'/getStatusDetailsByCode';
		$strStatusArr['strDataAddEditPanel']	= 'statusModel';
		$strStatusArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/status', $strStatusArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get status details by code.
	/*Inputs	: None.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getStatusDetailsByCode(){
		/* Setting the status code */
		$intStatusCode 							= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$strStatusArr							= array();
		/* Checking the status code shared */
		if($intStatusCode > 0){
			/* getting requested status code details */
			$strStatusArr						= $this->_getStatus($intStatusCode);	
			
			/* if record not found then do needful */
			if(empty($strStatusArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strStatusArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid status code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the status details.
	/*Inputs	: $pIntStatusCode :: Status Code,
				: $pIntParentStatus :: Parent Sattus code,
				: $pStatusName :: Status name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getStatus($pIntStatusCode = 0, $pIntParentStatus = -1, $pStatusName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strStatusArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if status filter code is passed then do needful */
		if($pIntStatusCode < 0){
			/* Adding Status code filter */
			$strWhereClauseArr	= array('company_code'=>1, 'parent_id'=>-1);
		/* if status filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strStatusName	= ($this->input->post('txtStatusName') != '') ? $this->input->post('txtStatusName') : '';
			$blnIsDefault	= ($this->input->post('rdoisDefault') != '') ? $this->input->post('rdoisDefault') : '';
			$intparentStatus= ($this->input->post('cboParnetStatus') != '') ? $this->input->post('cboParnetStatus') : '';
			
			if($strStatusName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strStatusName));
			}
			if($blnIsDefault != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('is_default'=>$blnIsDefault));
			}
			if($intparentStatus != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('parent_id'=>$intparentStatus));
			}
		}else{
			/* Getting status categories */
			if($pIntStatusCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pIntStatusCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pIntStatusCode));
				}
			}else{
				/* Adding Parent Status code filter */
				$strWhereClauseArr	= array('company_code'=>1, 'parent_id >='=>0);
			}
		}
		
		/* adding parent status code filter */
		if($pIntParentStatus >= 0){
			/* Adding Parent Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('parent_id'=>$pIntParentStatus));
		}

		/* filter by status name */
		if($pStatusName !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$pStatusName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pIntStatusCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		/* Getting the status list */
		$strStatusArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strStatusArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the lead status.
	/*Inputs	: None.
	/*Returns 	: Transcation Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setStatus(){
		/* variable initialization */
		$strStausName			= ($this->input->post('txtStatusName') != '')?$this->input->post('txtStatusName'):'';
		$intParentStatusCode	= ($this->input->post('cboParnetStatus') != '')?$this->input->post('cboParnetStatus'):0;
		$intDefaultStatus		= ($this->input->post('rdoisDefault') != '')?$this->input->post('rdoisDefault'):0;
		$intStatusCode			= ($this->input->post('txtStatusCode') != '')?$this->input->post('txtStatusCode'):0;
		$blnEditRequest			= (($intStatusCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strStausName == '') || ($intParentStatusCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Fetching any status with same name */
		$strStatusArr 	= $this->_getStatus($intStatusCode, -1, $strStausName, $blnEditRequest);
		
		/* if status already exists then do needful */
		if(!empty($strStatusArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Status is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
														'description'=>$strStausName,
														'parent_id'=>$intParentStatusCode,
														'company_code'=>$this->getCompanyCode(),
														'status_type'=>$intParentStatusCode,
														'is_default'=>$intDefaultStatus
													)
									);
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intStatusCode);
				/* Adding status in the database */
				$intStatusCode = $this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding status in the database */
				$intStatusCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Removed used variables */
			unset($strDataArr);
			/* checking last insert id / updated record count */
			if($intStatusCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Status Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Status added successfully.'), true);
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
		$intStatusCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not status code pass then do needful */
		if($intStatusCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid status code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intStatusCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Status deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
}