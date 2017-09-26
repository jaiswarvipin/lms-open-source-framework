<?php
/***********************************************************************/
/* Purpose 		: Application lead sources.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadsource extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_lead_source';
	private $_strModuleName			= "Lead Source";
	private $_strModuleForm			= "frmLeadSource";
	
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
		
		/* Getting status list */
		$strLeadSourceArr['strRatingCode'] 			= $this->_objForm->getDropDown(array('1'=>'1 Star','2'=>'2 Start','3'=>'3 Start','4'=>'4 Start','5'=>'5 Start'));
		$strLeadSourceArr['dataSet'] 				= $this->_getLeadSource(0,'',false,false, $intCurrentPageNumber);
		$strLeadSourceArr['intPageNumber'] 			= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strLeadSourceArr['pagination'] 			= getPagniation($this->_getLeadSource(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strLeadSourceArr['moduleTitle']			= $this->_strModuleName;
		$strLeadSourceArr['moduleForm']				= $this->_strModuleForm;
		$strLeadSourceArr['moduleUri']				= SITE_URL.'settings/'.__CLASS__;
		$strLeadSourceArr['deleteUri']				= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strLeadSourceArr['getRecordByCodeUri']		= SITE_URL.'settings/'.__CLASS__.'/getLeadSourceDetailsByCode';
		$strLeadSourceArr['strDataAddEditPanel']	= 'leadSourceModel';
		$strLeadSourceArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/leadsource', $strLeadSourceArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get lead source details by code.
	/*Inputs	: None.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadSourceDetailsByCode(){
		/* Setting the status code */
		$intleadSourceCode 						= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$strLeadSourceArr						= array();
		/* Checking the status code shared */
		if($intleadSourceCode > 0){
			/* getting requested status code details */
			$strLeadSourceArr						= $this->_getLeadSource($intleadSourceCode);
			
			/* if record not found then do needful */
			if(empty($strLeadSourceArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strLeadSourceArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid status code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the lead source details.
	/*Inputs	: $pIntLedSourceCode :: Lead Source Code,
				: $pStatusName :: led source name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadSource($pIntLedSourceCode = 0, $pStrLedSourceName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strLeadSourceArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if status filter code is passed then do needful */
		if($pIntLedSourceCode < 0){
			/* Adding Status code filter */
			$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		/* if status filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strleadSourceName	= ($this->input->post('txtLeadSourceDescription') != '') ? $this->input->post('txtLeadSourceDescription') : '';
			$intReatingCode		= ($this->input->post('cboRatingCode') != '') ? $this->input->post('cboRatingCode') : '';
			
			if($strleadSourceName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strleadSourceName));
			}
			if($intReatingCode != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('rating_code'=>$intReatingCode));
			}
		}else{
			/* Getting status categories */
			if($pIntLedSourceCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pIntLedSourceCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pIntLedSourceCode));
				}
			}
		}
		
		/* filter by status name */
		if($pStrLedSourceName !=''){
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
		if(($intCurrentPageNumber >= 0) && ($pIntLedSourceCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		/* Getting the status list */
		$strLeadSourceArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strLeadSourceArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the lead source details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setLeadSource(){
		/* variable initialization */
		$strLeadSourceName		= ($this->input->post('txtLeadSourceDescription') != '')?$this->input->post('txtLeadSourceDescription'):'';
		$intReatingCode			= ($this->input->post('cboRatingCode') != '')?$this->input->post('cboRatingCode'):0;
		$intLeadSourceCode		= ($this->input->post('txtLeadSourceCode') != '')?$this->input->post('txtLeadSourceCode'):0;
		$blnEditRequest			= (($intLeadSourceCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strLeadSourceName == '') || ($intReatingCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Fetching any status with same name */
		$strLeadSourceArr 	= $this->_getLeadSource($intLeadSourceCode, $strLeadSourceName, $blnEditRequest);
		
		/* if status already exists then do needful */
		if(!empty($strLeadSourceArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Lead Source is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
														'description'=>$strLeadSourceName,
														'rating_code'=>$intReatingCode,
														'company_code'=>$this->getCompanyCode()
													)
									);
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intLeadSourceCode);
				/* Adding led source in the database */
				$intLeadSourceCode = $this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding lead source in the database */
				$intLeadSourceCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Removed used variables */
			unset($strDataArr);
			/* checking last insert id / updated record count */
			if($intLeadSourceCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Lead Source Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Lead Source added successfully.'), true);
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
		$intLeadSourceCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not status code pass then do needful */
		if($intLeadSourceCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid lead source code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intLeadSourceCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Lead Source deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
}