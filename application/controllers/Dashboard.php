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
		$strDataArr['strEmpPerformance']	= $this->_getTopPerformingEmployee();
		$strDataArr['strSalesFunnel']		= $this->_getSaleFunnel();

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
	
	/**********************************************************************/
	/*Purpose 	: Get top performaing lead owner lead count from reporting strecture.
	/*Inputs	: None.
	/*Returns 	: Lead owner list from along with performance.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getTopPerformingEmployee(){
		/* Variable initialization */
		$strResultArr	= $strWhereArr	= $strStatusArr = $strAllStatusArr =  array();
		$strPerformanceStatusArr	= array(OPEN_CLOSURE_STATUS_CODE,POSITIVE_CLOSURE_STATUS_CODE);
		
		/* Variable initialization */
		$intYesterdayDate	= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		/* Setting filter clause */
		$strWhereArr		= array('trans_rpt_employee_performance.company_code'=>$this->getCompanyCode(), 'trans_rpt_employee_performance.record_date'=>$intYesterdayDate, 'branch_code'=>array_keys(decodeKeyValueArr($this->getBranchDetails())), 'status_type'=>OPEN_CLOSURE_STATUS_CODE);
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>array('trans_rpt_employee_performance','master_user'),
									'join'=>array('','trans_rpt_employee_performance.lead_owner_code = master_user.id'),
									'where'=>$strWhereArr,
									'column'=>array('sum(lead_count) as lead_count','status_type','lead_owner_code','user_name as name','region_code','branch_code'),
									'group'=>array('lead_owner_code','status_type')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArrSet 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if record found then do needful */
		if(!empty($strResultArrSet)){
			/* Iterating the loop */
			foreach($strResultArrSet as $strResultArrSetKey => $strResultArrSetValue){
				/* Setting record by lead code */
				$strFormatedResult[$strResultArrSetValue['lead_owner_code']]	= array_merge(array('value'=>0,'open'=>$strResultArrSetValue['lead_count']),$strResultArrSetValue);
			}
		}
		
		
		
		/******* Setting filter clause for positive closur */
		$strWhereArr		= array('trans_rpt_employee_performance.company_code'=>$this->getCompanyCode(),'trans_rpt_employee_performance.record_date <='=>$intYesterdayDate,'trans_rpt_employee_performance.record_date >='=>$intWeekDate , 'branch_code'=>array_keys(decodeKeyValueArr($this->getBranchDetails())), 'status_type'=>POSITIVE_CLOSURE_STATUS_CODE);
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>array('trans_rpt_employee_performance','master_user'),
									'join'=>array('','trans_rpt_employee_performance.lead_owner_code = master_user.id'),
									'where'=>$strWhereArr,
									'column'=>array('sum(lead_count) as lead_count','status_type','lead_owner_code','user_name as name','region_code','branch_code'),
									'group'=>array('lead_owner_code','status_type')
							);
		
		/* removed used variables */
		unset($strWhere);
		
		/* getting number of lead count from location */
		$strResultArrSet 		= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if record found then do needful */
		if(!empty($strResultArrSet)){
			/* Iterating the loop */
			foreach($strResultArrSet as $strResultArrSetKey => $strResultArrSetValue){
				/* variable initialziation */
				$intValue	= 0;
				/* if lead owner code is set for open and count is > then do need full */
				if((isset($strFormatedResult[$strResultArrSetValue['lead_owner_code']]))){
					/* if count > 0 then do needful */
					if((int)$strFormatedResult[$strResultArrSetValue['lead_owner_code']]['open'] > 0){
						/* Setting the performance value */
						$intValue	= numberFormating(($strResultArrSetValue['lead_count'] / $strFormatedResult[$strResultArrSetValue['lead_owner_code']]['open'])); 
					}else{
						/* Setting the performance value */
						$intValue	= numberFormating($strResultArrSetValue['lead_count'] * 100); 
					}
					
					/* Setting record by lead code */
					$strFormatedResult[$strResultArrSetValue['lead_owner_code']]			= array_merge(array('closed'=>$strResultArrSetValue['lead_count']), $strFormatedResult[$strResultArrSetValue['lead_owner_code']]);
					$strFormatedResult[$strResultArrSetValue['lead_owner_code']]['value']	= $intValue;
				}else{
					/* Setting the performance value */
					$intValue	= numberFormating($strResultArrSetValue['lead_count'] * 100); 
					/* Setting record by lead code */
					$strFormatedResult[$strResultArrSetValue['lead_owner_code']]	= array_merge(array('closed'=>$strResultArrSetValue['lead_count'], 'value'=>$intValue,'open'=>0),$strResultArrSetValue);
				}
			}
		}
		
		/* Sorting by value */
		usort($strFormatedResult, function($pFirstRefrenceArr, $pSecondRefrenceArr) {
			return $pFirstRefrenceArr['value'] - $pSecondRefrenceArr['value'];
		});
		
		/* Setting value */
		$strResultArr['data']	= $strFormatedResult;
		$strResultArr['region']	= $this->getRegionDetails();
		$strResultArr['branch']	= $this->getBranchDetails();
		
		/* removed used variables */
		unset($strFormatedResult, $strStatusArr, $strIndexName, $strResultSetArr, $strIndexArr, $strFinalReturnArr);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/employee_performace_count', array('strResultArr'=>$strResultArr), true);
		
		/* removed used variables */
		unset($strResultArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get sales funnel.
	/*Inputs	: None.
	/*Returns 	: Sales funnel data.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getSaleFunnel(){
		/* Variable initialization */
		$strReturnArr	=  	$strFilterArr = array();
		$intYesterdayDate	= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		
		/* Getting lead status based on paraent code */
		$strReturnArr['statusArr']		= $this->getLeadStatusBasedOnRequest();
		$strReturnArr['intDate']		= $intYesterdayDate;
		
		/* Creating the query array */
		$strFilterArr	= array(
									'table'=>array('trans_rpt_leads','master_status'),
									'join'=>array('','trans_rpt_leads.status_code = master_status.id'),
									'column'=>array('count(trans_rpt_leads.id) as leadCount','description','status_code','parent_id','left(lead_record_date,8) as lead_date'),
									'where'=>array('trans_rpt_leads.company_code'=>$this->getCompanyCode(),'branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true),'trans_rpt_leads.record_date'=>$intYesterdayDate),
									'group'=>array('status_code')
							);
		
		/* getting number of lead count from location */
		$strResultArrSet 		= $this->_objDataOperation->getDataFromTable($strFilterArr);
		/* Removed used variable */
		unset($strFilterArr);
		
		/* if result having some records then do needful */
		if(!empty($strResultArrSet)){
			/* Iterating the loop */
			foreach($strResultArrSet as $strResultArrSetKey => $strResultArrSetValue){
				/* Setting the array group by parent status */
				$strReturnArr['data'][$strResultArrSetValue['parent_id']][$strResultArrSetValue['status_code']] = $strResultArrSetValue;
			}
			
		}
		
		/* Removed used variable */
		unset($strResultArrSet);
		
		/* Return  new lead HTML */
		return $this->load->view('dashboard/sales_funnel', array('strResultArr'=>$strReturnArr), true);
		
		/* removed used variables */
		unset($strReturnArr);
	}
}