<?php 
/*******************************************************************************/
/* Purpose 		: Managing the task related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Task{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "trans_task";
	private $_strBranchCodeArr	= array();
	
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code,
				: $pStrBranchCodeArr :: Branch Code Array
	/* Returns	: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0, $pStrBranchCodeArr = array()){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
		/* Setting Branch Code */
		$this->_strBranchCodeArr= decodeKeyValueArr($pStrBranchCodeArr, true);
	}
	
	/***************************************************************************/
	/* Purpose	: Get task type filter by company code
	/* Inputs 	: None.
	/* Returns	: Task Type List.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getTaskTypeByCompanyCode(){
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'master_task',
									'where'=>array('company_code'=>$this->_intCompanyCode),
									'column'=>array('id', 'description','is_default')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: Get default task type filter by company code
	/* Inputs 	: None.
	/* Returns	: Default Task Type details.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getDefaultTaskTypeByCompanyCode(){
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'master_task',
									'where'=>array('company_code'=>$this->_intCompanyCode,'is_default'=>1),
									'column'=>array('id', 'description')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: Adding task.
	/* Inputs 	: $pTaskArr :: Task details array.
	/* Returns	: Task Code.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function setTask($pTaskArr = array()){
		/* variable initialization */
		$intLeadCode		= isset($pTaskArr['leadCode'])?$pTaskArr['leadCode']:0;
		$intLeadOwnerCode	= isset($pTaskArr['leadOwnerCode'])?$pTaskArr['leadOwnerCode']:0;
		$inNetxFollowUpDate	= isset($pTaskArr['next_follow_date'])?$pTaskArr['next_follow_date']:date('YmdHis',mktime((date('H') + FIRST_TOUCH_TO_LEAD_TIME_IN_HOURS),date('i'),date('s'),date('m'),date('d'),date('Y')));
		$intTaskTypeCode	= isset($pTaskArr['taskTypeCode'])?$pTaskArr['taskTypeCode']:0;
		$intUpdateBy		= isset($pTaskArr['updatedBy'])?$pTaskArr['updatedBy']:0;
		$strComments		= isset($pTaskArr['comments'])?$pTaskArr['comments']:'-';
		$intUpdatedBySystem	= isset($pTaskArr['isSystem'])?$pTaskArr['isSystem']:0;
		$intStatusCode		= isset($pTaskArr['statusCode'])?$pTaskArr['statusCode']:0;
		$intStatusType		= isset($pTaskArr['statusType'])?$pTaskArr['statusType']:1;
		
		$intTransStatus		= 0;
		/* Requested details is not found then do needful */
		if(($intLeadCode == 0) || ($intLeadOwnerCode == 0)){
			/* Return task transaction status */
			return $intTransStatus;
		}
		
		/* Setting the data array */
		$strDataArr	= array(
								'updated_by'=>$intUpdateBy,
								'updated_date'=>date('YmdHis')
						);
		
		/* Setting complete all previous task of same lead */
		$intTransStatus	= $this->_databaseObject->setUpdateData(
																	array(
																		'table'=>$this->_strTableName,
																		'data'=>$strDataArr,
																		'where'=>array(
																						'lead_code'=>$intLeadCode,
																						'updated_date'=>0
																					)
																	)
																);
		
		/* if open status code found then dp needful */
		if($intStatusType == 1){
			/* If task type is not set then fetch the default code */
			if($intTaskTypeCode == 0){
				/* Get default task */
				$strDefautTaskArr	= $this->getDefaultTaskTypeByCompanyCode();
				/*if Default task found then do needful */
				if(!empty($strDefautTaskArr)){
					/* Setting Default task */
					$intTaskTypeCode	= $strDefautTaskArr[0]['id'];
				}
				/* Removed used variables */
				unset($strDefautTaskArr);
			}
			
			/* Setting new task data array */
			$strDataArr	= array(
									'lead_code'=>$intLeadCode,
									'lead_owner_code'=>$intLeadOwnerCode,
									'next_follow_up_date'=>$inNetxFollowUpDate,
									'task_type_code'=>$intTaskTypeCode,
									'comments'=>$strComments
							);
			
			/* Setting new task of same lead */
			$intTransStatus	= $this->_databaseObject->setDataInTable(
																		array(
																			'table'=>$this->_strTableName,
																			'data'=>$strDataArr
																		)
																	);
		}
		
		if((int)$intStatusCode > 0){
			/* if open status code found then dp needful */
			if($intStatusType == 0){
				/* Setting new status same lead */
				$intTransStatus	= $this->_databaseObject->getDirectQueryResult("update master_leads set status_code = ".$intStatusCode.", comments ='".$strComments."', updated_by = ".$intUpdateBy.", updated_date =".date('YmdHis')." where id = ".$intLeadCode);
			}else{
				/* Setting new status and follow-up date of same lead */
				$intTransStatus	= $this->_databaseObject->getDirectQueryResult("update master_leads set last_followup_date = next_followup_date, next_followup_date = '".$inNetxFollowUpDate."', status_code = ".$intStatusCode.", comments ='".$strComments."', updated_by = ".$intUpdateBy.", updated_date =".date('YmdHis')." where id = ".$intLeadCode);
			}
		}							
		
		/* Setting communication history */
		$communicationHistoryObj	= new communicationhistory($this->_databaseObject, $this->_intCompanyCode);
		/* Setting communication history */
		$communicationHistoryObj->setCommuncationHistory(
															array(
																	'lead_code'=>$intLeadCode,
																	'lead_owner_code'=>$intLeadOwnerCode,
																	'follow_up_date'=>$inNetxFollowUpDate,
																	'status_code'=>$intStatusCode,
																	'comments'=>$strComments,
																	'comm_text'=>'',
																	'is_system'=>$intUpdatedBySystem,
																	'updated_by'=>$intUpdateBy
															)
													);
		/* Removed used variables */
		unset($communicationHistoryObj);
		
		/* Return task transaction status */
		return $intTransStatus;
	}
	
	/***************************************************************************/
	/* Purpose	: Getting task list by requested filter.
	/* Inputs 	: $pStrFilterArr :: Filter array.
	/* Returns	: Task List.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getTaskList($pBlnCountNeeded = false, $pStrFilterArr = array()){
		/* Variable initialization */
		$strReturnArr	= array();
		$strColumn		= $strWhere		= '';
		
		/* Setting column */
		$strColumn					= 'master_leads.*, trans_leads_'.$this->_intCompanyCode.'.* ,master_lead_source.description as souce_name, master_status.description as status_name, master_leads.record_date as lead_created_date, 0 as taskNotifiation';
		
		/* if lead filter is not empty then do needful */
		if(!empty($pStrFilterArr)){
			/* Removed page limit */
			unset($pStrFilterArr['offset'] , $pStrFilterArr['limit']);
			
			/* Iterating the loop */
			foreach($pStrFilterArr as $pStrFilterArrKey => $pStrFilterArrValue){
				if(strstr($pStrFilterArrKey,'like')!=''){
					/*Checking for like clause */
					$strWhere	.= " AND ".str_replace('like','',$pStrFilterArrKey)." like  '%".$pStrFilterArrValue."%'";
				}else if(is_array($pStrFilterArrValue)){
					/* Setting in array filter */
					$strWhere	.= ' AND '.$pStrFilterArrKey.' in( '.implode(',',$pStrFilterArrValue).')';
				}else{
					/* Setting normal filter */
					$strWhere	.= ' AND '.$pStrFilterArrKey.' = '.$pStrFilterArrValue;
				}
			}
		}
		
		/* if needed count */
		if($pBlnCountNeeded){
			$strColumn	= 'COUNT(master_leads.id) as recordCount ';
		}
		
		/* Creating Query */
		$strQuery		= 	'
									SELECT 
										'.$strColumn.'
									FROM master_leads
										INNER JOIN trans_leads_'.$this->_intCompanyCode.' ON master_leads.id = trans_leads_'.$this->_intCompanyCode.'.lead_code
										INNER JOIN master_lead_source ON  master_lead_source.id = master_leads.lead_source_code
										INNER JOIN master_status ON master_status.id = master_leads.status_code
									WHERE 
										master_leads.deleted = 0
										AND trans_leads_'.$this->_intCompanyCode.'.deleted = 0
										AND master_lead_source.deleted = 0 
										AND master_status.deleted = 0
										AND master_leads.company_code = '.$this->_intCompanyCode.'
										AND trans_leads_'.$this->_intCompanyCode.'.branch_code in ('.implode(',',$this->_strBranchCodeArr).')
										AND left(master_leads.next_followup_date,8) = '.date('Ymd').' '.$strWhere.' 
								UNION ALL
									SELECT 
										'.$strColumn.'
									FROM master_leads
										INNER JOIN trans_leads_'.$this->_intCompanyCode.' ON master_leads.id = trans_leads_'.$this->_intCompanyCode.'.lead_code
										INNER JOIN master_lead_source ON  master_lead_source.id = master_leads.lead_source_code
										INNER JOIN master_status ON master_status.id = master_leads.status_code
									WHERE 
										master_leads.deleted = 0
										AND trans_leads_'.$this->_intCompanyCode.'.deleted = 0
										AND master_lead_source.deleted = 0 
										AND master_status.deleted = 0
										AND master_leads.company_code = '.$this->_intCompanyCode.'
										AND trans_leads_'.$this->_intCompanyCode.'.branch_code in ('.implode(',',$this->_strBranchCodeArr).')
										AND left(master_leads.next_followup_date,8) < '.date('Ymd').' '.$strWhere.' 
								UNION ALL
									SELECT 
										'.$strColumn.'
									FROM master_leads
										INNER JOIN trans_leads_'.$this->_intCompanyCode.' ON master_leads.id = trans_leads_'.$this->_intCompanyCode.'.lead_code
										INNER JOIN master_lead_source ON  master_lead_source.id = master_leads.lead_source_code
										INNER JOIN master_status ON master_status.id = master_leads.status_code
									WHERE 
										master_leads.deleted = 0
										AND trans_leads_'.$this->_intCompanyCode.'.deleted = 0
										AND master_lead_source.deleted = 0 
										AND master_status.deleted = 0
										AND master_leads.company_code = '.$this->_intCompanyCode.'
										AND trans_leads_'.$this->_intCompanyCode.'.branch_code in ('.implode(',',$this->_strBranchCodeArr).')
										AND left(master_leads.next_followup_date,8) > '.date('Ymd').' '.$strWhere.'
							';
		
		/* if limit is set then do needful */
		if(isset($pStrFilterArr['limit'])){
			/* Setting limit */
			$strQuery	.= 	' LIMIT '.$pStrFilterArr['offset'].', '.$pStrFilterArr['limit'];
		};
		
		/* Getting the query result */
		$strReturnArr	= $this->_databaseObject->getDirectQueryResult($strQuery);
		
		/* Return the task */
		return $strReturnArr;
	}
}
?>