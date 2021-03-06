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
	/*Purpose 	: get lead follow-up panel.
	/*Inputs	: None
	/*Returns	: Lead follow-up panel. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadFollowDetails(){
		/* Variable initialization */
		$strDataArr						= array();
		$strDataArr['strStatusCode']	= $this->_objForm->getDropDown($this->getLeadStatusInParentChildArr(),'');
		$strDataArr['strTaskType']		= $this->_objForm->getDropDown($this->getTaskType(),'');
		
		/* Return the lead follow-up details */
		return $this->load->view('leads/lead-follow-up',$strDataArr,true);
	}
	
	/**********************************************************************/
	/*Purpose 	: get lead profile panel.
	/*Inputs	: None
	/*Returns	: Lead profile panel. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadPofilePanel(){
		/* Getting configured column */
		$strModuleArr 			= $this->getLeadAttributeList(); 
		$strLeadAttArr			= array();
		
		/* Creating widget panel */
		$leadObj	 	= new Lead($this->_objDataOperation, $this->getCompanyCode());
		$strModuleArr	= $leadObj->getLeadAttributesListByModuleUrl('');
		
		/* if all lead attributes found then do needful */
		if(!empty($strModuleArr)){
			/* Iterating the loop */
			foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
				$strLeadAttArr[$strModuleArrValue['attri_slug_key']] = array('column'=>$strModuleArrValue['attri_slug_key'], 'label'=>$strModuleArrValue['attri_slug_name'],$strModuleArrValue['attri_data_type']=>'');
			}
		}
		
		/* Lead source variable initialization */
		$strLeadSourceArr	= $this->getLeadSource();
		$strLeadSourcesArr	= array();
		
		/* if lead source found then do needful */
		if(!empty($strLeadSourceArr)){
			/* Iterating the loop */
			foreach($strLeadSourceArr as $strLeadSourceArrKey => $strLeadSourceArrValue){
				/* Setting value */
				$strLeadSourcesArr[$strLeadSourceArrValue->id]	= $strLeadSourceArrValue->description;
			}
		}
		/* removed used variables */
		unset($strLeadSourceArr);
		
		/* Setting the lead source */
		$strLeadAttArr['lead_source_code'] = array('disabled'=>'true','column'=>'lead_source_code', 'label'=>'Lead Source','dropdown'=>'1','data'=>$this->_objForm->getDropDown($strLeadSourcesArr,''));
		
		/* if ADMIN then allow to edit the lead source */
		if((int)$this->getAdminFlag() == 1){
			/* removed disabled index */
			unset($strLeadAttArr['lead_source_code']['disabled']);
		}
		
		/* Removed used variables */
		unset($leadObj);
		
		/* Creating widget panel */
		$widgetObj	 		= new Widget($this->_objDataOperation, $this->getCompanyCode());
		$strLeadAttrHTML 	= $widgetObj->getColumnAsSearchPanel($strLeadAttArr);
		$strLeadAttrHTML   .= '<input type="hidden" name="txtProfileid" id="txtProfileid" data-set="id" value= "" />';
		/* Removed used variables */
		unset($widgetObj);
		
		/* Return the lead follow-up details */
		return $this->load->view('leads/lead-profile',array('strLeadAttHTML' => $strLeadAttrHTML),true);
	}
	
	/**********************************************************************/
	/*Purpose 	: get lead profile details panel.
	/*Inputs	: None.
	/*Returns	: Lead profile details panel. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadProfileDetails(){
		//if($this->input->post('txtProfilelead_source_code')){
			/* Update lead information */
			//$this->setNewLeadDetails();
		//}
		
		/* Variable initialization */
		$strDataArr		= array();
		$pIntLeadCode	= ($this->input->post('txtLeadCode'))?$this->input->post('txtLeadCode'):'';
		
		/* if lead code is not passed then do needful */
		if($pIntLeadCode == ''){
			/* Return empty HTML */
			return jsonReturn(array('status'=>0,'message'=>$strDataArr),true);
		}
		
		/* Creating lead object */
		$leadObj	= new Lead($this->_objDataOperation, $this->getCompanyCode(), $this->getBranchCodes(),$this->getAllReportingList());
		/* Get lead information by lead code */
		$strDataArr	= $leadObj->getLeadDetialsByLogger(false,array('master_leads.id'=>getDecyptionValue($pIntLeadCode)));
		/* Removed used variables */
		unset($leadObj);
		
		/* if data found then do needful */
		if(!empty($strDataArr)){
			/* Setting value */
			$strDataArr[0]['lead_source_code']	= getEncyptionValue($strDataArr[0]['lead_source_code']);
			$strDataArr[0]['id']				= getEncyptionValue($strDataArr[0]['id']);
		}
		
		/* Creating communication object */
		$communicationhistoryObj 	= new communicationhistory($this->_objDataOperation, $this->getCompanyCode());
		/* Fetch the communication of requested leads */
		$strCommunicationHistoryArr	= $communicationhistoryObj->getCommuncationHistory(array('lead_code'=>getDecyptionValue($pIntLeadCode)));
		
		/* Checking communication history data set */
		if(!empty($strCommunicationHistoryArr)){
			/* Iterating the loop */
			foreach($strCommunicationHistoryArr as $intRecordIndex => $strCommunicationHistoryArrValueArr){
				/* Iterating the communication history column index */
				foreach($strCommunicationHistoryArrValueArr as $strColumn => $strColumnValue){
					/* Checking for lead owner details */
					if($strColumn == 'lead_owner_code'){
						/* Setting the value */
						$strCommunicationHistoryArr[$intRecordIndex][$strColumn] = $this->getLeadAttributeDetilsByAttributeKey('lead_owner_name', $strColumnValue);
					}else{
						/* Setting the value */
						$strCommunicationHistoryArr[$intRecordIndex][$strColumn] = $this->getLeadAttributeDetilsByAttributeKey($strColumn, $strColumnValue);
					}
				}
			}
		}
		
		/* Get communication HTML */
		$strDataArr[0]['strHistory']= $this->load->view('leads/lead-communicaion-history',array('strCommunicaionHistoryArr' => $strCommunicationHistoryArr),true);
		/* Removed used variables */
		unset($communicationhistoryObj, $strCommunicationHistoryArr);
		
		/* if lead code is not passed then do needful */
		if(empty($strDataArr)){
			/* Return the lead details */
			return jsonReturn(array('status'=>0,'message'=>array()),true);
		}else{
			/* Return the lead details */
			return jsonReturn(array('status'=>1,'message'=>$strDataArr),true);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Creating lead transfer panel.
	/*Inputs	: None.
	/*Returns	: Lead transfer panel. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLeadTransferPanel(){
		/* Variable initialization */
		$strDataArr						= array();
		$strDataArr['strRegionArr']		= $this->_objForm->getDropDown($this->getRegionDetails(),'');
		$strDataArr['strBranchArr']		= $this->_objForm->getDropDown($this->getBranchDetails(),'');
		$strDataArr['strTaskType']		= $this->_objForm->getDropDown($this->getTaskType(),'');
		
		/* Return the lead follow-up details */
		return $this->load->view('leads/lead-transfer',$strDataArr,true);
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
		
		/* Variable initialization */
		$strElementPreFix	= 'txtWidget';
		/* Updating the lead information */
		if($this->input->post('txtProfilelead_source_code')){
			/* Setting element prefix */
			$strElementPreFix	= 'txtProfile';
		}
		
		/* Iterating the attribute loop */
		foreach($strWidgetArr as $strWidgetArrKey => $strWidgetArrValue){
			/* Value of post element */
			$strValueOfElement	= $this->input->post($strElementPreFix.$strWidgetArrValue['attri_slug_key']);
			/* Setting key and user input value */
			if($strWidgetArrValue['attri_data_type'] == 'select'){
				$strAttributeArr[$strWidgetArrValue['attri_slug_key']]	= getDecyptionValue($strValueOfElement);
			}else{
				$strAttributeArr[$strWidgetArrValue['attri_slug_key']]	= $strValueOfElement;
			}
				
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
		
		/* Updating the lead information */
		if($this->input->post('txtProfilelead_source_code')){
			/* lead code */
			$intLeadCode	= getDecyptionValue($this->input->post('txtProfileid'));
			/* checking for lead code object */
			if(($intLeadCode == '') || ((int)$intLeadCode == 0)){
				jsonReturn(array('status'=>0,'message'=>'Invalid Lead code found.'), true);
			}
			
			/* lead source details */
			$intLeadSource	= getDecyptionValue($this->input->post('txtProfilelead_source_code'));
			/* checking for lead source object */
			if(($intLeadSource == '') || ((int)$intLeadSource == 0)){
				jsonReturn(array('status'=>0,'message'=>'Lead source is not selected.'), true);
			}
			
			/* variable initialization */
			$strAttributeArr['lead_source_code']	= $intLeadSource;
			
			/* Creating lead object */
			$leadObj		= new Lead($this->_objDataOperation, $this->getCompanyCode(), $this->getBranchCodes(),$this->getAllReportingList());
			/* setting lead new information */
			$intLeadStatus 	= $leadObj->setLeadUpdatedDetails($intLeadCode, $strAttributeArr,$this->getUserCode());
			/* removed used variable */
			unset($leadObj, $strLeadAttArr);
			
			if($intLeadStatus > 0){
				return jsonReturn(array('status'=>1,'message'=>'Lead details updated successfully.'),true);
			}else{
				return jsonReturn(array('status'=>0,'message'=>'Error occurred while updated lead information.'),true);
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
		$intLeadCodeArr		= ($this->input->post('txtLeadCode')!='')?((strstr($this->input->post('txtLeadCode'),DELIMITER))?explode(DELIMITER,$this->input->post('txtLeadCode')):array($this->input->post('txtLeadCode'))):array();
		$intLeadownerCodeArr= ($this->input->post('txtLeadOwnerCode')!='')?((strstr($this->input->post('txtLeadOwnerCode'),DELIMITER))?explode(DELIMITER,$this->input->post('txtLeadOwnerCode')):array($this->input->post('txtLeadOwnerCode'))):array();
		//$intLeadownerCode	= ($this->input->post('txtLeadOwnerCode')!='')?getDecyptionValue($this->input->post('txtLeadOwnerCode')):0;
		$blnIsCLosedStatus	= json_decode($this->isOpenStatus($this->input->post('cboStatusCode')));
		$blnIsCLosedStatus  = $blnIsCLosedStatus->isopen;
		
		/* Checking to all valid information passed */
		if(($strDate == '') && ($blnIsCLosedStatus == 1)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up date is not selected.'), true);
		}
		if(($strTime == '') && ($blnIsCLosedStatus == 1)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up time is not selected.'), true);
		}
		if($intStatusCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Status is not selected.'), true);
		}
		if(($intTaskTypeCode == 0) && ($blnIsCLosedStatus == 1)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Task type is not selected.'), true);
		}
		if($strComments == ''){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Follow-up comments is empty.'), true);
		}
		if(empty($intLeadCodeArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead reference is not found. Can not perform requested action.'), true);
		}
		if(empty($intLeadownerCodeArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead Owner reference is not found. Can not perform requested action.'), true);
		}
		 
		/* Creating task object */
		$taskObj		= new Task($this->_objDataOperation, $this->getCompanyCode());
		
		/* Iterating the lead loop */
		foreach($intLeadCodeArr as $intLeadCodeKey => $intLeadCode){
			/* Task Update Array */
			$strTaskUpdateArr						= array();
			$strTaskUpdateArr['leadCode']			= getDecyptionValue($intLeadCode);
			$strTaskUpdateArr['leadOwnerCode']		= getDecyptionValue($intLeadownerCodeArr[$intLeadCodeKey]);
			$strTaskUpdateArr['next_follow_date']	= getDateFormat($strDate.$strTime,1).'00';
			$strTaskUpdateArr['taskTypeCode']		= $intTaskTypeCode;
			$strTaskUpdateArr['updatedBy']			= $this->getUserCode();
			$strTaskUpdateArr['comments']			= $strComments;
			$strTaskUpdateArr['statusCode']			= $intStatusCode;
			$strTaskUpdateArr['statusType']			= $blnIsCLosedStatus;
			
			/* Get task type list */
			$intTaskStatus	= $taskObj->setTask($strTaskUpdateArr);	
		}
		
		/* Removed used variables */
		unset($taskObj, $intLeadCodeArr, $intLeadownerCodeArr);
			
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
	/*Purpose 	: Setting new lead owner.
	/*Inputs	: None.
	/*Returns	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setNewLeadOwner(){
		/* Variable initialization */
		$intLeadCodeArr		= ($this->input->post('txtLeadCode'))?explode( DELIMITER, ($this->input->post('txtLeadCode'))):array();
		$intUserBranchArr	= ($this->input->post('cboUSerCode'))?explode( DELIMITER, $this->input->post('cboUSerCode')):array();
		$intUserCode		=	$intBranchCode		= $intRegionCode 	= 0;
		
		/* Checking requested value is passed or not */
		if(empty($intLeadCodeArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Invalid lead code. Operation can not perform.'), true);
		}else{
			/* Iterating the loop */
			foreach($intLeadCodeArr as $intLeadCodeArrKey => $intLeadCodeArrValue){
				/* Setting the lead code in readable format */
				$intLeadCodeArr[$intLeadCodeArrKey]	= getDecyptionValue($intLeadCodeArrValue);
			}
		}
		if(empty($intUserBranchArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Lead owner is not select or invalid lead owner code. Operation can not perform.'), true);
		}else{
			/* Setting value */
			$intUserCode		= isset($intUserBranchArr[1])?getDecyptionValue($intUserBranchArr[1]):0;
			$intBranchCode		= isset($intUserBranchArr[0])?getDecyptionValue(getDecyptionValue($intUserBranchArr[0])):0;
			$intRegionCode		= isset($intUserBranchArr[2])?getDecyptionValue($intUserBranchArr[2]):0;
			
			/* Removed used variables */
			unset($intUserBranchArr);
			
			/* Checking requested value is passed or not */
			if((int)$intUserCode == 0){
				/* Return Information */
				jsonReturn(array('status'=>0,'message'=>'Lead owner is not select or invalid lead owner code. Operation can not perform.'), true);
			}
			if((int)$intBranchCode == 0){
				/* Return Information */
				jsonReturn(array('status'=>0,'message'=>'Invalid Branch code. Operation can not perform.'), true);
			}
			if((int)$intRegionCode == 0){
				/* Return Information */
				jsonReturn(array('status'=>0,'message'=>'Invalid Region code. Operation can not perform.'), true);
			}
		}
		/* Creating lead object */
		$ObjLead	= new Lead($this->_objDataOperation, $this->getCompanyCode(), $this->getBranchCodes(),$this->getAllReportingList());
		/* Get exiting lead owner code details */
		$strLeadDetailArr 	= $ObjLead->getLeadDetialsByLogger(false,array('master_leads.id'=>$intLeadCodeArr));
		/* if requested lead details not found then do needful */
		if(empty($strLeadDetailArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Lead details not found.'), true);
		}else{
			/* Iterating the loop for assigning the loop */
			foreach($strLeadDetailArr as $strLeadDetailArrKey => $strLeadDetailArrValue){
				/* Setting lead owner */
				$ObjLead->setLeadOwner($strLeadDetailArrValue['id'], $intUserCode, array('lead_owner_code'=>$intUserCode,'status_code'=>$strLeadDetailArrValue['status_code'],'updated_by'=>$this->getUserCode()));
			}
		}
		
		/* Return Information */
		jsonReturn(array('status'=>1,'message'=>'Lead transferred successfully.'), true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get Branch List by Region code.
	/*Inputs	: None.
	/*Returns	: Branch List by Region code. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getBranchListByRegionCodeAct(){
		/* Variable initialization */
		$strRegion	= ($this->input->post('txtRegionCode')!='')?array($this->input->post('txtRegionCode')):array();
		/* Return the response */
		jsonReturn($this->getBranchListByRegionCode($strRegion),true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking is requested status is open or in close status group.
	/*Inputs	: None.
	/*Returns	: Open / Close status group. 
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function isOpenStatusCheck(){
		/* Return the status */
		die($this->isOpenStatus($this->input->post('statusCode')));
	}
}