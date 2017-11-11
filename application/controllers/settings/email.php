<?php
/***********************************************************************/
/* Purpose 		: Email module Request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_email';
	private $_strEmailName			= "Email";
	private $_strModuleForm			= "frmEmail";
	
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
		
		/* Getting email list */
		$strResponseArr['dataSet'] 				= $this->_getEmailDetails(0,'',false,false, $intCurrentPageNumber);
		$strResponseArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strResponseArr['pagination'] 			= getPagniation($this->_getEmailDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strResponseArr['moduleTitle']			= $this->_strEmailName;
		$strResponseArr['moduleForm']			= $this->_strModuleForm;
		$strResponseArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strResponseArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strResponseArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getEmailDetailsByCode';
		$strResponseArr['strDataAddEditPanel']	= 'emailModel';
		$strResponseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/email', $strResponseArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get email details by code.
	/*Inputs	: None.
	/*Returns 	: Email details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getEmailDetailsByCode(){
		/* Setting the module code */
		$intEmailCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strEmailArr		= array();
		
		/* Checking the email code shared */
		if($intEmailCode > 0){
			/* getting requested email code details */
			$strEmailArr	= $this->_getEmailDetails($intEmailCode);
			
			/* if record not found then do needful */
			if(empty($strEmailArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strEmailArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid email code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the email details.
	/*Inputs	: $pEmailCode :: Email code,
				: $pStrEmailName :: Email Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Email details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getEmailDetails($pEmailCode = 0, $pStrEmailName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
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
			$strEmailName		= ($this->input->post('txtEmailName') != '')?$this->input->post('txtEmailName'):'';
			$intSystem			= ($this->input->post('rdoisDefault') != '')?$this->input->post('rdoisDefault'):0;
			
			if($strEmailName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strEmailName));
			}
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('is_system'=>$intSystem));
		}else{
			/* Getting email categories */
			if($pEmailCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding email code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pEmailCode));
				}else{
					/* Adding email code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pEmailCode));
				}
			}
		}
		
		/* filter by email name and parent code */
		if($pStrEmailName !=''){
			/* Adding email description as filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$pStrEmailName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pEmailCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the email list */
		$strEmailDetailsArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strEmailDetailsArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting email details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setEmailDetails(){
		/* variable initialization */
		$intEmailCode		= ($this->input->post('txtEmailCode') != '')? $this->input->post('txtEmailCode'):0;
		$strEmailName		= ($this->input->post('txtEmailName') != '')?$this->input->post('txtEmailName'):'';
		$intIsSystem		= ($this->input->post('rdoisDefault') != '')?$this->input->post('rdoisDefault'):0;
		$blnEditRequest		= (($intEmailCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr	= array();
		 
		/* Checking to all valid information passed */
		if(($strEmailName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Email name field is empty.'), true);
		}
		
		/* Adding email description filter */
		$strWhereClauseArr	= array('description'=>$strEmailName);
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding email code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intEmailCode));
		}
		
		/* Checking enter email description is already register or not */
		$strLeadAttribueDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if module already exists then do needful */
		if(!empty($strLeadAttribueDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Email Name is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
													'description'=>$strEmailName,
													'is_system'=>$intIsSystem,
													'company_code'=>$this->getCompanyCode()
												)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intEmailCode);
				/* Updating lead details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding email details in the database */
				$intEmailCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* checking last insert id / updated record count */
			if($intEmailCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Email name details Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Email name added successfully.'), true);
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
		$intEmailCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if email code is not pass then do needful */
		if($intEmailCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid email code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intEmailCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested email deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get selected emails, email template listing.
	/*Inputs	: None.
	/*Returns 	: Email template HTML.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function moduletemplate(){
		/* Variable initialization */
		$strWhereArr				= $strFilterArr 	= $strReturnArr	= array();
		$intEmailCode							= ($this->input->get('eMaIlCoDe') != '')? getDecyptionValue($this->input->get('eMaIlCoDe')):0;
		$strReturnArr['moduleForm']				= 'fromEmailTemplate';
		$strReturnArr['moduleUri']				= SITE_URL.'settings/'.strtolower(__CLASS__).'/moduletemplate?eMaIlCoDe='.$this->input->get('eMaIlCoDe');
		$strReturnArr['deleteUri']				= SITE_URL.'settings/'.__CLASS__.'/deleteEmailTemplateRecord?eMaIlCoDe='.$this->input->get('eMaIlCoDe');
		$strReturnArr['getRecordByCodeUri']		= SITE_URL.'settings/'.__CLASS__.'/getEmailTemplateDetailsByCode';
		$strReturnArr['strDataAddEditPanel']	= 'emailTemplateModel';
		$strReturnArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* checking for email code, if not found then redirect it session on the email name listing module */
		if($intEmailCode == 0){
			/* Redirect */
			redirect(SITE_URL.'settings/'.strtolower(__CLASS__));
		}
		
		/* Setting the email template filter */
		$strWhereArr	= array('master_email_templates.email_code = '.$intEmailCode, 'master_email.company_code = '.$this->getCompanyCode());
		
		/* Setting the filter array */
		$strFilterArr	= array(
									'table'=>array('master_email','master_email_templates'),
									'join'=>array('','master_email.id = master_email_templates.email_code'),
									'column'=>array('master_email_templates.*','master_email.description as email_name'),
									'where'=>$strWhereArr
							);
		
		/* Updating the requested record set */
		$strReturnArr['strDataSet'] = $this->_objDataOperation->getDataFromTable($strFilterArr);
		/* Setting the view with data */
		$this->load->view('settings/email_templates', $strReturnArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting the email template.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setEmailTemplateDetails(){
		
	}
}