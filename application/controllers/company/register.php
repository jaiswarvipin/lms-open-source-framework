<?php
/***********************************************************************/
/* Purpose 		: Company request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_company';
	private $_strSecondaryTableName	= 'master_user';

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();

		/* Load the registration */
		$dataArr['body']	= $this->load->view('company/register', array(), true);
		
		/* Loading the template for browser rending */
		$this->load->view(DEFAULT_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Authenticating the user.
	/*Inputs	: None.
	/*Returns 	: Authenticating response.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function doRegistration(){
		/* variable initialization */
		$strCompanyName	= ($this->input->post('txtCompanyName') != '')?$this->input->post('txtCompanyName'):'';
		$strAdminName	= ($this->input->post('txtAdminName') != '')?$this->input->post('txtAdminName'):'';
		$strEmail		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
		$strpassword	= ($this->input->post('txtPassword') != '')?$this->input->post('txtPassword'):'';
		$intResponseCode= 0;

		/* if email or password filed is empty then do needful */
		if(($strEmail == '') || ($strpassword == '') || ($strCompanyName == '') || ($strAdminName == '')){
			jsonReturn(array('status'=>0,'message'=>'All fields are mandatory.'), true);
		}
		/* Checking for record existence */
		$this->_doCheckRecordExistance($strCompanyName, $strEmail);

		/* Creating the common DML object reference */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Company Registration */
		$intResponseCode	= $ObjdbOperation->setDataInTable(array('table'=>$this->_strPrimaryTableName, 'data'=>array('name'=>$strCompanyName,'logo'=>'','is_active'=>1)));
		
		/* Checking user existence response */
		if($intResponseCode == 0){
			/* if no response found then do needful */
			jsonReturn(array('status'=>0,'message'=>'Error occurred while registering the Company.'), true);
		/* User Registration */
		}else{
			/* Creating transnational table */
			$this->_setLeadTranscationSchema($intResponseCode);
			
			/* Company Registration */
			$intResponseCode	= $ObjdbOperation->setDataInTable(array('table'=>$this->_strSecondaryTableName, 'data'=>array('user_name'=>$strAdminName,'user_email'=>$strEmail,'password'=>md5($strpassword),'company_code'=>$intResponseCode,'is_admin'=>1,'system_role_code'=>1,'role_code'=>1)));

			/* Checking user existence response */
			if($intResponseCode == 0){
				/* if no response found then do needful */
				jsonReturn(array('status'=>0,'message'=>'occurred while registering the Admin user.'), true);
			}else{
				jsonReturn(array('status'=>1,'message'=>'Company Registration done successfully.','destinationURL'=>SITE_URL.'login'),true);
			}
		}

		/* removed used variables */
		unset($ObjdbOperation);
	}

	/**********************************************************************/
	/*Purpose 	: Checking for company name and admin email existence.
	/*Inputs	: $pStrCompanyName :: Company Name,
				: $pStrEmail :; Admin email address.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doCheckRecordExistance($pStrCompanyName ='' , $pStrEmail = ''){
		/* if any once the string is empty then do needful */
		if(($pStrCompanyName == '') || ($pStrEmail == '')){
			/* return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid company name or Admin email address."), true);
		}

		/* Creating the common DML object reference */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Checking for requested company name */
		$strResponseArr	= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('name like'=>$pStrCompanyName)));

		/* Checking company exists */
		if(!empty($strResponseArr)){
			/* return error message */
			jsonReturn(array('status'=>0,'message'=>"Company already exists with same name."), true);
		}else{
			/* Checking for requested user already exists with requested email address */
			$strResponseArr	= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strSecondaryTableName, 'where'=>array('user_email'=>$pStrEmail)));

			/* if company already exists then do needful */
			if(!empty($strResponseArr)){
				/* return error message */
				jsonReturn(array('status'=>0,'message'=>"User already register with shared email address."), true);
			}
		}
		/* removed used variables */
		unset($ObjdbOperation, $strResponseArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Creating lead transaction table.
	/*Inputs	: $pIntCompnayCode :: Company Code.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setLeadTranscationSchema($pIntCompnayCode = 0){	
		/* variable initialization */
		$strTableName	= 'trans_leads_'.$pIntCompnayCode;
		
		/* Creating the common DML object reference */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Creating table */
		$ObjdbOperation->getDirectQueryResult("CREATE TABLE ".$strTableName."(id BIGINT(20) NOT NULL AUTO_INCREMENT,lead_code BIGINT(20) NOT NULL DEFAULT '0',updated_date BIGINT(20) NOT NULL DEFAULT '0',`updated_by` BIGINT(20) NOT NULL DEFAULT '0', record_date BIGINT(20) NOT NULL DEFAULT '0', region_code BIGINT(20) NOT NULL DEFAULT '0', branch_code BIGINT(20) NOT NULL DEFAULT '0', deleted TINYINT(1) NOT NULL DEFAULT '0',PRIMARY KEY (`id`),KEY `lead_code` (`lead_code`,`updated_date`,`updated_by`,`record_date`,`deleted`),KEY `region_code` (`region_code`),KEY `branch_code` (`branch_code`));");
		
		/* setting lead allocation email and its template */
		$intEmailTemplateCode = $ObjdbOperation->getDirectQueryResult('INSERT INTO master_email(company_code, description, is_system, record_date) SELECT '.$pIntCompnayCode.', description, is_system, '.date('YmdHis').' FROM master_email WHERE id = 6;');
		$ObjdbOperation->getDirectQueryResult('INSERT INTO master_email_templates(email_code, email_subject, email_body, from_name, from_email, black_list_emails, is_active, record_date) SELECT '.$intEmailTemplateCode.', email_subject, email_body, from_name, from_email, black_list_emails, is_active, '.date('YmdHis').' FROM master_email_templates WHERE email_code = 6;');
		
		/* Setting user configuration */
		$ObjdbOperation->getDirectQueryResult('INSERT INTO master_user_config(company_code, key_description, value_description, record_date) SELECT '.$pIntCompnayCode.', key_description, 0, '.date('YmdHis').' FROM master_user_config WHERE company_code = 1;');
		$ObjdbOperation->getDirectQueryResult("UPDATE master_user_config set value_description = ".$intEmailTemplateCode." WHERE company_code = ".$pIntCompnayCode." and key_description='LEAD_ASSIGMENT_EMAIL';");
		
		/* Creating and Setting Default Administrator Role */
		$intRoleCode = $ObjdbOperation->getDirectQueryResult('INSERT INTO master_role(company_code, description, record_date) SELECT '.$pIntCompnayCode.', description, '.date('YmdHis').' FROM master_role WHERE id = 1;');
		$ObjdbOperation->getDirectQueryResult('UPDATE master_user SET role_code = '.$intRoleCode.' WHERE company_code = '.$pIntCompnayCode.' and system_role_code = 1;');
		
		/* Setting the default status */
		$ObjdbOperation->getDirectQueryResult('INSERT INTO master_status(company_code, description, parent_id, is_default, status_type, record_date) SELECT '.$pIntCompnayCode.', description, parent_id, is_default, status_type, '.date('YmdHis').' FROM master_status WHERE id = 8;');
		
		/* removed used variables */
		unset($ObjdbOperation);
	}
}