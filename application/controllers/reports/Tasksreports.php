<?php
/***********************************************************************/
/* Purpose 		: Manage the Task report request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasksreports extends Requestprocess {
	/* variable deceleration */
	private $_strModuleName			= "Task Reports";
	private $_strModuleForm			= "frmTaskReportSearch";
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
		$intCurrentPageNumber				= 0;
		
		/* Getting current page number */
		$intCurrentPageNumber	= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting module list */
		$this->_strColumnArr				= $this->_getColumnArr();
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.'reports/'.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.__CLASS__.'/getLeadDetailsWithRequest';
		$strDataArr['strDataAddEditPanel']	= 'TaskReportModules';
		$strDataArr['strColumnsArr'] 		= $this->_strColumnArr;
		$strDataArr['strDataArr'] 			= $this->_getLeadReportData(false,$intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getLeadReportData(true),($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['strColumnSearchPanel'] = $this->getColumnAsSearchPanel(array_merge($this->_strColumnArr,array('frmName'=>$this->_strModuleForm)),SITE_URL.'reports/'.__CLASS__);
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strDataArr['strTaskOpenCloseGraph']= $this->_getTaskOpenandClosedTaskCountAndDateWiseReportData();
		$strDataArr['strTaskTypeGraph']		= $this->_getTaskTypeAndDateWiseReportData();
		/* Load the View */
		$strDataArr['body']	= $this->load->view(REPORTS_TEMPLATE.'task', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $strDataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get task report table date.
	/*Inputs	: $pBlnCountNeeded :: pagination request,
				: $pIntRecordSetIndex :: Record page number.
	/*Returns 	: Task repots data in table format.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadReportData($pBlnCountNeeded = false, $pIntRecordSetIndex = 0){
		/* Variable initialization */
		$strLimitFilter			= $strColumArr		= $strWhereClauseArr	= $strReturnArr 		= array();
		
		/* Variable initialization */
		$intYesterdayDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate			= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		/* Setting filter clause */
		$strWhereClauseArr				= array('trans_leads_'.$this->getCompanyCode().'.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true),'trans_task.record_date <='=>$intYesterdayDate.'240000','trans_task.record_date >='=>$intWeekDate.'000000');
		
		/* if task record filter code is passed then do needful */
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
						}else if($strColumnName == 'FromDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date >='] = getDateFormat($strValue,1).'000000';
							$intWeekDate		= getDateFormat($strValue,1);
						}else if($strColumnName == 'ToDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date <=']	= getDateFormat($strValue,1).'240000';
							$intYesterdayDate	= $strValue;
						}else{
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName.' like'=>$strValue));
						}
					}
				}
			}
		}
		
		/* setting date range date */
		$strReturnArr['strFromDate']	= getDateFormat($intWeekDate,2);
		$strReturnArr['strToDate']		= getDateFormat($intYesterdayDate,2);
				
		/* if pagination request comes then do needful */
		if($pBlnCountNeeded){
			/* Set count column */
			$strColumArr	= array('count(trans_task.id) as recordCount');
		}else{
			/* Set data column */ 
			$strColumArr	= array('trans_task.*','trans_task.record_date as task_date','master_user.user_name as lead_owner_code','trans_leads_'.$this->getCompanyCode().'.*, master_task.description as task_type_name');
		}
		
		/* Variable Initialization */
		$strLimitFilter['offset']	= 0;
		$strLimitFilter['limit']	= DEFAULT_RECORDS_ON_PER_PAGE;
		
		/* if requested page number is > 0 then do needful */ 
		if(($pIntRecordSetIndex >= 0) && (!$pBlnCountNeeded)){
			/* Setting the page index */
			$strLimitFilter['offset']	= ($pIntRecordSetIndex * DEFAULT_RECORDS_ON_PER_PAGE);
		}
		
		$strFilterArr	= array(
									'table'=>array('trans_task','trans_leads_'.$this->getCompanyCode(),'master_user','master_task'),
									'join'=>array('','trans_task.lead_code = trans_leads_'.$this->getCompanyCode().'.lead_code','master_user.id = trans_task.lead_owner_code','master_task.id = trans_task.task_type_code'),
									'column'=>$strColumArr,
									'where'=>$strWhereClauseArr,
									'order'=>array('trans_task.record_date'=>'desc')
								);
		
		/* getting number of task count */
		$strLeadArr 		= $this->_objDataOperation->getDataFromTable(array_merge($strFilterArr, $strLimitFilter));
		
		/* if lead details found then do needful */
		if(!empty($strLeadArr)){
			/* Lead count */
			if($pBlnCountNeeded){
				$strReturnArr	= $strLeadArr;
			}else{
				/* Iterating the loop */
				foreach($strLeadArr as $strLeadArrKey => $strLeadArrValue){	
					/* Iterating the lead details array */
					foreach($this->_strColumnArr as $strColumnArrKey => $strColumnArrValue){
						if((string)$strColumnArrKey == 'date_range'){
							continue;
						}
						
						/* Setting Value */
						$strReturnArr['data'][$strLeadArrKey][$strColumnArrValue['column']]	= $this->getLeadAttributeDetilsByAttributeKey($strColumnArrValue['column'], $strLeadArrValue[$strColumnArrValue['column']]);
					}
				}
			}
		}
		
		/* Return the report data */
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
		$strModuleArr 	= $this->getLeadAttributeList();
		
		$strReturnArr['date_range']	= array('column'=>'date_range','label'=>'Date Range','is_date'=>'1');
		$strReturnArr[]	= array('column'=>'task_date','label'=>'Creating Date','is_date'=>'1');
		$strReturnArr[]	= array('column'=>'next_follow_up_date','label'=>'Follow-up Date','is_date'=>'1');
		$strReturnArr[]	= array('column'=>'lead_owner_code','label'=>'Lead owner','dropdown'=>'1','data'=>'');
		
		/* If configured fields is not empty then do needful */
		if(!empty($strModuleArr)){
			/* iterating the loop */
			foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
				/* Setting Column */
				$strReturnArr[]	= array('column'=>$strModuleArrKey,'label'=>$strModuleArrValue->label);
			}
		}
		
		/* Removed used variables */
		unset($strModuleArr);
		
		$strReturnArr[]	= array('column'=>'region_code','label'=>'Region','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getRegionDetails(),''));
		$strReturnArr[]	= array('column'=>'branch_code','label'=>'Branch','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getBranchDetails(),''));
		$strReturnArr[]	= array('column'=>'task_type_name','label'=>'Task Type','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),''));
		$strReturnArr[]	= array('column'=>'comments','label'=>'Comments','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),''));
		
		/* Return column array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get Task count by task open and closed v/s date range.
	/*Inputs	: None.
	/*Returns 	: Task count by X: Open and closed task  count v/s 
								Y: date range.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getTaskOpenandClosedTaskCountAndDateWiseReportData(){
		/* Variable initialization */
		$strWhereClauseArr	= $strReturnArr = $strStatusArr = array();
		
		/* Variable initialization */
		$intYesterdayDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate			= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		/* Parent Child Status Array */
		$strStatusArr			= array_keys(decodeKeyValueArr($this->getLeadStatus()));
		
		/* Setting filter clause */
		$strWhereClauseArr		= array('trans_leads_'.$this->getCompanyCode().'.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true),'trans_task.record_date <='=>$intYesterdayDate.'240000','trans_task.record_date >='=>$intWeekDate.'000000');
		
		/* if lead record filter code is passed then do needful */
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
						}else if($strColumnName == 'FromDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date >='] = getDateFormat($strValue,1).'000000';
							$intWeekDate		= getDateFormat($strValue,1);
						}else if($strColumnName == 'ToDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date <=']	= getDateFormat($strValue,1).'240000';
							$intYesterdayDate	= $strValue;
						}else{
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName.' like'=>$strValue));
						}
					}
				}
			}
		}
		
		/* Set data column */ 
		$strColumArr			= array('CASE WHEN trans_task.updated_date > 0 THEN COUNT(trans_task.id) END AS closedTask','CASE WHEN trans_task.updated_date = 0 THEN COUNT(trans_task.id) END AS openTask','left(trans_task.record_date ,8) as lead_record_date');
		
		$strFilterArr	= array(
									'table'=>array('trans_task','trans_leads_'.$this->getCompanyCode()),
									'join'=>array('','trans_task.lead_code = trans_leads_'.$this->getCompanyCode().'.lead_code'),
									'column'=>$strColumArr,
									'where'=>$strWhereClauseArr,
									'group'=>array('left(trans_task.record_date ,8)'),
									'order'=>array('3'=>'asc')
								);
								
		/* getting number of task count */
		$strLeadTaskArr 		= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Variable initialization  */
		$strReturnArr['date']	 = array();
		$strReturnArr['open']	 = array();
		$strReturnArr['closed']	 = array();
		
		/* If data found then do needful */
		if(!empty($strLeadTaskArr)){
			/* Iterating the loop */
			foreach($strLeadTaskArr as $strLeadTaskArrKey => $strLeadTaskArrValue){
				/* Setting date */
				$strReturnArr['date'][]		= getDateFormat($strLeadTaskArrValue['lead_record_date'],4);
				$strReturnArr['open'][]		= (int)$strLeadTaskArrValue['openTask'];
				$strReturnArr['closed'][]	= (int)$strLeadTaskArrValue['closedTask'];
			}
		}
		/* removed used variables */
		unset($strLeadTaskArr, $strFilterArr);
		
		/* Setting the value as per chart request */		
		$strReturnArr['date']			= array_values($strReturnArr['date']);
		$strReturnArr['data'][]			= array('name'=>'Open Task','data'=>array_values($strReturnArr['open']));
		$strReturnArr['data'][]			= array('name'=>'Closed Task','data'=>array_values($strReturnArr['closed']));
		/* removed used variables */
		unset($strReturnArr['open'], $strReturnArr['closed']);
		/* Return the JSON string */
		return jsonReturn($strReturnArr);
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get task count by task type v/s date range.
	/*Inputs	: None.
	/*Returns 	: Task count by X:  task  type count v/s 
								Y: date range.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getTaskTypeAndDateWiseReportData(){
		/* Variable initialization */
		$strWhereClauseArr	= $strReturnArr = $strTaskTypeArr = array();
		
		/* Variable initialization */
		$intYesterdayDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate			= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		/* Parent Child Status Array */
		$strStatusArr			= array_keys(decodeKeyValueArr($this->getLeadStatus()));
		
		/* Setting filter clause */
		$strWhereClauseArr		= array('trans_leads_'.$this->getCompanyCode().'.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true),'trans_task.record_date <='=>$intYesterdayDate.'240000','trans_task.record_date >='=>$intWeekDate.'000000');
		
		/* if lead record filter code is passed then do needful */
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
						}else if($strColumnName == 'FromDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date >='] = getDateFormat($strValue,1).'000000';
							$intWeekDate		= getDateFormat($strValue,1);
						}else if($strColumnName == 'ToDate'){
							/* Setting filter column */
							$strWhereClauseArr['trans_task.record_date <=']	= getDateFormat($strValue,1).'240000';
							$intYesterdayDate	= $strValue;
						}else{
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName.' like'=>$strValue));
						}
					}
				}
			}
		}
		
		/* Task object initialization */
		$objTask		= new Task($this->_objDataOperation, $this->getCompanyCode());
		/* Get Task type */
		$strTaskTypeArr	= $objTask->getTaskTypeByCompanyCode();
		/* removed used variables */
		unset($objTask);
		
		/* Set data column */ 
		$strColumArr			= array('COUNT(trans_task.id) AS taskCount','task_type_code','master_task.description','left(trans_task.record_date ,8) as lead_record_date');
		
		$strFilterArr	= array(
									'table'=>array('trans_task','trans_leads_'.$this->getCompanyCode(),'master_task'),
									'join'=>array('','trans_task.lead_code = trans_leads_'.$this->getCompanyCode().'.lead_code','master_task.id = trans_task.task_type_code'),
									'column'=>$strColumArr,
									'where'=>$strWhereClauseArr,
									'group'=>array('left(trans_task.record_date ,8)','task_type_code'),
									'order'=>array('4'=>'asc')
								);
								
		/* getting number of task count */
		$strLeadTaskArr 		= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Variable initialization  */
		$strReturnArr['date']	 = array();
		
		/* If data found then do needful */
		if(!empty($strLeadTaskArr)){
			/* Iterating the loop */
			foreach($strLeadTaskArr as $strLeadTaskArrKey => $strLeadTaskArrValue){
				/* Setting date */
				$strReturnArr['date'][getDateFormat($strLeadTaskArrValue['lead_record_date'],4)] = getDateFormat($strLeadTaskArrValue['lead_record_date'],4);
				/* Iterating the task type */
				foreach($strTaskTypeArr as $strTaskTypeArrKey => $strTaskTypeArrValue){
					if((!isset($strReturnArr[$strTaskTypeArrValue['description']][$strLeadTaskArrValue['lead_record_date']])) && ($strTaskTypeArrValue['id'] == $strLeadTaskArrValue['task_type_code'])){
						$strReturnArr[$strTaskTypeArrValue['description']][$strLeadTaskArrValue['lead_record_date']]		= (double)$strLeadTaskArrValue['taskCount'];
					}else {
						if(((double)$strLeadTaskArrValue['taskCount'] > 0) && ($strTaskTypeArrValue['id'] == $strLeadTaskArrValue['task_type_code'])){
							$strReturnArr[$strTaskTypeArrValue['description']][$strLeadTaskArrValue['lead_record_date']]	= (double)$strLeadTaskArrValue['taskCount'];
						}else{
							$strReturnArr[$strTaskTypeArrValue['description']][$strLeadTaskArrValue['lead_record_date']]	= 0;
						}
					}
				}
			}
		}
		
		/* removed used variables */
		unset($strLeadTaskArr, $strFilterArr, $strTaskTypeArr);
		/* Setting the value as per chart request */		
		$strDateArr						= array_values($strReturnArr['date']);
		unset($strReturnArr['date']);
		
		/* Iterating task type the loop */
		foreach($strReturnArr as $strReturnArrKey => $strReturnArrValue){
			/* Set array values */
			$strReturnArr['data'][$strReturnArrKey]	 = array('name'=>$strReturnArrKey, 'data'=>array_values($strReturnArrValue));
			/* removed used variables */
			unset($strReturnArr[$strReturnArrKey]);
		}
		
		/* Set returns values */
		$strReturnArr['date']			= $strDateArr;
		$strReturnArr['data']			= (isset($strReturnArr['data'])?array_values($strReturnArr['data']):array());
		
		/* Return the JSON string */
		return jsonReturn($strReturnArr);
	}
}