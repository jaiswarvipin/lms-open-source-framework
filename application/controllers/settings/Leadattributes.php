<?php
/***********************************************************************/
/* Purpose 		: Application lead attributes.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadattributes extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_lead_attributes';
	private $_strModuleName			= "Lead Attributes";
	private $_strModuleForm			= "frmleadAttributes";
	
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
		
		/* Getting lead attributes list */
		$strUserRoleArr['dataSet'] 				= $this->_getLeadProfileDetails(0,'',false,false, $intCurrentPageNumber);
		$strUserRoleArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strUserRoleArr['pagination'] 			= getPagniation($this->_getLeadProfileDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getLeadAttributeByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'userleadAttriuteModel';
		$strUserRoleArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strUserRoleArr['strElementsArr']		= $this->_objForm->getDropDown(unserialize(LEAD_ATTRIBUTE_INPUT_ELEMENT),'');
		$strUserRoleArr['strValidationArr']		= $this->_objForm->getDropDown(unserialize(LEAD_ATTRIBUTE_INPUT_VALIDATION),'');
		
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/lead-attributes', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get lead attribute details by code.
	/*Inputs	: None.
	/*Returns 	: lead Attributes Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadAttributeByCode(){
		/* Setting the lead attribute code */
		$intleadAttributeCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strLeadAttributeArr		= array();
		
		/* Checking the lead attribute code shared */
		if($intleadAttributeCode > 0){
			/* getting requested lead attribute code details */
			$strLeadAttributeArr	= $this->_getLeadProfileDetails($intleadAttributeCode);
			
			/* if record not found then do needful */
			if(empty($strLeadAttributeArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Setting the default value collection values */
				$strLeadAttributeArr[0]['attri_value_list']	= (!empty(unserialize($strLeadAttributeArr[0]['attri_value_list'])))?unserialize($strLeadAttributeArr[0]['attri_value_list']):'';
				/* Return the JSON string */
				jsonReturn($strLeadAttributeArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid lead attribute code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the lead profile details.
	/*Inputs	: $pLeadAttributeCode :: Lead attribute code description,
				: $pStrSlugName :: Lead attribute slug name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadProfileDetails($pLeadAttributeCode = 0, $pStrSlugName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strUserRoleArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if user profile filter code is passed then do needful */
		if($pLeadAttributeCode < 0){
			/* Adding User profile code filter */
			$strWhereClauseArr	= array('company_code'=>1);
		/* if profile filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strSlugName			= ($this->input->post('txtAttrubuteName') != '')?$this->input->post('txtAttrubuteName'):'';
			$strSlugKey				= getSlugify($strSlugName);
			
			if($strSlugKey != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('attri_slug_key like'=>$strSlugKey));
			}
		}else{
			/* Getting status categories */
			if($pLeadAttributeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pLeadAttributeCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pLeadAttributeCode));
				}
			}
		}
		
		/* filter by email name */
		if($pStrSlugName !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('attri_slug_key like'=>getSlugify($pStrSlugName)));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pLeadAttributeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the lead attribute list */
		$strLeadAttArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Getting status categories */
		if($pLeadAttributeCode > 0){
			$strLeadAttArr[0]['attri_data_type']	= getEncyptionValue($strLeadAttArr[0]['attri_data_type']);
			$strLeadAttArr[0]['attri_validation']	= getEncyptionValue($strLeadAttArr[0]['attri_validation']);
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strLeadAttArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting lead attribute details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setLeadAttributDetails(){
		/* variable initialization */
		$intLeadAttributeCode	= ($this->input->post('txtAttributeCode') != '')? $this->input->post('txtAttributeCode'):0;
		$strLeadAttributeName	= ($this->input->post('txtAttrubuteName') != '')?$this->input->post('txtAttrubuteName'):'';
		$strLeadAttributeKey	= getSlugify($strLeadAttributeName);
		$strAttributeTypeCode	= ($this->input->post('cboAttributeType') != '')?getDecyptionValue($this->input->post('cboAttributeType')):'';
		$strValidationCode		= ($this->input->post('cboValidation') != '')?getDecyptionValue($this->input->post('cboValidation')):'';
		$isMandatory			= ($this->input->post('rdoisMandatory') != '')?($this->input->post('rdoisMandatory')):'0';
		$blnEditRequest			= (($intLeadAttributeCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr		= array();
		$strAttributeList	 	= ($this->input->post('txtLeadAttributesName'))?serialize($this->input->post('txtLeadAttributesName')):serialize(array());
		
		/* Checking to all valid information passed */
		if(($strLeadAttributeName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead attribute description field is empty.'), true);
		}else if(($strAttributeTypeCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead attribute type is not selected.'), true);
		}else if(($strValidationCode == '') && ($strAttributeTypeCode != '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead attribute validation is not selected.'), true);
		}
		
		/* Adding Status code filter */
		$strWhereClauseArr	= array('attri_slug_key'=>$strLeadAttributeKey,'company_code'=>$this->getCompanyCode());
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intLeadAttributeCode));
		}
		
		/* Checking enter lead attribute slug address is already register or not */
		$strLeadAttribueDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if lead attribute already exists then do needful */
		if(!empty($strLeadAttribueDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Lead Attribute is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
											'data'=>array(
														'attri_continer_code'=>0,
														'attri_slug_key'=>$strLeadAttributeKey,
														'attri_slug_name'=>$strLeadAttributeName,
														'attri_data_type'=>$strAttributeTypeCode,
														'attri_default_value'=>'',
														'attri_value_list'=>$strAttributeList,
														'is_mandatory'=>$isMandatory,
														'attri_validation'=>$strValidationCode,
														'company_code'=>$this->getCompanyCode()
													)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intLeadAttributeCode);
				/* Updating lead details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding lead details in the database */
				$intLeadAttributeCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* Checking for column existence */
			$this->_setLeadTranscationSchema($strLeadAttributeKey);
				
			/* checking last insert id / updated record count */
			if($intLeadAttributeCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Lead Attribute Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Lead Attribute added successfully.'), true);
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
		$intUserRoleCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if not role code pass then do needful */
		if($intUserRoleCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid lead attribute code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intUserRoleCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Lead Attribute deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Updating lead transaction table.
	/*Inputs	: $pStrColumnName :: Column name.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setLeadTranscationSchema($pStrColumnName = ''){	
		/* variable initialization */
		$strTableName	= 'trans_leads_'.$this->getCompanyCode();
		
		/* Checking table exists */
		if($this->_objDataOperation->isTableExists($strTableName)){
			/* checking column exists */
			if(!$this->_objDataOperation->isFiledExists($strTableName, $pStrColumnName)){
				/* loading Database Forge Module */
				$this->load->dbforge();
				
				$strFieldArr	= array(
											$pStrColumnName =>array(
																		'type'=>'VARCHAR',
																		'constraint'=>255,
																		'null'=>true,
																)
									);
	
				$this->dbforge->add_column($strTableName, $strFieldArr);
			}
		}
	}
}