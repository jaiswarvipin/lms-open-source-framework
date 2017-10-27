<?php
/***********************************************************************/
/* Purpose 		: Manage the Dashboard and custom widget request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard	 extends Requestprocess {
	/* variable deceleration */
	private $_strModuleName			= "Dashboard";
	private $_strModuleForm			= "";
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
		$strDataArr	= array();
		
		/* Getting module list */
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.__CLASS__.'/getLeadDetailsWithRequest';
		$strDataArr['strDataAddEditPanel']	= 'leadModules';
		$strDataArr['strNewLead']			= $this->_getNewCount();
		$strDataArr['strPendingTask']		= $this->_getPendingTaskCount();
		$strDataArr['strRegionLeads']		= $this->_getRegionBranchWiseLeadCount('region_code');
		$strDataArr['strBranchLeads']		= $this->_getRegionBranchWiseLeadCount('branch_code');
		$strDataArr['strRegionPerformance']	= $this->_getRegionBranchPerformaceWiseCount('region_code');
		$strDataArr['strBranchPerformance']	= $this->_getRegionBranchPerformaceWiseCount('branch_code');

		/* Load the View */
		$strDataArr['body']	= $this->load->view(DASHBOARD_TEMPLATE, $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $strDataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get new leads count.
	/*Inputs	: None.
	/*Returns 	: New lead count HTML.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getNewCount(){
		/* Variable initialization */
		$strWhere	= array('company_code'=>$this->getCompanyCode(), 'trans_leads_'.$this->getCompanyCode().'.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true), 'status_code'=>getDecyptionValue($this->getDefaultStatusCode()));
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>array('master_leads','trans_leads_'.$this->getCompanyCode()),
									'join'=>array('','master_leads.id = trans_leads_'.$this->getCompanyCode().'.lead_code'),
									'where'=>$strWhere,
									'column'=>array('count(master_leads.id) as newLeadCount')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArr = $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/new_lead_count', array('strResultArr'=>$strResultArr), true);
		
		/* removed used variables */
		unset($strResultArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get pending task count.
	/*Inputs	: None.
	/*Returns 	: Pending task count HTML.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getPendingTaskCount(){
		/* Variable initialization */
		$strWhere	= array('company_code'=>$this->getCompanyCode(), 'next_followup_date < '=>date('YmdHis'), 'trans_leads_'.$this->getCompanyCode().'.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true));
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>array('master_leads','trans_leads_'.$this->getCompanyCode()),
									'join'=>array('','master_leads.id = trans_leads_'.$this->getCompanyCode().'.lead_code'),
									'where'=>$strWhere,
									'column'=>array('count(master_leads.id) as newLeadCount')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArr = $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/pending_task_count', array('strResultArr'=>$strResultArr), true);
		
		/* removed used variables */
		unset($strResultArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get Region or Branch wise lead count.
	/*Inputs	: $pStrRegionORBranch ::Contains region or branch key field name .
	/*Returns 	: Region / Branch Wise Lead count Group by Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getRegionBranchWiseLeadCount($pStrRegionORBranch = 'region_code'){
		
		/* Variable initialization */
		$strResultArr	= $strWhereArr	= $strStatusArr = array();
		$strLabel		= 'Region';
		/* Getting status array from looger */
		$strStatusLogger	= $this->getLeadStatus();
		
		/* if status details found then do needful */
		if(!empty($strStatusLogger)){
			/* Iterating the loop */
			foreach($strStatusLogger as $strStatusLoggerKey => $strStatusLoggerValue){
				if((int)$strStatusLoggerValue->parent_id == -1){
					$strStatusArr[$strStatusLoggerValue->id]['name']		= $strStatusLoggerValue->description;
					$strStatusArr[$strStatusLoggerValue->id]['child']		= '';
				}else if(isset($strStatusArr[$strStatusLoggerValue->parent_id])){
					$strStatusArr[$strStatusLoggerValue->parent_id]['child'][getEncyptionValue($strStatusLoggerValue->id)]	= $strStatusLoggerValue->description;
				}
			}
		}
		
		/* removed used variables */
		unset($strStatusLogger);
		
		/* Variable initialization */
		$intYesterdayDate	= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		if($pStrRegionORBranch == 'region_code'){
			$strWhereArr		= array('company_code'=>$this->getCompanyCode(), 'record_date = '=>$intYesterdayDate,'region_code'=>array_keys(decodeKeyValueArr($this->getRegionDetails())));
		}else{
			$strWhereArr		= array('company_code'=>$this->getCompanyCode(), 'record_date = '=>$intYesterdayDate,'branch_code'=>array_keys(decodeKeyValueArr($this->getBranchDetails())));
			$strLabel			= 'Branch';
		}
		$strFormatedResult	= array();
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'trans_rpt_leads',
									'where'=>$strWhereArr,
									'column'=>array('count(id) as leadCount','status_code',$pStrRegionORBranch),
									'group'=>array('region_code','status_code')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArr['data'] 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* If data found then */
		if(!empty($strResultArr['data'])){
			/* Iterating the loop */
			foreach($strResultArr['data'] as $strResultKey => $strResultValue){
				/* Setting value */
				$strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][getEncyptionValue($strResultValue['status_code'])]	= $strResultValue['leadCount'];
			}
		}
		
		$strResultArr['data'] 	= $strFormatedResult;
		$strResultArr['status']	= $strStatusArr;
		if($pStrRegionORBranch == 'region_code'){
			$strResultArr['region']	= $this->getRegionDetails();
		}else{
			$strResultArr['branch']	= $this->getBranchDetails();
		}
		
		
		/* removed used variables */
		unset($strFormatedResult);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/region_branch_wise_count', array('strResultArr'=>$strResultArr,'strLabel'=>$strLabel), true);
		
		/* removed used variables */
		unset($strResultArr);
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get Region or Branch performance wise lead count.
	/*Inputs	: $pStrRegionORBranch ::Contains region or branch key field name .
	/*Returns 	: Region / Branch Performance Wise Lead count Group by Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getRegionBranchPerformaceWiseCount($pStrRegionORBranch = 'region_code'){
		/* Variable initialization */
		$strResultArr	= $strWhereArr	= $strStatusArr = $strAllStatusArr =  array();
		$strPerformanceStatusArr	= array(OPEN_CLOSURE_STATUS_CODE,POSITIVE_CLOSURE_STATUS_CODE);
		$strLabel					= 'Region';
		
		/* Get all open and positive closed lead status list */
		$strStatusArr	= $this->getLeadStatusBasedOnRequest(array(OPEN_CLOSURE_STATUS_CODE,POSITIVE_CLOSURE_STATUS_CODE));
		
		/* Checking responsed status list is not empty and having requested status index */
		if(!empty($strStatusArr) && (isset($strStatusArr[OPEN_CLOSURE_STATUS_CODE]))){
			/* Setting the operation status code */
			$strAllStatusArr	= array_keys($strStatusArr[OPEN_CLOSURE_STATUS_CODE]);
		}
		/* Checking responsed status list is not empty and having requested status index */
		if(!empty($strStatusArr) && (isset($strStatusArr[POSITIVE_CLOSURE_STATUS_CODE]))){
			/* Setting the operation status code */
			$strAllStatusArr	= array_merge($strAllStatusArr, array_keys($strStatusArr[POSITIVE_CLOSURE_STATUS_CODE]));
		}
		
		/* Variable initialization */
		$intYesterdayDate	= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		if($pStrRegionORBranch == 'region_code'){
			$strWhereArr		= array('company_code'=>$this->getCompanyCode(), 'record_date'=>$intYesterdayDate,'lead_record_date >='=> $intWeekDate.'000000' ,'lead_record_date <=' => $intYesterdayDate.'240000','region_code'=>array_keys(decodeKeyValueArr($this->getRegionDetails())), 'status_code'=>$strAllStatusArr);
		}else{
			$strWhereArr		= array('company_code'=>$this->getCompanyCode(), 'record_date'=>$intYesterdayDate,'lead_record_date >='=> $intWeekDate.'000000' ,'lead_record_date <='=> $intYesterdayDate.'240000','branch_code'=>array_keys(decodeKeyValueArr($this->getBranchDetails())), 'status_code'=>$strAllStatusArr);
			$strLabel			= 'Branch';
		}
		/* removed used variables */
		unset($strAllStatusArr);
		$strFormatedResult	= array();
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'trans_rpt_leads',
									'where'=>$strWhereArr,
									'column'=>array('count(id) as leadCount','status_code',$pStrRegionORBranch),
									'group'=>array('region_code','status_code')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArr['data'] 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* If data found then */
		if(!empty($strResultArr['data'])){
			/* Iterating the loop */
			foreach($strResultArr['data'] as $strResultKey => $strResultValue){
				if(isset($strStatusArr[OPEN_CLOSURE_STATUS_CODE][$strResultValue['status_code']])){
					if(isset($strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][OPEN_CLOSURE_STATUS_CODE])){
						$strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][OPEN_CLOSURE_STATUS_CODE]	+= (int)$strResultValue['leadCount'];
					}else{
						$strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][OPEN_CLOSURE_STATUS_CODE]	= (int)$strResultValue['leadCount'];
					}
				}else{
					if(isset($strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][POSITIVE_CLOSURE_STATUS_CODE])){
						$strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][POSITIVE_CLOSURE_STATUS_CODE]	+= (int)$strResultValue['leadCount'];
					}else{
						$strFormatedResult[getEncyptionValue($strResultValue[$pStrRegionORBranch])][POSITIVE_CLOSURE_STATUS_CODE]	= (int)$strResultValue['leadCount'];
					}
				}
			}
		}
		
		/* variable initialization */
		$strIndexName			= str_replace('_code','',$pStrRegionORBranch);
		$strIndexArr			= $strFinalReturnArr	= $strResultSetArr		= array();
		
		/* Based on request setting the index */
		if($pStrRegionORBranch == 'region_code'){
			$strResultArr['region']	= $this->getRegionDetails();
		}else{
			$strResultArr['branch']	= $this->getBranchDetails();
		}
		
		
		/* Checking location name is empty */
		if(isset($strResultArr[$strIndexName]) && (!empty($strIndexName))){
			/* Iterating the loop */
			foreach($strResultArr[$strIndexName] as $strResultArrKey => $strResultArrValue){
				/* variable initialization */
				$intPerformance = $intOpenLeadCount = $intClosedLeadCount	= 0;
				
				/* Checking for open leads */
				if(isset($strFormatedResult[$strResultArrKey][OPEN_CLOSURE_STATUS_CODE])){
					/* Incrementing the open lead count */
					$intOpenLeadCount += $strFormatedResult[$strResultArrKey][OPEN_CLOSURE_STATUS_CODE];
				}
				/* Checking for closed leads */
				if(isset($strFormatedResult[$strResultArrKey][POSITIVE_CLOSURE_STATUS_CODE])){
					/* Incrementing the closed lead count */
					$intClosedLeadCount += $strFormatedResult[$strResultArrKey][POSITIVE_CLOSURE_STATUS_CODE];
				}
				
				/* if lead found then do needful */
				if($intOpenLeadCount > 0){
					/* Setting the performance percentage */
					$intPerformance =  numberFormating(($intClosedLeadCount / $intOpenLeadCount));
				}else if( $intOpenLeadCount >= 0){
					$intPerformance	= numberFormating($intOpenLeadCount*100);
				}else{
					$intPerformance	= '0.00';
				}
				
				/* If performance ration is > 0 then do needful */ 
				if($intPerformance > 0){
					/* setting the final array */
					$strResultSetArr[$strResultArrKey]	= array('name'=>$strResultArrValue, 'open'=>$intOpenLeadCount, 'close'=>$intClosedLeadCount,'value'=>$intPerformance);
				}
			}
		}
		/* Sorting by value */
		usort($strResultSetArr, function($pFirstRefrenceArr, $pSecondRefrenceArr) {
			return $pFirstRefrenceArr['value'] - $pSecondRefrenceArr['value'];
		});
		
		/* Setting value */
		$strResultArr['data']	= $strResultSetArr;
		
		/* removed used variables */
		unset($strFormatedResult, $strStatusArr, $strIndexName, $strResultSetArr, $strIndexArr, $strFinalReturnArr);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/region_branch_performance_count', array('strResultArr'=>$strResultArr,'strLabel'=>$strLabel), true);
		
		/* removed used variables */
		unset($strResultArr);
	}
}