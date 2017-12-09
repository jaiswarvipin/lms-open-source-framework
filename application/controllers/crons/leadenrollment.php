<?php
/***********************************************************************/
/* Purpose 		: Enrolling lead in the system (CRON).
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadenrollment extends Requestprocess {
	/* variable deceleration */
	private $_isDebug				= false;
	private $_strLeadAttrArr		= array();
	private $_intleadSourceCode		= 0;
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* Setting debug */
		$this->_isDebug			= (isset($_REQUEST['debug']))?true:false;
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Processing Data Received ----------------');
			debugVar($_REQUEST);
		}
		
		/* Checking for company code */
		$intCompnayCode	= ($this->input->get('company_code') !='')?getDecyptionValue(urldecode($this->input->get('company_code'))):0;
		/* Checking for company code data verification */
		if((!is_numeric($intCompnayCode)) || ($intCompnayCode == 0)){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>'In-valid company code / key.'), true);
		}
		
		/* Checking for requested company existence */ 
		if(!$this->_isCompanyExists($intCompnayCode)){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>'Requested company not register with us. In-valid company code / key.'), true);
		}
		
		/* Get lead attributes of requested company */
		$this->_strLeadAttrArr 	= $this->_getLeadAttributeByCompanyCode($intCompnayCode);
		
		/* is lead attribute is not set then */
		if(empty($this->_strLeadAttrArr)){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>'Lead attributes is not set for requested key. Kindly contact to system administrator.'), true);
		}
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------lead Attributes List ----------------');
			debugVar($this->_strLeadAttrArr);
		}
		
		/* Verifying passed lead information is align with configured lead attributes */
		$this->_validateLeadAttrubites($intCompnayCode);
		
		/* start the execution  process */
		$this->_process($intCompnayCode);
	}
	
	/**********************************************************************/
	/*Purpose 	: process the data and create lead.
	/*Inputs	: $intCompnayCode :: company code.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _process($pIntCompanyCode = 0){
		/* company code is not set then do needful */
		if($pIntCompanyCode == 0){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>'Looks in-valid company code passed, while creating lead.'), true);
		}
		
		/* Get user configuration array set */
		$strUserConfigArr	= $this->_getUserConfig($pIntCompanyCode);
		$intLeadOwnerCode	= 0;
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------User Configuration Default  ----------------');
			debugVar($strUserConfigArr);
		}
		
		/* if user configuration found then do needful */
		if((isset($strUserConfigArr['lead_owner_code']))  && ((int)$strUserConfigArr['lead_owner_code'] > 0)){
			/* Setting lead owner */
			$intLeadOwnerCode	= $strUserConfigArr['lead_owner_code'];
		}
		
		/* variable initialization */
		$strLeadAttrArr	= array();
		/* Iterating the lead attribute array set */
		foreach($this->_strLeadAttrArr as $strLeadAttrArrKey => $strLeadAttrArrValue){
			/* Setting the value */
			$strLeadAttrArr[$strLeadAttrArrValue['attri_slug_key']]	= $_REQUEST[$strLeadAttrArrValue['attri_slug_key']];
		}
		
		/* final array for creating lead */
		$strLeadDetailsArr	= array_merge($strLeadAttrArr, array('is_debug'=>$this->_isDebug,'lead_owner_code'=>$intLeadOwnerCode,'lead_source_code'=>$this->_intleadSourceCode,'lead_source_code'=>$this->_intleadSourceCode));
		/* Lead object */
		$leadObj = new Lead($this->_objDataOperation, $pIntCompanyCode);
		/* Creating lead */
		$intLeadCode	= $leadObj->setLeadDetails($strLeadDetailsArr);
		/* removed used variables */
		unset($leadObj);
		
		/* if lead generated then do needful */
		if((int)$intLeadCode > 0){
			jsonReturn(array('status'=>true,'lead_code'=>$intLeadCode,'message'=>'lead added successfully.'),true);
		}else{
			jsonReturn(array('status'=>false,'message'=>'error occurred while processing the lead request. Kindly contact to system administrator.'),true);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking requested company is exists or not.
	/*Inputs	: $pIntCompanyCode :: Company code.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _isCompanyExists($pIntCompanyCode = 0){
		/* Variable initialization */
		$blnReturn = false;
		/* if company code is passed then */
		if($pIntCompanyCode > 0){
			/* Query builder array */
			$strQueryArr	= array(
										'table'=>'master_company',
										'column'=>array('id'),
										'where'=>array('id'=>$pIntCompanyCode)
								);
			/* checking for company existence */
			$strResult	= $this->_objDataOperation->getDataFromTable($strQueryArr);
			/* If company found then do needful */
			if(!empty($strResult)){
				/* Value overriding */
				$blnReturn = true;
			}
			
			/* Removed used variables */
			unset($strQueryArr);
			
		}
		
		/* Return the company existence status */
		return $blnReturn;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking requested lead source exists or not.
	/*Inputs	: $pStrLeadSource :: lead source,
				: $pIntCompanyCode :: Company code.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _isLeadSourceExists($pStrLeadSource = '', $pIntCompanyCode = 0){
		/* Variable initialization */
		$intLeadSourceCode = false;
		/* if company code is passed then */
		if(($pStrLeadSource != '') && ($pIntCompanyCode > 0)){
			/* Query builder array */
			$strQueryArr	= array(
										'table'=>'master_lead_source',
										'column'=>array('id'),
										'where'=>array('description'=>$pStrLeadSource,'company_code'=>$pIntCompanyCode)
									);
			/* checking for lead source existence */
			$strResult	= $this->_objDataOperation->getDataFromTable($strQueryArr);
			/* If lead source found then do needful */
			if(!empty($strResult)){
				/* Value overriding */
				$intLeadSourceCode = $strResult[0]['id'];
			}
			
			/* Removed used variables */
			unset($strQueryArr);
			
		}
		
		/* Return the lead source existence status */
		return $intLeadSourceCode;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead attributes of requested company.
	/*Inputs	: $pIntCompanyCode :: Company code.
	/*Returns	: Lead attribute list array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadAttributeByCompanyCode($pIntCompanyCode = 0){
		/* variable initialization */
		$strReturnArr = array();
		/* if company code is zero then do needful */
		if($pIntCompanyCode == 0){
			/* return the empty array */
			return $strReturnArr;
		}
		
		/* Lead object */
		$leadObj = new Lead($this->_objDataOperation, $pIntCompanyCode);
		/* Get lead attribute of same company */
		$strReturnArr	= $leadObj->getLeadAttributesListByCompnayCode();
		/* removed used variables */
		unset($leadObj);
		
		/* return lead attribute array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get default region and branch code of requested company.
	/*Inputs	: $pIntCompanyCode :: Company code.
	/*Returns	: Region and Branch code array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserConfig($pIntCompanyCode = 0){
		/* variable initialization */
		$strReturnArr = array();
		/* if company code is zero then do needful */
		if($pIntCompanyCode == 0){
			/* return the empty array */
			return $strReturnArr;
		}
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>'master_user_config',
									'where'=>array('company_code'=>$pIntCompanyCode)
								);
								
		/* checking for user configuration array */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		/* If configuration found then do needful */
		if(!empty($strResultArr)){
			/* iterating the loop */
			foreach($strResultArr as $strResultArrKey => $strResultArrValue){
				$strFromIndex	= $strResultArrValue['key_description'];
				/* based on index setting the value */
				switch($strResultArrValue['key_description']){
					case 'DEFAULT_REGION':
						$strFromIndex	= 'region_code';
						break;
					case 'DEFAULT_BRANCH':
						$strFromIndex	= 'branch_code';
						break;
					case 'DEFAULT_LEAD_ALLOCATED_TO':
						$strFromIndex	= 'lead_owner_code';
						break;
					default:
						$strFromIndex	= $strResultArrValue['key_description'];
						break;
				}
				/* Setting value */
				$strReturnArr[$strFromIndex]	= $strResultArrValue['value_description'];
			}
		}
		
		/* Removed used variables */
		unset($strQueryArr, $strResultArr);
		
		/* return lead attribute array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: verifying requested lead attributes with its data type.
	/*Inputs	: $pIntCompanyCode :: Company code.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _validateLeadAttrubites($pIntCompanyCode = 0){
		/* variable initialization */
		
		/* iterating the lead attributes loop */
		foreach($this->_strLeadAttrArr as $strLeadAttrArrKey => $strLeadAttrArrValue){
			/* checking mandatory value is exists */
			if((!isset($_REQUEST[$strLeadAttrArrValue['attri_slug_key']])) &&($strLeadAttrArrValue['is_mandatory'] == 1)){
				/* return the error response */ 
				jsonReturn(array('status'=>'error','message'=>"'".$strLeadAttrArrValue['attri_slug_key']."', lead attributes is not passed."), true);
				/* checking mandatory value is exists */
			}else if((isset($_REQUEST[$strLeadAttrArrValue['attri_slug_key']])) && ($strLeadAttrArrValue['is_mandatory'] == 1) && (trim($_REQUEST[$strLeadAttrArrValue['attri_slug_key']])	=='')){
				/* return the error response */ 
				jsonReturn(array('status'=>'error','message'=>"In-valid value passed to '".$strLeadAttrArrValue['attri_slug_key']."', lead attributes."), true);
			}
		}
		
		/* Checking for lead source */
		if((!isset($_REQUEST['lead_source_code'])) || (trim($_REQUEST['lead_source_code']) =='')){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>"In-valid value passed to 'lead_source_code', lead attributes."), true);
		}
		
		/* setting lead source */
		$this->_intleadSourceCode	= $this->_isLeadSourceExists($_REQUEST['lead_source_code'], $pIntCompanyCode);
		/* checking is requested lead source is exists */
		if($this->_intleadSourceCode == 0){
			/* return the error response */ 
			jsonReturn(array('status'=>'error','message'=>"'".$_REQUEST['lead_source_code']."' as lead source, is not register, please contact to system administrator."), true);
		}
	}
}