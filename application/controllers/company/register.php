<?php
/***********************************************************************/
/* Purpose 		: Company request and response management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	/* variable decelarition */
	private $_strPrimaryTableName	= 'master_company';
	private $_strSecondaryTableName	= 'master_user';

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initilaization */
		$dataArr	= array();

		/* Load the registration */
		$dataArr['body']	= $this->load->view('company/register', array(), true);
		
		/* Loading the template for browser rending */
		$this->load->view(DEFAULT_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Authencating the user.
	/*Inputs	: None.
	/*Returns 	: Authiencation response.
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
		/* Checking for record existance */
		$this->_doCheckRecordExistance($strCompanyName, $strEmail);

		/* Creating the common DML object refrence */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Company Registration */
		$intResponseCode	= $ObjdbOperation->setDataInTable(array('table'=>$this->_strPrimaryTableName, 'data'=>array('name'=>$strCompanyName,'logo'=>'','is_active'=>1)));
		

		/* Checking user existance respone */
		if($intResponseCode == 0){
			/* if no response found then do needful */
			jsonReturn(array('status'=>0,'message'=>'Error occured while registraing the Company.'), true);
		/* User Registration */
		}else{
			/* Company Registration */
			$intResponseCode	= $ObjdbOperation->setDataInTable(array('table'=>$this->_strSecondaryTableName, 'data'=>array('name'=>$strAdminName,'email'=>$strEmail,'password'=>md5($strpassword),'company_code'=>$intResponseCode,'is_admin'=>1)));


			/* Checking user existance respone */
			if($intResponseCode == 0){
				/* if no response found then do needful */
				jsonReturn(array('status'=>0,'message'=>'Error occured while registraing the Admin user.'), true);
			}else{
				jsonReturn(array('status'=>1,'message'=>'Company Registration done sucessfully.','destinationURL'=>SITE_URL.'login'),true);
			}
		}

		/* removed used variables */
		unset($ObjdbOperation);
	}

	/**********************************************************************/
	/*Purpose 	: Checking for company name and admin email exitance.
	/*Inputs	: $pStrCompanyName :: Company Name,
				: $pStrEmail :; Admin email address.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doCheckRecordExistance($pStrCompanyName ='' , $pStrEmail = ''){
		/* if any once the string is empty then do needful */
		if(($pStrCompanyName == '') || ($pStrEmail == '')){
			/* reurn error meesage */
			jsonReturn(array('status'=>0,'message'=>"Invalid company name or Admin email address."), true);
		}

		/* Creating the common DML object refrence */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Checking for requested company name */
		$strResponseArr	= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('name like'=>$pStrCompanyName)));

		/* Checking company exists */
		if(!empty($strResponseArr)){
			/* reurn error meesage */
			jsonReturn(array('status'=>0,'message'=>"Company already exists with same name."), true);
		}else{
			/* Checking for requested user alreay exists with requested email address */
			$strResponseArr	= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strSecondaryTableName, 'where'=>array('email'=>$pStrEmail)));

			/* if company alreay exists then do needful */
			if(!empty($strResponseArr)){
				/* reurn error meesage */
				jsonReturn(array('status'=>0,'message'=>"User already register with shared email address."), true);
			}
		}
		/* removed used variables */
		unset($ObjdbOperation, $strResponseArr);

	}
}