<?php 
/*******************************************************************************/
/* Purpose 		: Managing the email related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Emailprocess{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_email";
	
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code.
	/* Returns	: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
	}
	
	/***************************************************************************/
	/* Purpose	: Sending email.
	/* Inputs 	: pEmailTemplateCode :: Email template code,
				: pIntSendingUserCode :: Receiver user code,
				: pIntLeadCode	:: Lead Code,
	/* Returns	: TRUE / FALSE.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function sendEmail($pEmailTemplateCode = 0, $pIntSendingUserCode = 0, $pIntLeadCode = 0){
		/* Variable initialization */
		$pResponseArr 	= array('status'=>false, 'message'=>'');
		
		/* Checking for is template code passed */
		if($pEmailTemplateCode == 0){
			/* Return response array */
			return $pResponseArr['message']	= "Requested parameters are not passed. 1) Template Code : ".$pEmailTemplateCode.", 2. User Code : ".$pIntSendingUserCode;
		}
		
		/* Setting user details */
		$strUserArr	= $this->_getUserDetails($pIntSendingUserCode);
		/* Checking for is user details */
		if(empty($strUserArr)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested user details is not found; Company Code : ".$this->_intCompanyCode.', user code : '.$pIntSendingUserCode;
		}
		
		/* Get email template details */
		$strEmailDetailsArr	=  $this->_getEmailTemplateDetails($pEmailTemplateCode);
		/* Checking for is email template details */
		if(empty($strEmailDetailsArr)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested email template details is not found; Company Code : ".$this->_intCompanyCode.', template code : '.$pEmailTemplateCode;
		}
		
		/* variable initialization */
		$strLeadArr	= array();
		/* if lead code is passed then do needful */
		if((int)$pIntLeadCode > 0){
			/* Setting lead details */
			$strLeadArr	= $this->_getLeadDetails($pIntLeadCode);
		}
		
		/* Setting the email template dynamic content details */
		$strEmailBody	= $this->_setTemplateContent($pEmailTemplateCode, $strEmailDetailsArr, $strUserArr, $strLeadArr);
		/* Checking for is email body details */
		if(empty($strEmailBody)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested email body rules is not found; Company Code : ".$this->_intCompanyCode.', template code : '.$pEmailTemplateCode;
		}
		
		/* Sending Email */
		if((isset($_COOKIE['email'])) && (mail($strUserArr[0]['user_email'],$strEmailDetailsArr[0]['email_subject'],$strEmailBody))){
			/* Return response array */
			$pResponseArr['message']	= $strEmailBody;
			$pResponseArr['status']		= true;
		}else{
			/* Return response array */
			$pResponseArr['message']	= 'Error occurred while sending email.';
			$pResponseArr['message']	= $strEmailBody;
			$pResponseArr['status']		= true;
		}
		/* Returns email sending status */
		return $pResponseArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get user details by user code.
	/* Inputs 	: pIntUserCode :: Email user code.
	/* Returns	: User details array.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getUserDetails($pIntUserCode = 0 ){
		/* Variable initialization */
		$strRerturnArr	= array();
		/* If user code is empty then do needful */
		if($pIntUserCode == 0){
			/* Return the response */
			return $strRerturnArr;
		}
		
		/* get sending use details */
		$strRerturnArr = $this->_databaseObject->getDataFromTable(
																	array(
																			'table'=>"master_user",
																			'where'=>array('company_code'=>$this->_intCompanyCode,'id'=>$pIntUserCode)
																	)
															);
		/* Return the user details */
		return $strRerturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get email template details by template code.
	/* Inputs 	: pIntTemplateCode :: Email template code.
	/* Returns	: Email details array.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getEmailTemplateDetails($pIntTemplateCode = 0 ){
		/* Variable Initialization */
		$strReturnArr	= array();
		/* if email template code is empty then do needful */
		if($pIntTemplateCode  == 0){
			/* Return template code array */
			return $strReturnArr;
		}
		
		/* get email template details */
		$strRerturnArr = $this->_databaseObject->getDataFromTable(
																	array(
																			'table'=>array($this->_strTableName,"master_email_templates"),
																			'join'=>array('',$this->_strTableName.'.id = master_email_templates.email_code'),
																			'column'=>array('master_email_templates.*'),
																			'where'=>array($this->_strTableName.'.company_code'=>$this->_intCompanyCode,$this->_strTableName.'.id'=>$pIntTemplateCode,'is_active'=>1)
																	)
															);
		
		/* return the template details */
		return $strRerturnArr;
	}
	
	
	/***************************************************************************/
	/* Purpose	: Get lead details by lead code.
	/* Inputs 	: pIntLeadCode :: Lead code.
	/* Returns	: lead details array.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getLeadDetails($pIntLeadCode = 0 ){
		/* Variable Initialization */
		$strReturnArr	= array();
		/* if lead code is empty then do needful */
		if($pIntLeadCode  == 0){
			/* Return template code array */
			return $strReturnArr;
		}
		
		/* get lead details */
		$strRerturnArr = $this->_databaseObject->getDataFromTable(
																	array(
																			'table'=>array("master_leads","trans_leads_".$this->_intCompanyCode),
																			'join'=>array('','master_leads.id = trans_leads_'.$this->_intCompanyCode.'.lead_code'),
																			'column'=>array('trans_leads_'.$this->_intCompanyCode.'.*'),
																			'where'=>array('master_leads.id'=>$pIntLeadCode)
																	)
															);
		
		/* return the lead details */
		return $strRerturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Email template dynamic place replacement.
	/* Inputs 	: $pIntTemplateCode :: Email template code,
				: $pStrEmailTemplateArr :: Email template details array,
				: pStrUserArr :: User detail array,
				: pStrLeadArr :: Lead array.
	/* Returns	: Final email body.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _setTemplateContent($pIntTemplateCode = 0, $pStrEmailTemplateArr = array(), $pStrUserArr = array(), $pStrLeadArr = array()){
		/* Variable Initialization */
		$strRuleArr		= $this->_getTemplateRule($pIntTemplateCode);
		$strEmailBody	= '';
		
		/* if not rule is set the do needful */
		if(empty($strRuleArr)){
			/* Return empty email body */
			return $strEmailBody;
		}
		
		/* Value overriding */
		$strEmailBody	= $pStrEmailTemplateArr[0]['email_body'];
		
		/* Iterating the rule loop */
		foreach($strRuleArr as $strRuleArrKey => $strRuleArrValue){
			/* get value */
			$strKeyValue	= (isset($pStrEmailTemplateArr[0][$strRuleArrValue])?$pStrEmailTemplateArr[0][$strRuleArrValue]:(isset($pStrUserArr[0][$strRuleArrValue])?$pStrUserArr[0][$strRuleArrValue]:(isset($pStrLeadArr[0][$strRuleArrValue])?$pStrLeadArr[0][$strRuleArrValue]:'')));
			/* Replacing the value */
			$strEmailBody	= str_replace('{'.$strRuleArrKey.'}',$strKeyValue,$strEmailBody);
		}
		
		/* removed used variables */
		unset($strRuleArr);
		
		/* Return Email Body */
		return $strEmailBody;
	}
	
	/***************************************************************************/
	/* Purpose	: Email template dynamic place replacement rule.
	/* Inputs 	: $pIntTemplateCode :: template code,
	/* Returns	: Template decoding rule.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getTemplateRule($pIntTemplateCode = 0){
		/* Variable initialization */
		$strReturnRuleArray = array();
		
		/* based on the template setting the rules */
		switch($pIntTemplateCode){
			/* Lead assignment */ 
			case LEAD_ASSIGMENT_EMAIL:
				/* Setting Rules */
				$strReturnRuleArray	= array('USER_NAME'=>'user_name','LEAD_NAME'=>'name','LEAD_NUMBER'=>'');
				break;
		}
		
		/* Return the rules */
		return $strReturnRuleArray;
	}
}