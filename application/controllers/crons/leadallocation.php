<?php
/***********************************************************************/
/* Purpose 		: Lead allocation automated script (CRON).
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadallocation extends Requestprocess {
	/* variable deceleration */
	private $_intFromDate			= 0;
	private $_intToDate				= 0;
	private $_isDebug				= false;
	private $_intCRONCode			= 3;
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* Setting operational date */
		$this->_intFromDate	= (isset($_REQUEST['fromdate']))?$_REQUEST['fromdate']:date('YmdHis', mktime(date('H'),date('i')-5,date('s'),date('m'),date('d'),date('Y')));
		$this->_intToDate	= (isset($_REQUEST['todate']))?$_REQUEST['todate']:date('YmdHis', mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')));
		
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
			debugVar($this->_intFromDate);
			debugVar($this->_intToDate);
		}
		
		/* Get all active company list */
		$strCompanyArr	= $this->_getCompanyList();
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Active company List ----------------');
			debugVar($strCompanyArr);
		}
		
		/* no active company found then do needful */
		if(empty($strCompanyArr)){
			/* Setting the return array */
			return jsonReturn(array('status'=>0,'message'=>'No active company found.'));
		}
		
		/* get last index till where CRON successfully executed */
		$intLeadIndex	= $this->_getCronDetails();
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Last lead index ----------------');
			debugVar($intLeadIndex);
		}
		/* Setting value */
		$intLeadIndex	= isset($intLeadIndex[0]['cron_value'])?$intLeadIndex[0]['cron_value']:0;
		
		/* Get user configuration array set */
		$strUserConfigArr	= $this->_getUserConfig();
		$intLeadOwnerCode	= 0;
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------User Configuration Default  ----------------');
			debugVar($strUserConfigArr);
		}
		
		/* iterating the company loop */
		foreach($strCompanyArr as $strCompanyArrKey => $strCompanyArrValue){
			/* lead array list */
			$strLeadArr	= $this->_getUnAllocatedLeads($strCompanyArrValue['id'], $intLeadIndex);
			
			/* if Debugging is set the do needful */
			if($this->_isDebug){
				debugVar('----------------Unallocated lead of company :'.$strCompanyArrValue['id'].' ----------------');
				debugVar($strLeadArr);
			}
			
			/* lead list is not empty then do needful */
			if(!empty($strLeadArr)){
				/* Get the default user configuration setting for lead allocation */
				$strUserConfigArr	= isset($strUserConfigArr[$strCompanyArrValue['id']])?$strUserConfigArr[$strCompanyArrValue['id']]:array();
				$intActionCode		= 0;
				
				/* Checking for default user allocation */
				if((isset($strUserConfigArr['lead_owner_code'])) && ($strUserConfigArr['lead_owner_code'] > 0)){
					/* Setting action code */
					$intActionCode	= 1;
					/* Checking for default location (Region and Branch) */
				}else if((isset($strUserConfigArr['region_code'])) && (isset($strUserConfigArr['branch_code'])) && ($strUserConfigArr['branch_code'] > 0) && ($strUserConfigArr['region_code'] > 0)){
					/* Setting action code */
					$intActionCode	= 2;
				}
				
				/* Creating lead object */
				$leadObj	= new Lead($this->_objDataOperation, $strCompanyArrValue['id']);
				
				/* Iterating the loop */
				foreach($strLeadArr as $strLeadArrKey => $strLeadArrValue){
					/* Setting lead index */
					$intLeadIndex		= $strLeadArrValue['id'];
					/* if Debugging is set the do needful */
					if($this->_isDebug){
						debugVar('----------------For this lead Below rule applied ----------------');
						debugVar($intActionCode);
						debugVar($strLeadArrValue);
					}
					
					/* Central authority */
					if($intActionCode == 1){
						/* Setting employee code */
						$strLeadArrValue['lead_owner_code'] = $strUserConfigArr['lead_owner_code'];
						$strLeadArrValue['branch_code'] 	= $strUserConfigArr['branch_code'];
						$strLeadArrValue['region_code'] 	= $strUserConfigArr['region_code'];
						/* Central Region and Branch with Random Employee */
					}else if($intActionCode == 2){
						/* Get lead owner code */
						$intLeadOwnerCode 					= $this ->_getLeadownerByBranchCode($strUserConfigArr['branch_code']);
						$strLeadArrValue['lead_owner_code'] = $intLeadOwnerCode;
						$strLeadArrValue['branch_code'] 	= $strUserConfigArr['branch_code'];
						$strLeadArrValue['region_code'] 	= $strUserConfigArr['region_code'];
						/* if lead owner code is not found then do needful */
						if($intLeadOwnerCode == 0){
							continue;
						}
						/* don't do nay thing */
					}else{
						continue;
					}
					
					/* if Debugging is set the do needful */
					if($this->_isDebug){
						debugVar('----------------After applying the rule lead array is ----------------');
						debugVar($strLeadArrValue);
					}
					
					/* Setting the default update user */
					$strLeadArrValue['updated_by']		= 1;
					/* Setting leads */
					$intProcessStatus = $leadObj->setLeadOwner($strLeadArrValue['id'],$strLeadArrValue['lead_owner_code'],$strLeadArrValue);
				}
				
				/* removed used variables */
				unset($leadObj);
			}
		}
		/* update the CRON details */
		$this->_objDataOperation->setUpdateData(
													array(
															'table'=>'trans_cron',
															'cron_value'=>$intLeadIndex,
															'where'=>array('id'=>$this->_intCRONCode)
														)
												);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get CRON Details.
	/*Inputs	: None.
	/*Returns	: Cron details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getCronDetails(){
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>'trans_cron',
									'where'=>array('id'=>$this->_intCRONCode)
								);
								
		/* get lead allocation CRON details */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		/* return the requested CRON details */
		return $strResultArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Fetch all active company list.
	/*Inputs	: None.
	/*Returns	: Company list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getCompanyList(){
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>'master_company',
									'column'=>array('id')
								);
								
		/* get company list */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		/* return the company list */
		return $strResultArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Fetch lead needs to assigned.
	/*Inputs	: $pIntCompanyCode ::Company code,
				: $pIntLastLeadIndex :: Last execution point.
	/*Returns	: Lead array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUnAllocatedLeads($pIntCompanyCode = 0, $pIntLastLeadIndex = 0){
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>array('master_leads','trans_leads_'.$pIntCompanyCode),
									'join'=>array('','master_leads.id = trans_leads_'.$pIntCompanyCode.'.lead_code'),
									'column'=>array('master_leads.id','status_code','lead_owner_code','region_code','branch_code'),
									'where'=>array('lead_owner_code'=>0,'master_leads.id >'=>$pIntLastLeadIndex,'company_code'=>$pIntCompanyCode),
									'order'=>array('master_leads.id'=>'desc')
								);
								
		/* get not assigned lead array set */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		/* return the lead array */
		return $strResultArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get default region and branch code of requested company.
	/*Inputs	: None.
	/*Returns	: User default configuration code array.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserConfig(){
		/* variable initialization */
		$strReturnArr = array();
		
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>array('master_user_config','master_company'),
									'join'=>array('','master_user_config.company_code = master_company.id'),
									'column'=>array('master_user_config.*')
								);
								
		/* checking for user configuration array */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		/* If configuration found then do needful */
		if(!empty($strResultArr)){
			/* iterating the loop */
			foreach($strResultArr as $strResultArrKey => $strResultArrValue){
				$strFromIndex	= $strResultArrValue['key_description'];
				/* based on index setting the value */
				switch($strResultArrValue['key_description']){
					case 'DEFAULT_REGION':
						$strFromIndex	= 'region_code';
						break;
					case 'DEFAULT_BRANCH':
						$strFromIndex	= 'branch_code';
						break;
					case 'DEFAULT_LEAD_ALLOCATED_TO':
						$strFromIndex	= 'lead_owner_code';
						break;
					default:
						$strFromIndex	= $strResultArrValue['key_description'];
						break;
				}
				/* Setting value */
				$strReturnArr[$strResultArrValue['company_code']][$strFromIndex]	= $strResultArrValue['value_description'];
			}
		}
		
		/* Removed used variables */
		unset($strQueryArr, $strResultArr);
		
		/* return lead attribute array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Fetch lead owner code, having less lead allocated.
	/*Inputs	: $pBranchCode ::Branch code.
	/*Returns	: Lead code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadownerByBranchCode($pBranchCode = 0 ){
		/* Variable initialization */
		$intLeadOwnerCode = 0;
		
		/* Query builder array */
		$strQueryArr	= array(
									'table'=>array('master_leads','trans_user_location','master_user'),
									'join'=>array('','master_leads.id = trans_user_location.user_code','master_user.id = master_leads.lead_owner_code'),
									'column'=>array('COUNT(master_leads.id) AS leadCunt','lead_owner_code'),
									'where'=>array('branch_code'=>$pBranchCode,'system_role_code'=>FOS_ROLE_CODE),
									'group'=>array('lead_owner_code'),
									'order'=>array('1'=>'asc')
								);
								
		/* get not assigned lead array set */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Leadowner found at '.$pBranchCode.'---------------');
			debugVar($strResultArr);
		}
		
		/* lead owner list found with count the do needful */
		if(!empty($strResultArr)){
			/* setting the lead owner code */
			$intLeadOwnerCode	= $strResultArr[0]['lead_owner_code'];
		}
		
		/* return the lead code */
		return $intLeadOwnerCode;
	}
}