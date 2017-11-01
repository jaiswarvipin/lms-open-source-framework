<?php
/***********************************************************************/
/* Purpose 		: Employee Performance cron.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Employeeperformance extends Requestprocess {
	/* variable deceleration */
	private $_intYesterDate			= 0;
	private $_isDebug				= false;
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* getting yesterday */
		$this->_intYesterDate	= (isset($_REQUEST['date']))?$_REQUEST['date']:date('Ymd', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		
		/* Setting debug */
		$this->_isDebug			= (isset($_REQUEST['debug']))?true:false;
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		
		$this->_process();
	}
	
	/**********************************************************************/
	/*Purpose 	: process the data and create the array.
	/*Inputs	: none.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _process(){
		/* Variable initialization */
		$strStatusListArr	= $strMessageArr	= $strFilterArr	= array();
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Processing For Date ----------------');
			debugVar($this->_intYesterDate);
		}
		
		/* Get status list */
		$strStatusListArr	= $this->_getStatusList();
		/* Get lead counts */
		$strLeadCountArr	= $this->_getOpenAndCloseLeadDetails();
		
		/* if lead count array is empty then do needful */
		if((empty($strLeadCountArr)) || (empty($strStatusListArr))){
			/* Setting error message */
			$strMessageArr	= jsonReturn(array('status'=>0,'message'=>'No lead details found.'));
		}else{
			/* Deactivate requested date data */
			$this->_objDataOperation->setUpdateData(array('table'=>'trans_rpt_employee_performance','data'=>array('deleted'=>1),'where'=>array('record_date'=>$this->_intYesterDate)));
			
			/* Iterating the loop */
			foreach($strLeadCountArr as $strLeadCountArrKey => $strLeadCountArrValue){
				/* checking for open lead count */
				if(in_array($strLeadCountArrValue['status_code'],$strStatusListArr[OPEN_CLOSURE_STATUS_CODE])){
					/* Checking for array index not set */
					if(!isset($strFilterArr[$strLeadCountArrValue['company_code']][OPEN_CLOSURE_STATUS_CODE])){
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][OPEN_CLOSURE_STATUS_CODE]	= $strLeadCountArrValue;
					}else{
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][OPEN_CLOSURE_STATUS_CODE]['lead_count']	+= $strLeadCountArrValue['lead_count'];
					}
				}else if(in_array($strLeadCountArrValue['status_code'],$strStatusListArr[POSITIVE_CLOSURE_STATUS_CODE])){
					/* Checking for array index not set */
					if(!isset($strFilterArr[$strLeadCountArrValue['company_code']][POSITIVE_CLOSURE_STATUS_CODE])){
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][POSITIVE_CLOSURE_STATUS_CODE]	= $strLeadCountArrValue;
					}else{
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][POSITIVE_CLOSURE_STATUS_CODE]['lead_count']	+= $strLeadCountArrValue['lead_count'];
					}
				}else{
					/* Checking for array index not set */
					if(!isset($strFilterArr[$strLeadCountArrValue['company_code']][NEGATIVE_CLOSURE_STATUS_CODE])){
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][NEGATIVE_CLOSURE_STATUS_CODE]	= $strLeadCountArrValue;
					}else{
						/* Setting the index and value */
						$strFilterArr[$strLeadCountArrValue['company_code']][NEGATIVE_CLOSURE_STATUS_CODE]['lead_count']	+= $strLeadCountArrValue['lead_count'];
					}
				}
			}
		}
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Final Processing Data ----------------');
			debugVar($strFilterArr);
		}
		
		/* if resulting array is not empty then do needful */
		if(!empty($strFilterArr)){
			/* Iterating the array */
			foreach($strFilterArr as $strFilterArrKey => $strCompanyArr){
				/* Iterating the array */
				foreach($strCompanyArr as $strValueKey => $strValueDetails){
					/* Creating the insert array */
					$strInsertArr = array_merge($strValueDetails , array('manager_code'=>0,'record_date'=>$this->_intYesterDate, 'updated_by'=>1,'status_type'=>$strValueKey));
					/* removed used index  */
					unset($strInsertArr['status_code']);
					/* Inserting into table */
					$this->_objDataOperation->setDataInTable(array('table'=>'trans_rpt_employee_performance','data'=>$strInsertArr));
				}
			}
			
			if(empty($strMessageArr)){
				/* Setting success message */
				$strMessageArr	= jsonReturn(array('status'=>1,'message'=>'Process run successfully'));
			}
			
			/* Deactivate requested date data */
			$this->_objDataOperation->setUpdateData(array('table'=>'trans_cron','data'=>array('corn_value'=>$strMessageArr),'where'=>array('id'=>2)));			
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Status List of all company.
	/*Inputs	: None
	/*Returns	: Status List.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getStatusList(){
		/* Variable initialization */
		$statusObj		= new Status($this->_objDataOperation, 0);
		$strReturnArr	= array(OPEN_CLOSURE_STATUS_CODE=>array(),POSITIVE_CLOSURE_STATUS_CODE=>array(),NEGATIVE_CLOSURE_STATUS_CODE=>array());
		
		/* Get all company status list array */
		$strStatusArr	=  $statusObj->getLeadStatusByCompanyCode();
		/* Removed used variables */
		unset($statusObj);
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Status Row Details ----------------');
			debugVar($strStatusArr);
		}
		
		/* Iterating the loop */
		foreach($strStatusArr as $strStatusArrKey => $strStatusArrValue){
			/* Chceking for open status */
			if((int)$strStatusArrValue['parent_id'] == OPEN_CLOSURE_STATUS_CODE){
				/* Setting open lead status */
				$strReturnArr[OPEN_CLOSURE_STATUS_CODE][$strStatusArrValue['id']]	= $strStatusArrValue['id'];
			}else if((int)$strStatusArrValue['parent_id'] == NEGATIVE_CLOSURE_STATUS_CODE){
				/* Setting negative lead status */
				$strReturnArr[NEGATIVE_CLOSURE_STATUS_CODE][$strStatusArrValue['id']]	= $strStatusArrValue['id'];
			}else  if((int)$strStatusArrValue['parent_id'] == POSITIVE_CLOSURE_STATUS_CODE){
				/* Setting positive lead status */
				$strReturnArr[POSITIVE_CLOSURE_STATUS_CODE][$strStatusArrValue['id']]	= $strStatusArrValue['id'];
			}
		}
		/* removed used variables */
		unset($strStatusArr);
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Status Formatted Details ----------------');
			debugVar($strReturnArr);
		}
		
		/* Return Status Array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get CRON date open or close lead details.
	/*Inputs	: None.
	/*Returns	: open or close lead list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getOpenAndCloseLeadDetails(){
		/* Variable initialization */
		$strReturnArr	= array();
	
		/* Filter array */
		$strFilterArr	= array(
									'table'=>'trans_rpt_leads',
									'where'=>array('record_date'=>$this->_intYesterDate),
									'column'=>array('count(id) as lead_count','company_code','status_code','region_code','branch_code','lead_owner_code','lead_source_code','record_date'),
									'group'=>array('company_code','status_code','region_code','branch_code','lead_owner_code','lead_source_code')
							);
		
		/* Get new count */
		$strReturnArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Open Leads Details ----------------');
			debugVar($strReturnArr);
		}
		
		/* removed used variables */
		unset($strOpenArry , $strFilterArr);
		
		/* Return new lead */
		return $strReturnArr;
	}
}