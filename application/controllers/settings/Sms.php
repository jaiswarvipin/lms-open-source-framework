<?php
/***********************************************************************/
/* Purpose 		: SMS module Request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_sms';
	private $_strSMSName			= "SMS";
	private $_strModuleForm			= "frmSMS";
	
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
		
		/* Getting SMS list */
		$strResponseArr['dataSet'] 				= $this->_getSMSDetails(0,'',false,false, $intCurrentPageNumber);
		$strResponseArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strResponseArr['pagination'] 			= getPagniation($this->_getSMSDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strResponseArr['moduleTitle']			= $this->_strSMSName;
		$strResponseArr['moduleForm']			= $this->_strModuleForm;
		$strResponseArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strResponseArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strResponseArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getSMSDetailsByCode';
		$strResponseArr['strDataAddEditPanel']	= 'emailModel';
		$strResponseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/sms', $strResponseArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get SMS details by code.
	/*Inputs	: None.
	/*Returns 	: SMS details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getSMSDetailsByCode(){
		/* Setting the SMS code */
		$intSMSCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strSMSArr		= array();
		
		/* Checking the SMS code shared */
		if($intSMSCode > 0){
			/* getting requested SMS code details */
			$strSMSArr	= $this->_getSMSDetails($intSMSCode);
			
			/* if record not found then do needful */
			if(empty($strSMSArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strSMSArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid SMS code requested.'), true);
		}
	}
/**********************************************************************/
	/*Purpose 	: Getting the SMS details.
	/*Inputs	: $pSMSCode :: SMS code,
				: $pStrSMSName :: SMS Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: SMS details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getSMSDetails($pSMSCode = 0, $pStrSMSName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strResponseArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strSMSSubject		= ($this->input->post('txtSMSSubject') != '')?$this->input->post('txtSMSSubject'):'';
			$strSMSFrom			= ($this->input->post('txtSmsFrom') != '')?$this->input->post('txtSmsFrom'):'';
			$strSMSType			= ($this->input->post('txtSMSType') != '')?$this->input->post('txtSMSType'):'';
			
			if($strSMSSubject != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('sms_subject like'=>$strSMSSubject));
			}
			if($strSMSFrom != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('sms_from like'=>$strSMSFrom));
			}
			if($strSMSType != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('sms_type like'=>$strSMSType));
			}
		}else{
			/* Getting SMS categories */
			if($pSMSCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding SMS code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pSMSCode));
				}else{
					/* Adding SMS code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pSMSCode));
				}
			}
		}
		
		/* filter by SMS subject */
		if($pStrSMSName !=''){
			/* Adding SMS subject as filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('sms_subject like'=>$pStrSMSName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if countneeded then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pSMSCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the SMS list */
		$strSMSDetailsArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strSMSDetailsArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting SMS details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setSMSDetails(){
		/* variable initialization */
		$intSMSCode			= ($this->input->post('txtSMSCode') != '')? $this->input->post('txtSMSCode'):0;
		$strSMSSubject		= ($this->input->post('txtSMSSubject') != '')?$this->input->post('txtSMSSubject'):'';
		$strSMSFrom			= ($this->input->post('txtSmsFrom') != '')?$this->input->post('txtSmsFrom'):'';
		$strSMSType			= ($this->input->post('txtSMSType') != '')?$this->input->post('txtSMSType'):'';
		$strSMSBody			= ($this->input->post('txtSMSBody') != '')?$this->input->post('txtSMSBody'):'';
		$blnEditRequest		= (($intSMSCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr	= array();
		 
		/* Checking to all valid information passed */
		if(($strSMSSubject == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'SMS subject field is empty.'), true);
		}
		if(($strSMSFrom == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'SMS from field is empty.'), true);
		}
		if(($strSMSType == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'SMS type field is empty.'), true);
		}
		if(($strSMSBody == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'SMS body field is empty.'), true);
		}
		
		/* Adding SMS subject filter */
		$strWhereClauseArr	= array('sms_subject'=>$strSMSSubject);
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding SMS code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intSMSCode));
		}
		
		/* Checking enter SMS subject is already register or not */
		$strSMSDetailsArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if SMS already exists then do needful */
		if(!empty($strSMSDetailsArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested SMS Subject is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
													'sms_subject'=>$strSMSSubject,
													'sms_from'=>$strSMSFrom,
													'sms_type'=>$strSMSType,
													'sms_body'=>$strSMSBody,
													'company_code'=>$this->getCompanyCode()
												)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intSMSCode);
				/* Updating SMS details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding SMS details in the database */
				$intSMSCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* checking last insert id / updated record count */
			if($intSMSCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'SMS details Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'SMS added successfully.'), true);
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
		$intSMSCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if SMS code is not pass then do needful */
		if($intSMSCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid SMS code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intSMSCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested SMS deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
}