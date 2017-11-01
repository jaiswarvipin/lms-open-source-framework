<?php
/***********************************************************************/
/* Purpose 		: Authentication of user.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user';

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();

		/* Load the login */
		$dataArr['body']	= $this->load->view('auth/login', array(), true);
		
		/* Loading the template for browser rending */
		$this->load->view(DEFAULT_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Authenticating the user.
	/*Inputs	: None.
	/*Returns 	: Authentication response.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function doAuthincation(){
		/* variable initialization */
		$strEmail		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
		$strpassword	= ($this->input->post('txtPassword') != '')?$this->input->post('txtPassword'):'';

		/* if email or password filed is empty then do needful */
		if(($strEmail == '') || ($strpassword == '')){
			jsonReturn(array('status'=>0,'message'=>'Email address or password value is empty.'), true);
		}

		/* Creating the common DML object reference */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Checking for requested user authentication */
		$strResponseArr	= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('user_email'=>$strEmail,'password'=>md5($strpassword))));
		/* removed used variables */
		unset($ObjdbOperation);

		/* Checking user existence response */
		if(empty($strResponseArr)){
			/* if no response found then do needful */
			jsonReturn(array('status'=>0,'message'=>'Invalid email address or password.'), true);
		/* if user is not active in the system then do needful */
		}else if($strResponseArr[0]['is_active'] != 1){
			/* if user is not active then do needful */
			jsonReturn(array('status'=>0,'message'=>'Requested login is disabled. Kindly contact to Company Administrator.'), true);
		}else{
			/* Creating logger object */
			$objLogger	= new Logger();
			/* LOgger Object registration request */
			$objLogger->setLogger($strResponseArr[0]['id']);
			/* Removed used variable */
			unset($objLogger);

			/* return response */
			jsonReturn(array('status'=>1,'message'=>'Valid account .','destinationURL'=>SITE_URL.'dashboard'),true);
		}
	}
}