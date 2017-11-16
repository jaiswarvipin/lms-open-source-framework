<?php
/***********************************************************************/
/* Purpose 		: Email template Request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Emailtemplate extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_email_templates';
	private $_strEmailName			= "Email Template(s)";
	private $_strModuleForm			= "frmEmailTemplate";
	
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
		$strEmailUrlCode						= $this->input->post('eMaIlCoDe');
		$intEmailCode							= ($this->input->post('eMaIlCoDe') != '')? getDecyptionValue($this->input->post('eMaIlCoDe')):0;
		
		/* checking for email code, if not found then redirect it session on the email name listing module */
		if($intEmailCode == 0){
			/* Redirect */
			redirect(SITE_URL.'settings/email');
		}
		
		/* Getting current page number */
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting email template list */
		$strResponseArr['strEmailPName']		= $this->_getParentEmailDetails($intEmailCode);
		$strResponseArr['dataSet'] 				= $this->_getEmailTemplateDetails(0,'',false,false, $intCurrentPageNumber);
		$strResponseArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strResponseArr['pagination'] 			= getPagniation($this->_getEmailTemplateDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strResponseArr['moduleTitle']			= $this->_strEmailName. ' - '.$strResponseArr['strEmailPName'];
		$strResponseArr['moduleForm']			= $this->_strModuleForm;
		$strResponseArr['strEmailPCode']		= ($strEmailUrlCode);
		$strResponseArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__.'?eMaIlCoDe='.$strEmailUrlCode;
		$strResponseArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord?eMaIlCoDe='.$strEmailUrlCode;
		$strResponseArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getEmailTemplateDetailsByCode?eMaIlCoDe='.$strEmailUrlCode;
		$strResponseArr['strDataAddEditPanel']	= 'emailTemplateModel';
		$strResponseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/email_templates', $strResponseArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get email template details by code.
	/*Inputs	: None.
	/*Returns 	: Email template details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getEmailTemplateDetailsByCode(){
		/* Setting the module code */
		$intEmailCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strEmailArr		= array();
				
		/* Checking the email template code shared */
		if($intEmailCode > 0){
			/* getting requested email code details */
			$strEmailArr	= $this->_getEmailTemplateDetails($intEmailCode);
			
			/* if record not found then do needful */
			if(empty($strEmailArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strEmailArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid email template code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the email template details.
	/*Inputs	: $pEmailCode :: Email code,
				: $pStrEmailName :: Email Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Email details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getEmailTemplateDetails($pEmailCode = 0, $pStrEmailName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strResponseArr	= $strWhereClauseArr 	= array();
		$intEmailPCode	= ($this->input->post('eMaIlCoDe') != '')? getDecyptionValue($this->input->post('eMaIlCoDe')):0;
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company and parent email filter */
		$strWhereClauseArr	= array('master_email.company_code'=>$this->getCompanyCode(),$this->_strPrimaryTableName.'.email_code'=>$intEmailPCode);
		
		/* if any specific email template requested the do needful */
		if($pEmailCode > 0){
			/* Remove email parent code */
			unset($strWhereClauseArr[$this->_strPrimaryTableName.'.email_code']);
		}
		
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strEmailSubject	= ($this->input->post('txtEmailSubject') != '')?$this->input->post('txtEmailSubject'):'';
			$strFromName		= ($this->input->post('txtEmailFromName') != '')?$this->input->post('txtEmailFromName'):'';
			$strFromEmail		= ($this->input->post('txtEmailFromEmail') != '')?$this->input->post('txtEmailFromEmail'):'';
			
			/* Checking for email subject */
			if($strEmailSubject != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'email_subject like'=>$strEmailSubject));
			}
			/* Checking for email From name */
			if($strFromName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'from_name like'=>$strFromName));
			}
			/* Checking for email From email */
			if($strFromEmail != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'from_email like'=>$strFromEmail));
			}
		}else{
			/* Getting email categories */
			if($pEmailCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding email template code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id !='=>$pEmailCode));
				}else{
					/* Adding email template code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id'=>$pEmailCode));
				}
			}
		}
		
		/* filter by email subject */
		if($pStrEmailName !=''){
			/* Adding email subject as filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.email_subject like'=>$pStrEmailName));
		}
		
		/* Setting the filter array */
		$strFilterArr	= array(
									'table'=>array('master_email',$this->_strPrimaryTableName),
									'join'=>array('','master_email.id = '.$this->_strPrimaryTableName.'.email_code'),
									'column'=>array($this->_strPrimaryTableName.'.*','master_email.description as email_name'),
									'where'=>$strWhereClauseArr
							);
							
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count('.$this->_strPrimaryTableName.'.id) as recordCount ');
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
	/*Purpose 	: Setting Email template details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setEmailTemplateDetails(){
		/* variable initialization */
		$intEmailCode		= ($this->input->post('txtEmailTemplateCode') != '')? $this->input->post('txtEmailTemplateCode'):0;
		$intEmailPCode		= ($this->input->post('eMaIlCoDe') != '')? getDecyptionValue($this->input->post('eMaIlCoDe')):0;
		$strEmailSubject	= ($this->input->post('txtEmailSubject') != '')?$this->input->post('txtEmailSubject'):'';
		$strFromName		= ($this->input->post('txtEmailFromName') != '')?$this->input->post('txtEmailFromName'):'';
		$strFromEmail		= ($this->input->post('txtEmailFromEmail') != '')?$this->input->post('txtEmailFromEmail'):'';
		$strEmailBody		= ($this->input->post('txtEmailBody') != '')?$this->input->post('txtEmailBody'):'';
		$strBlackList		= ($this->input->post('txtBlackListEmailAddress') != '')?$this->input->post('txtBlackListEmailAddress'):'';
		$intActiveCode		= ($this->input->post('rdoisDefault') != '')?$this->input->post('rdoisDefault'):0;
		$blnEditRequest		= (($intEmailCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr	= array();
		
		/* checking for email code, if not found then redirect it session on the email name listing module */
		if($intEmailPCode == 0){
			/* Redirect */
			redirect(SITE_URL.'settings/email');
		}
		 
		/* Checking to all valid information passed */
		if(($strEmailSubject == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Email subject field is empty.'), true);
		}
		if(($strFromName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Email from name field is empty.'), true);
		}
		if(($strFromEmail == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Email name field is empty.'), true);
		}
		if(($strEmailBody == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Email body field is empty.'), true);
		}
		
		/* Adding email subject description filter */
		$strWhereClauseArr	= array('email_subject'=>$strEmailSubject);
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding email template code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intEmailCode));
		}
		
		/* Checking enter email subject is already register or not */
		$strEmailTemplateArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if email template subject already exists then do needful */
		if(!empty($strEmailTemplateArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Email Template Subject is already exists.'), true);	
		}else{
			
			/* if current email template  mark active then de-activating other all template belongs to requested parent mail code, */
			if((int)$intActiveCode > 0){
				/* updating the other active template of email code */
				$this->_objDataOperation->setUpdateData(
															array('table'=>$this->_strPrimaryTableName,
																	'data'=>array(
																				'is_active'=>0
																			),
																	'where'=>array('email_code'=>$intEmailPCode,'deleted'=>0)
															)
														);
			}
			
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
													'email_subject'=>$strEmailSubject,
													'from_name'=>$strFromName,
													'from_email'=>$strFromEmail,
													'email_body'=>$strEmailBody,
													'black_list_emails'=>$strBlackList,
													'is_active'=>$intActiveCode,
													'email_code'=>$intEmailPCode
												)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intEmailCode);
				/* Updating email template details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding email template details in the database */
				$intEmailCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* checking last insert id / updated record count */
			if($intEmailCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Email template details Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Email template added successfully.'), true);
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
			jsonReturn(array('status'=>0,'message'=>"Invalid email template code requested."), true);
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
			jsonReturn(array('status'=>1,'message'=>'Requested email template deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get email name.
	/*Inputs	: $pIntParentEmailCode :: email code.
	/*Returns 	: Email name.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getParentEmailDetails($pIntParentEmailCode = 0 ){
		/* Variable initialization */
		$strEmailName	= '';
		/* Checking for email code */
		if($pIntParentEmailCode == 0){
			/* Return the email name */
			return $strEmailName;
		}
		
		/* Getting the requested email details */
		$strEmailDetailsArr	=  $this->_objDataOperation->getDataFromTable(
																			array(
																					'table'=>'master_email',
																					'column'=>array('description'),
																					'where'=>array('id'=>$pIntParentEmailCode)
																			)
																		);
		/* Checking for record set */
		if(!empty($strEmailDetailsArr)){
			/* Setting value */
			$strEmailName = $strEmailDetailsArr[0]['description'];
		}
		
		/* removed used variables */
		unset($strEmailDetailsArr);
		/* Return email name */
		return $strEmailName;
	}
}