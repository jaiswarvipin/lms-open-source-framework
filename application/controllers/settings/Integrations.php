<?php
/***********************************************************************/
/* Purpose 		: Application integration manager.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Integrations extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_lead_attributes';
	private $_strModuleName			= "API Integration";
	private $_strModuleForm			= "frmAPIIntegration";
	
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
		
		/* Getting API integration content list */
		$strUserRoleArr['dataSet'] 				= $this->_getLeadAttributesDetails();
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getModuesDetailsByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'apiIntegrationModel';
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/integrations', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Getting the lead attribute code details.
	/*Inputs	: None.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadAttributesDetails(){
		/* variable initialization */
		$strReturnnArr	= $strWhereClauseArr 	= array();
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* Getting the lead attributes list */
		$strReturnnArr['lead_attri'] =  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Filter array */
		$strFilterArr	= array('table'=>'master_lead_source','where'=>$strWhereClauseArr);
		
		/* Getting the lead source list */
		$strReturnnArr['lead_source']	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr, $strWhereClauseArr);

		/* return status */
		return $strReturnnArr;
	}
}