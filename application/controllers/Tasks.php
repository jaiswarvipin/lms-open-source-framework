<?php
/***********************************************************************/
/* Purpose 		: Manage the task related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks	 extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_leads';
	private $_strModuleName			= "Tasks";
	private $_strModuleForm			= "frmTask";
	private $_strColumnArr			= array();
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		/* Variable initialization */
		$this->_strColumnArr	= array();
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
		/* Getting 	column Array */
		$this->_strColumnArr					= $this->_getColumnArr();
		
		/* Getting module list */
		$strDataArr['strColumnsArr'] 		= $this->_strColumnArr;
		$strDataArr['dataSet'] 				= $this->_getLeadsDetails(0,'',false,false, $intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getLeadsDetails(0,'',false,true), ($intCurrentPageNumber + 1), 'frmLeadsColumnSearch');
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.__CLASS__.'/getLeadDetailsWithRequest';
		$strDataArr['strDataAddEditPanel']	= 'taskModules';
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strDataArr['strAddPanel']			= $this->getLeadOperationPanel(0);
		$strDataArr['strLeadFollowuppanel']	= $this->getLeadOperationPanel(1);
		$strDataArr['strLeadTransferPanel']	= $this->getLeadOperationPanel(2);
		$strDataArr['strLeadProfile']		= $this->getLeadOperationPanel(3);
		$strDataArr['strColumnSearchPanel']	= $this->getColumnAsSearchPanel(array_merge($this->_strColumnArr,array('frmName'=>'frmLeadsColumnSearch')),SITE_URL.__CLASS__);
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('leads/all-leads', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get module details by code.
	/*Inputs	: None.
	/*Returns 	: Module details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadDetailsWithRequest(){
		/* Setting the module code */
		$intModuleCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$intModuleCodeFoAttr= ($this->input->post('txtModuleFieldCode') != '') ? getDecyptionValue($this->input->post('txtModuleFieldCode')) : 0;
		$strModulesArr		= array();
		
		if($intModuleCodeFoAttr > 0){
			/* getting requested module field code details */
			$strModulesArr	= $this->_getLeadAttributesByModuleCode($intModuleCodeFoAttr);
			
			/* if record not found then do needful */
			if(empty($strModulesArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strModulesArr, true);
			}
			/* Checking the module code shared */
		}else if($intModuleCode > 0){
			/* getting requested module code details */
			$strModulesArr	= $this->_getLeadsDetails($intModuleCode);
			
			/* if record not found then do needful */
			if(empty($strModulesArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strModulesArr, true);
			}
			
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid module code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the module details.
	/*Inputs	: $pLeadCode :: Module code,
				: $pStrModuleName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadsDetails($pLeadCode = 0, $pStrModuleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strReturnArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Get all open lead status list */
		$strStatusArr	= $this->getLeadStatusBasedOnRequest(array(OPEN_CLOSURE_STATUS_CODE));
		/* Checking responsed status list is not empty and having requested status index */
		if(!empty($strStatusArr) && (isset($strStatusArr[OPEN_CLOSURE_STATUS_CODE]))){
			/* Setting the operation status code */
			$strStatusArr	= array_keys($strStatusArr[OPEN_CLOSURE_STATUS_CODE]);
		}else{
			/* re-initialization of the status array */
			$strStatusArr	= array();
		}
		/* Settting the filter caluse */
		$strWhereClauseArr	= array('status_code'=>$strStatusArr);
		
		/* if profile filter code is passed then do needful */
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* Iterating the search object */
			foreach($this->input->post() as $strPostObjectKey => $strPostObjectValue){
				/* Checking for search column */
				if(strstr($strPostObjectKey,'txtSearch')){
					/* Creating the column filter */
					$strColumnName	= str_replace('txtSearch', '', $strPostObjectKey);
					/* Creating the value */
					$strValue		= ((trim($strPostObjectValue)!= '') && (trim($strPostObjectValue) != 'null'))?$strPostObjectValue:'';
					/* if value is not empty then do needful */
					if(($strValue != '') && ($strColumnName != '')){
						/* checking for index column */
						if(strstr($strColumnName,'_code')){
							if(is_numeric(getDecyptionValue($strValue))){
								$strValue	= getDecyptionValue($strValue);
							}else{
								$strValue	= getDecyptionValue(getDecyptionValue($strValue));
							}
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName=>$strValue));
						}else{
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName.' like'=>$strValue));
						}
					}
				}
			}
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && (!$pBlnCountNeeded)){
			$strWhereClauseArr['offset'] = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strWhereClauseArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Creating task object */
		$taskObj = new Task($this->_objDataOperation, $this->getCompanyCode(), $this->getBranchCodes());
		/* Get task details */
		$strLeadArr	= $taskObj->getTaskList($pBlnCountNeeded, $strWhereClauseArr);
		/* removed used variables */
		unset($taskObj);
		
		/* if lead details found then do needful */
		if(!empty($strLeadArr)){
			/* Lead count */
			if($pBlnCountNeeded){
				/* Variable initialziation */
				$intREcordCount = 0;
				/* Iterating the loop */
				foreach($strLeadArr as $strLeadArrKey => $strLeadArrValue){	
					/* Setting counter */
					$intREcordCount	+= $strLeadArrValue['recordCount'];
				}
				$strReturnArr[0]['recordCount'] = $intREcordCount;
			}else{
				/* Setting current date and time */
				$intTodaysDate	= date('Ymd');
				
				/* Iterating the loop */
				foreach($strLeadArr as $strLeadArrKey => $strLeadArrValue){	
					/* Iterating the lead details array */
					foreach($this->_strColumnArr as $strColumnArrKey => $strColumnArrValue){
						/* Setting Value */
						$strReturnArr[$strLeadArrKey][$strColumnArrValue['column']]	= $this->getLeadAttributeDetilsByAttributeKey($strColumnArrValue['column'], $strLeadArrValue[$strColumnArrValue['column']]);
					}
					/* Setting the lead code */
					$strReturnArr[$strLeadArrKey]['lead_code']		= $strLeadArrValue['lead_code'];
					$strReturnArr[$strLeadArrKey]['is_open']		= 1;
					
					/* Checking for today's task  */
					if((int)substr($strLeadArrValue['next_followup_date'],0,8)  == (int)$intTodaysDate){
						/* Setting the task colour code */
						$strReturnArr[$strLeadArrKey]['task_notifi']	= ' style="background-color:green;color:#ffffff;"';
					/* Checking for Pending date task  */
					}else if((int)substr($strLeadArrValue['next_followup_date'],0,8)  < (int)$intTodaysDate){
						/* Setting the task colour code */
						$strReturnArr[$strLeadArrKey]['task_notifi']	= ' style="background-color:red;color:#ffffff;"';
					}else{
						/* Setting the task colour code */
						$strReturnArr[$strLeadArrKey]['task_notifi']	= '';
					}
				}
			}
		}
		
		/* Removed used variables */
		unset($strLeadArr);
		
		/* return status */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead attribute list.
	/*Inputs	: None.
	/*Returns 	: Led attributes list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getColumnArr(){
		/* Variable initialization */
		$strReturnArr	= array();
		/* Getting configured column */
		$strModuleArr 	= $this->getModuleAssociatedFieldByModuleURL($this->uri->segment(1));
		
		$strReturnArr[]	= array('column'=>'next_followup_date','label'=>'Follow-Up Date','is_date'=>'1');
		
		/* If configured fields is not empty then do needful */
		if(!empty($strModuleArr)){
			/* iterating the loop */
			foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
				/* Setting Column */
				$strReturnArr[]	= array('column'=>$strModuleArrValue['attri_slug_key'],'label'=>$strModuleArrValue['attri_slug_name']);
			}
		}
		/* Removed used variables */
		unset($strModuleArr);
		
		$strReturnArr[]	= array('column'=>'region_code','label'=>'Region','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getRegionDetails(),''));
		$strReturnArr[]	= array('column'=>'branch_code','label'=>'Branch','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getBranchDetails(),''));
		$strReturnArr[]	= array('column'=>'lead_owner_code','label'=>'Lead owner','dropdown'=>'1','data'=>'');
		$strReturnArr[]	= array('column'=>'comments','label'=>'comments','data'=>$this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),''));
		
		/* Return column array */
		return $strReturnArr;
	}
}