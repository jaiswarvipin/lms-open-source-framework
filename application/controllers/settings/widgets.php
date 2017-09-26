<?php
/***********************************************************************/
/* Purpose 		: Application Widget List management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Widgets extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_widget';
	private $_strModuleName			= "Widgets";
	private $_strModuleForm			= "frmWidget";
	
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
		
		/* Getting widget access list */
		$strDataArr['dataSet'] 				= $this->_getWidgetsDetails(0,'',false,false, $intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getWidgetsDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strDataArr['deleteUri']			= '';
		$strDataArr['getRecordByCodeUri']	= '';
		$strDataArr['strDataAddEditPanel']	= '';
		$strDataArr['noSearchAdd']			= 'yes';
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/widgets', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Getting the widgets details.
	/*Inputs	: $pModuleCode :: Module code,
				: $pStrModuleName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getWidgetsDetails($pModuleCode = 0, $pStrModuleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strDataArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>array());
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pModuleCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the module list */
		$strModuleArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		$strModuleArr[0]['role_code'] = $pModuleCode;
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strModuleArr;
	}
}