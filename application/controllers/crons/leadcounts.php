<?php
/***********************************************************************/
/* Purpose 		: Lead count cron.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadcounts extends Requestprocess {
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
		$strStatusListArr	= $strMessageArr	= $strFilterArr	= $srrWhereArr	= array();
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Processing For Date ----------------');
			debugVar($this->_intYesterDate);
		}
		
		$strCompanyArr		= $this->_getCompanyList();
		$strStatusListArr	= $this->_getStatusList();
		
		/* if no active company found then do needful */
		if(empty($strCompanyArr)){
			/* Setting error message */
			$strMessageArr	= jsonReturn(array('status'=>0,'message'=>'No company details found.'));
		}else{
			/* Deactivate requested date data */
			$this->_objDataOperation->setUpdateData(array('table'=>'trans_rpt_leads','data'=>array('deleted'=>1),'where'=>array('record_date'=>$this->_intYesterDate)));
			
			/* Iterating the company loop */
			foreach($strCompanyArr as $strCompanyArrKey => $strCompanyArrValue){
				/* Get company status array */
				$strCompanyStatusArr	= isset($strStatusListArr[$strCompanyArrValue['id']])?$strStatusListArr[$strCompanyArrValue['id']]:array();
				/* Get default status code */
				$intDefaultCode			= isset($strStatusListArr[$strCompanyArrValue['id']]['default'])?$strStatusListArr[$strCompanyArrValue['id']]['default']:0;
				
				/* if company wise status is not set or default status is not set then do needful */
				if(empty($strCompanyStatusArr) || ($intDefaultCode == 0)){
					/* Setting error message */
					$strMessageArr	= jsonReturn(array('status'=>0,'message'=>'Status is not set.'));
					/* continue the loop */
					continue;
				}
				/* Get new lead register */
				$strNewLeadArr	= $this->_getOpenAndCloseLeadDetails($strCompanyArrValue['id'], array($intDefaultCode=>$intDefaultCode));
				
				/* Open lead status array */
				$strOpenLeadArray	= isset($strCompanyStatusArr[OPEN_CLOSURE_STATUS_CODE]['child'])?$strCompanyStatusArr[OPEN_CLOSURE_STATUS_CODE]['child']:array();
				/* Removed the default status from list */
				unset($strOpenLeadArray[$intDefaultCode]);
				/* Get open lead details */
				$strOpenLeadArr	= $this->_getOpenAndCloseLeadDetails($strCompanyArrValue['id'], $strOpenLeadArray);
				/* removed used variables */
				unset($strOpenLeadArray);
				
				
				/* Positive Close lead status array */
				$strPositiveCloseLeadArray	= isset($strCompanyStatusArr[POSITIVE_CLOSURE_STATUS_CODE]['child'])?$strCompanyStatusArr[POSITIVE_CLOSURE_STATUS_CODE]['child']:array();
				/* Removed the default status from list */
				unset($strPositiveCloseLeadArray[$intDefaultCode]);
				/* Get positive close lead details */
				$strPositiveCloseLeadArr	= $this->_getOpenAndCloseLeadDetails($strCompanyArrValue['id'], $strPositiveCloseLeadArray);
				/* removed used variables */
				unset($strPositiveCloseLeadArray);
				
				
				/* Negative Close lead status array */
				$strNegativeCloseLeadArray	= isset($strCompanyStatusArr[NEGATIVE_CLOSURE_STATUS_CODE]['child'])?$strCompanyStatusArr[NEGATIVE_CLOSURE_STATUS_CODE]['child']:array();
				/* Removed the default status from list */
				unset($strNegativeCloseLeadArray[$intDefaultCode]);
				/* Get positive close lead details */
				$strNegativeCloseLeadArr	= $this->_getOpenAndCloseLeadDetails($strCompanyArrValue['id'], $strNegativeCloseLeadArray);
				/* removed used variables */
				unset($strNegativeCloseLeadArray);
				
				/* variable initialization */
				$strCompanyFinalArray	= array_merge($strNewLeadArr, $strOpenLeadArr, $strPositiveCloseLeadArr, $strNegativeCloseLeadArr);
				
				/* if Debugging is set the do needful */
				if($this->_isDebug){
					debugVar('----------------Final Leads Processing Array Details ----------------');
					debugVar($strCompanyFinalArray);
				}
				
				/* removed used variables */
				unset($strNewLeadArr, $strOpenLeadArr, $strPositiveCloseLeadArr, $strNegativeCloseLeadArr);
				
				/* if resulting array is not empty then do needful */
				if(!empty($strCompanyFinalArray)){
					/* Iterating the array */
					foreach($strCompanyFinalArray as $strCompanyFinalArrayKey => $strCompanyFinalArrayValue){
						/* Creating the insert array */
						$strInsertArr = array_merge($strCompanyFinalArrayValue , array('company_code'=>$strCompanyArrValue['id'],'record_date'=>$this->_intYesterDate, 'updated_by'=>1, 'tat'=>0, 'first_call_status_code'=>0,'second_call_status_code'=>0));
						
						/* Inserting into table */
						$this->_objDataOperation->setDataInTable(array('table'=>'trans_rpt_leads','data'=>$strInsertArr));
					}
				}
			}
			
			if(empty($strMessageArr)){
				/* Setting success message */
				$strMessageArr	= jsonReturn(array('status'=>1,'message'=>'Process run successfully'));
			}
			
			/* Deactivate requested date data */
			$this->_objDataOperation->setUpdateData(array('table'=>'trans_cron','data'=>array('corn_value'=>$strMessageArr),'where'=>array('id'=>1)));			
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Get all active company list.
	/*Inputs	: none.
	/*Returns	: Company List.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getCompanyList(){
		/* Variable initialization */
		$strFilterArr	= array();
			
		/* Filter array */
		$strFilterArr	= array(
									'table'=>'master_company',
									'column'=>array('id'),
									'where'=>array('is_active'=>1)
							);
							
		/* Get company list */
		$strCompanyArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Company Details ----------------');
			debugVar($strCompanyArr);
		}
		
		/* Removed used variables */
		unset($strFilterArr);
		
		/* Return company list */
		return $strCompanyArr;
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
		$strReturnArr	= array();
		
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
			/* Checking for parent Category */
			if((int)$strStatusArrValue['parent_id'] == -1){
				/* Setting parent Category */
				$strReturnArr[$strStatusArrValue['company_code']][$strStatusArrValue['id']]['name']	= $strStatusArrValue['description'];
			/* Checking for child category */
			}else if (isset($strReturnArr[$strStatusArrValue['company_code']][$strStatusArrValue['parent_id']])){
				/* Setting child categories */
				$strReturnArr[$strStatusArrValue['company_code']][$strStatusArrValue['parent_id']]['child'][$strStatusArrValue['id']]	= $strStatusArrValue['description'];
			}
			
			/* Checking for default number */
			if((int)$strStatusArrValue['is_default'] == 1){
				$strReturnArr[$strStatusArrValue['company_code']]['default']	= $strStatusArrValue['id'];
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
	/*Inputs	: $pIntCompnayCode :: Company Code,
				: $pIntOpenStatusArr :: Open status array.
	/*Returns	: open or close lead list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getOpenAndCloseLeadDetails($pIntCompnayCode = 0, $pIntOpenStatusArr = array()){
		/* Variable initialization */
		$strReturnArr	= array();
		
		/* checking is needed parameter is passed */
		if(($pIntCompnayCode == 0) || (empty($pIntOpenStatusArr))){
			/* Return empty array */
			return $strReturnArr;
		}
		
		/* Creating open array keys */
		$strOpenArry	= array_keys($pIntOpenStatusArr);
		
		/* Filter array */
		$strFilterArr	= array(
									'table'=>array('master_leads','trans_leads_'.$pIntCompnayCode),
									'join'=>array('','master_leads.id = trans_leads_'.$pIntCompnayCode.'.lead_code'),
									'where'=>array('company_code'=>$pIntCompnayCode,'left(master_leads.record_date,8) <='=>$this->_intYesterDate,'status_code'=>$strOpenArry),
									'column'=>array('lead_code','status_code','region_code','branch_code','lead_owner_code','lead_source_code','master_leads.record_date as lead_record_date'),
									//'group'=>array('status_code','region_code','branch_code','lead_owner_code','lead_source_code')
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




				
				
				
				
				/* Filter array *
				$strFilterArr	= array(
											'table'=>array('master_leads','trans_leads_'.$strCompanyArrValue['id']),
											'join'=>array('','master_leads.id = trans_leads_'.$strCompanyArrValue['id'].'.lead_code'),
											'where'=>array('company_code'=>$strCompanyArrValue['id'],'master_leads.record_date <='=>$this->_intYesterDate.'235959'),
											'column'=>array('count(master_leads.id) as lead_count','status_code','region_code','branch_code','lead_owner_code','lead_source_code'),
											'group'=>array('status_code','region_code','branch_code','lead_owner_code','lead_source_code')
									);
				/* Get count *
				$strCountArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
				
				if(empty($strCountArr)){
					/* Setting error message *
					$strMessageArr	= jsonReturn(array('status'=>0,'message'=>'No lad details found for '.$strCompanyArrValue['id']));
				}else{
					/* Iterating the number *
					foreach($strCountArr as $strCountArrKey => $strCountArrValue){
						/* Creating the insert array *
						$strInsertArr = array_merge($strCountArrValue , array('company_code'=>$strCompanyArrValue['id'],'record_date'=>date('Ymd'), 'updated_by'=>1));
						/* Inserting into table *
						//$this->_objDataOperation->setDataInTable(array('table'=>'trans_rpt_lead_count','data'=>$strInsertArr));
					}
				}*/