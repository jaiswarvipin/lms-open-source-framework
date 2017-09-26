<?php
/***********************************************************************/
/* Purpose 		: Manage the lead operation related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class LeadsOperation extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_leads';
	private $_strModuleName			= "Leads";
	private $_strModuleForm			= "frmNewLeads";
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
		
	}
	
	/**********************************************************************/
	/*Purpose 	: Creating new lead configured attributes panel.
	/*Inputs	: None
	/*Returns	: None. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getNewLeadPanel(){
		/* Creating widget object */
		$widgetObj	 	= new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* Getting the Module HTML */
		$strWidgetHTML 	= $widgetObj->getWidgetAttributesWithLayout('leadsoperation/leadsoperation/getnewleadpanel');
		/* Removed the variables */
		unset($widgetObj);
		/* Return the value */
		return $strWidgetHTML;
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Creating new lead configured attributes panel.
	/*Inputs	: None
	/*Returns	: None. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadFollowDetails(){
		/* Variable initialization */
		$strDataArr						= array();
		$strDataArr['strStatusCode']	= $this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),'');
		$strDataArr['strTaskType']		= $this->_objForm->getDropDown(getArrByKeyvaluePairs($this->_getTaskType(),'id','description'),'');
		
		/* Return the lead follow-up details */
		return $this->load->view('leads/lead-follow-up',$strDataArr,true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Set new lead details.
	/*Inputs	: None.
	/*Returns	: Request response. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setNewLeadDetails(){
		/* Variable initialization */
		$strAttributeArr	= array();
		
		/* Creating widget object */
		$widgetObj	 	= new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* Getting the Module HTML */
		$strWidgetArr 	= $widgetObj->getWidgetAttributesWithLayout(ADD_NEW_LEAD_MODULE_URL, false);
		/* Removed the variables */
		unset($widgetObj);
		
		/* if not attribute found then of needful */
		if(empty($strWidgetArr)){
			jsonReturn(array('status'=>0,'message'=>'No lead attributed found to add lead module.'), true);
		}
		
		/* Iterating the attribute loop */
		foreach($strWidgetArr as $strWidgetArrKey => $strWidgetArrValue){
			/* Value of post element */
			$strValueOfElement	= $this->input->post('txtWidget'.$strWidgetArrValue['attri_slug_key']);
			/* Setting key and user input value */
			$strAttributeArr[$strWidgetArrValue['attri_slug_key']]	= $strValueOfElement;
			
			/* Validation */
			if($strWidgetArrValue['is_mandatory'] == 1){
				/* Checking for validation */
				switch($strWidgetArrValue['attri_validation']){
					case 'numeric':
						if(!is_numeric($strValueOfElement)){
							jsonReturn(array('status'=>0,'message'=>$strWidgetArrValue['attri_slug_name'].' is not value number.'), true);
						}
						break;
					case 'string':
						if(trim($strValueOfElement) == ''){
							jsonReturn(array('status'=>0,'message'=>$strWidgetArrValue['attri_slug_name'].' is empty.'), true);
						}
						break;
					case 'email':
						if(!filter_var(filter_var($strValueOfElement, FILTER_SANITIZE_EMAIL),FILTER_VALIDATE_EMAIL)){
							jsonReturn(array('status'=>0,'message'=>$strWidgetArrValue['attri_slug_name'].' is not valid email address.'), true);
						}
						break;
					case 'contact-no':
						if(!preg_match('/^[0-9]{10}+$/', $strValueOfElement)){
							jsonReturn(array('status'=>0,'message'=>$strWidgetArrValue['attri_slug_name'].' is not valid contact no.'), true);
						}
						break;
				}
			}
		}
		
		/* Setting key and user input lead source value */
		$strAttributeArr['lead_source_code']	= getDecyptionValue($this->input->post('cboWidgetLeadSource'));
		$strAttributeArr['is_direct']			= 1;
		$strAttributeArr['lead_owner_code']		= $this->getUserCode();
		
		/* checking for lead source object */
		if($this->input->post('cboWidgetLeadSource') == ''){
			jsonReturn(array('status'=>0,'message'=>'Lead source is not selected.'), true);
		}
		
		/* Creating lead object */
		$leadObj		= new Lead($this->_objDataOperation, $this->getCompanyCode(), $this->getBranchCodes());
		/* Getting lead array */
		$intLeadCode	= $leadObj->setLeadDetails($strAttributeArr);
		/* removed used variables */
		unset($leadObj, $strAttributeArr);
		
		/* if response is negative then do needful */
		if(!is_numeric($intLeadCode)){
			jsonReturn(array('status'=>0,'message'=>$intLeadCode), true);
		}else{
			jsonReturn(array('status'=>1,'message'=>'Lead added successfully.'), true);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting lead follow-up date.
	/*Inputs	: None.
	/*Returns	: Operation status 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setlLeadFollowupDetails(){
		/* Variable initialization */
		$strDate			= ($this->input->post('txtFollowUpDate')!='')?$this->input->post('txtFollowUpDate'):'';
		$strTime			= ($this->input->post('txtFollowUpTime')!='')?$this->input->post('txtFollowUpTime'):'';
		$intStatusCode		= ($this->input->post('cboStatusCode')!='')?getDecyptionValue($this->input->post('cboStatusCode')):0;
		$intTaskTypeCode	= ($this->input->post('cboTaskTypeCode')!='')?getDecyptionValue($this->input->post('cboTaskTypeCode')):0;
		$strComments		= ($this->input->post('txtComments')!='')?$this->input->post('txtComments'):'';
		$intLeadCode		= ($this->input->post('txtLeadCode')!='')?getDecyptionValue($this->input->post('txtLeadCode')):0;
		$intLeadownerCode	= ($this->input->post('txtLeadOwnerCode')!='')?getDecyptionValue($this->input->post('txtLeadOwnerCode')):0;
		
		/* Checking to all valid information passed */
		if($strDate == ''){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up date is not selected.'), true);
		}
		if($strTime == ''){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up time is not selected.'), true);
		}
		if($intStatusCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Status is not selected.'), true);
		}
		if($intTaskTypeCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Task type is not selected.'), true);
		}
		if($strComments == ''){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up comments is empty.'), true);
		}
		if($intLeadCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead reference is not found. Can not perform requested action.'), true);
		}
		if($intLeadownerCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead Owner reference is not found. Can not perform requested action.'), true);
		}
		
		/* Task Update Array */
		$strTaskUpdateArr						= array();
		$strTaskUpdateArr['leadCode']			= $intLeadCode;
		$strTaskUpdateArr['leadOwnerCode']		= $intLeadownerCode;
		$strTaskUpdateArr['next_follow_date']	= getDateFormat($strDate.$strTime,1).'00';
		$strTaskUpdateArr['taskTypeCode']		= $intTaskTypeCode;
		$strTaskUpdateArr['updatedBy']			= $this->getUserCode();
		$strTaskUpdateArr['comments']			= $strComments;
		$strTaskUpdateArr['statusCode']			= $intStatusCode;
		
		/* Creating task object */
		$taskObj		= new Task($this->_objDataOperation, $this->getCompanyCode());
		/* Get task type list */
		$intTaskStatus	= $taskObj->setTask($strTaskUpdateArr);
		/* Removed used variables */
		unset($taskObj);
		
		/* Return task status */
		if($intTaskStatus == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Error occurred while setting the follow-up details.'), true);
		}else{
			/* Return Information */
			jsonReturn(array('status'=>1,'message'=>'Follow-up details are set successfully.'), true);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Get lead task type details .
	/*Inputs	: None.
	/*Returns	: Task type List. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function _getTaskType(){
		/*Variable initialization */
		$strReturnArr	= array();
		/* Creating task object */
		$taskObj		= new Task($this->_objDataOperation, $this->getCompanyCode());
		/* Get task type list */
		$strReturnArr	= $taskObj->getTaskTypeByCompanyCode();
		/* Removed used variables */
		unset($taskObj);
		/* Return Task Type */
		return $strReturnArr;
	}
}