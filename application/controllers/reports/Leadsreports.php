<?php
/***********************************************************************/
/* Purpose 		: Manage the Dashboard and custom widget request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadsreports extends Requestprocess {
	/* variable deceleration */
	private $_strModuleName			= "Lead Reports";
	private $_strModuleForm			= "frmLeadReportSearch";
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
		$strDataArr['strDataAddEditPanel']	= 'leadModules';
		$strDataArr['strColumnsArr'] 		= $this->_strColumnArr;
		$strDataArr['strDataArr'] 			= $this->_getLeadReportData(false,$intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getLeadReportData(true),($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['strColumnSearchPanel'] = $this->getColumnAsSearchPanel(array_merge($this->_strColumnArr,array('frmName'=>$this->_strModuleForm)),SITE_URL.'reports/'.__CLASS__);
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		/* Load the View */
		$strDataArr['body']	= $this->load->view(REPORTS_TEMPLATE.'lead', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $strDataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead report table date.
	/*Inputs	: $pBlnCountNeeded :: pagination request,
				: $pIntRecordSetIndex :: Record page number.
	/*Returns 	: Lead repots data in table format.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLeadReportData($pBlnCountNeeded = false, $pIntRecordSetIndex = 0){
		/* Variable initialization */
		$strLimitFilter			= $strColumArr		= $strWhereClauseArr	= $strReturnArr 		= array();
		
		
		/* Variable initialization */
		$intYesterdayDate		= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
		$intWeekDate			= date('Ymd',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-WEEAK_DAYS,date('Y')));
		
		$strReturnArr['strFromDate']	= getDateFormat($intWeekDate,2);
		$strReturnArr['strToDate']		= getDateFormat($intYesterdayDate,2);
		/* Setting filter caluse */
		$strWhereClauseArr				= array('trans_rpt_leads.branch_code'=>decodeKeyValueArr($this->getBranchCodes(),true),'trans_rpt_leads.record_date'=>$intYesterdayDate);
		
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
						}else{
							/* Setting filter column */
							$strWhereClauseArr	= array_merge($strWhereClauseArr, array($strColumnName.' like'=>$strValue));
						}
					}
				}
			}
		}
																	
		/* if pagination request comes then do needful */
		if($pBlnCountNeeded){
			/* Set count column */
			$strColumArr	= array('count(trans_rpt_leads.id) as recordCount');
		}else{
			/* Set data column */ 
			$strColumArr	= array('trans_rpt_leads.*','trans_rpt_leads.lead_record_date as assigment_date','master_user.user_name as lead_owner_code','master_status.description as status_name','trans_leads_'.$this->getCompanyCode().'.*');
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
									'table'=>array('trans_rpt_leads','trans_leads_'.$this->getCompanyCode(),'master_user','master_status'),
									'join'=>array('','trans_rpt_leads.lead_code = trans_leads_'.$this->getCompanyCode().'.lead_code','master_user.id = trans_rpt_leads.lead_owner_code','master_status.id = trans_rpt_leads.status_code'),
									'column'=>$strColumArr,
									'where'=>$strWhereClauseArr,
									'order'=>array('lead_record_date'=>'desc')
								);
		
		/* getting number of lead count from location */
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
						/* Setting Value */
						$strReturnArr['data'][$strLeadArrKey][$strColumnArrValue['column']]	= $this->getLeadAttributeDetilsByAttributeKey($strColumnArrValue['column'], $strLeadArrValue[$strColumnArrValue['column']]);
					}
				}
			}
		}
		
		
		/* Rerurn the report data */
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
		
		$strReturnArr[]	= array('column'=>'lead_record_date','label'=>'Creating Date','is_date'=>'1');
		$strReturnArr[]	= array('column'=>'assigment_date','label'=>'Assg. Date','is_date'=>'1');
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
		$strReturnArr[]	= array('column'=>'lead_source_code','label'=>'Source','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getRegionDetails(),''));
		$strReturnArr[]	= array('column'=>'region_code','label'=>'Region','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getRegionDetails(),''));
		$strReturnArr[]	= array('column'=>'branch_code','label'=>'Branch','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getBranchDetails(),''));
		$strReturnArr[]	= array('column'=>'status_code','label'=>'Status','dropdown'=>'1','data'=>$this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),''));
		
		/* Return column array */
		return $strReturnArr;
	}
}