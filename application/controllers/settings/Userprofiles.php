<?php
/***********************************************************************/
/* Purpose 		: Application user profile.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Userprofiles extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user';
	private $_strModuleName			= "User Profile";
	private $_strModuleForm			= "frmUserProfile";
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();
		/* Getting current page number */
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting user role list */
		$strUserRoleArr['dataSet'] 				= $this->_getUserProfilDetails(0,'',false,false, $intCurrentPageNumber);
		$strUserRoleArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strUserRoleArr['pagination'] 			= getPagniation($this->_getUserProfilDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getUserProfileDetailsByCode';
		$strUserRoleArr['strCustomUri']			= SITE_URL.'settings/'.__CLASS__.'/getLocationByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'userProfileModel';
		$strUserRoleArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strUserRoleArr['strZoneArr']			= $this->_objForm->getDropDown(getArrByKeyvaluePairs($this->getLocationByCode(),'id','description'),'',false);
		$strUserRoleArr['strCustomRoleArr']		= $this->_getRoleDetails();
		$strUserRoleArr['strSystemRoleArr']		= $this->_getRoleDetails(1);
		$strUserRoleArr['strUserStatsArr']		= $this->_objForm->getDropDown(array('1'=>'Active','0'=>'In-Active'),'',false);
		
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/userprofiles', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get user profile details by code.
	/*Inputs	: None.
	/*Returns 	: User Role Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getUserProfileDetailsByCode(){
		/* Setting the user profile code */
		$intUserCode 				= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strUserArr					= array();
		/* Checking the user profile Code shared */
		if($intUserCode > 0){
			/* getting requested user profile code details */
			$strUserArr				= $this->_getUserProfilDetails($intUserCode);
			
			/* if record not found then do needful */
			if(empty($strUserArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strUserArr, true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid user profile code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the user profile details.
	/*Inputs	: $pUserCodeCode :: User profile description,
				: $pStrUserEmailAddress :: User email address name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserProfilDetails($pUserCodeCode = 0, $pStrUserEmailAddress = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strUserRoleArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array($this->_strPrimaryTableName.'.company_code'=>$this->getCompanyCode());
		
		/* if user profile filter code is passed then do needful */
		if($pUserCodeCode < 0){
			/* Adding User profile code filter */
			$strWhereClauseArr	= array('company_code'=>1);
		/* if profile filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strUserName			= ($this->input->post('txtUserName') != '')?$this->input->post('txtUserName'):'';
			$strEmailAddress		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
			$intUserRoleCode		= ($this->input->post('cboRoleCode') != '')?getDecyptionValue($this->input->post('cboRoleCode')):0;
			$intUserSystemRoleCode	= ($this->input->post('cboUserSystemRole') != '')?getDecyptionValue($this->input->post('cboUserSystemRole')):0;
			$intUserStatusCode		= ($this->input->post('cboUserStatus') != '')?getDecyptionValue($this->input->post('cboUserStatus')):0;
			
			if($strUserName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('name like'=>$strUserName));
			}
			if($strEmailAddress != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('email like'=>$strEmailAddress));
			}
			if($intUserRoleCode != 0){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('role_code'=>$intUserRoleCode));
			}
			if($intUserRoleCode != 0){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('system_role_code'=>$intUserSystemRoleCode));
			}
			if($intUserRoleCode != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('is_active'=>$intUserStatusCode));
			}
		}else{
			/* Getting status categories */
			if($pUserCodeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id !='=>$pUserCodeCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id'=>$pUserCodeCode));
				}
			}
		}
		
		/* filter by email name */
		if($pStrUserEmailAddress !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('email like'=>$pStrUserEmailAddress));
		}
		
		/* Filter array */
		$strFilterArr	= array(
									'table'=>array($this->_strPrimaryTableName,'master_role'),
									'column'=>array($this->_strPrimaryTableName.'.*','master_role.description as role_name'),
									'join'=>array('',$this->_strPrimaryTableName.'.role_code =  master_role.id'),
									'where'=>$strWhereClauseArr
								);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count('.$this->_strPrimaryTableName.'.id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pUserCodeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the status list */
		$strUserProfileArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if edit request then do needful */
		if((int)$pUserCodeCode > 0){
			$strUserProfileArr[0]['role_code']			= getEncyptionValue($strUserProfileArr[0]['role_code']);
			$strUserProfileArr[0]['system_role_code']	= getEncyptionValue($strUserProfileArr[0]['system_role_code']);
			$strUserProfileArr[0]['is_active']			= getEncyptionValue($strUserProfileArr[0]['is_active']);
			
			/* Getting Location Filter array */
			$strFilterArr		= array('table'=>'trans_user_location','where'=>array('user_code'=>$pUserCodeCode));
			/* get location and manager details */
			$strUserLocationArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
			$strUserProfileArr	= array_merge($strUserProfileArr, $strUserLocationArr);
			/* Removed used variables */
			unset($strUserLocationArr);
			
			/* Creating Location object */
			$objLocation				= new location($this->_objDataOperation, $this->getCompanyCode());
			$strUserLocationArr			= $objLocation->getLocationsByUserCode($pUserCodeCode);
			$strUserProfileArr			= array_merge($strUserProfileArr, $strUserLocationArr);
			$strLocationAssiactionArr	= $strLocationArr	= array();
			
			/* if location array found then do needful */
			if(!empty($strUserLocationArr)){
				/* iterating the loop */
				foreach($strUserLocationArr as $strUserLocationArrKey => $strUserLocationArrValue){
					$strLocationArr['zone'][]	= $strUserLocationArrValue['zone_code'];
				}
				
				/* Get all Region,City, Area and Branches selected */
				$strLocationArr	=  $objLocation->getLocationsByZoneCode($strLocationArr['zone']);
			}
			
			/* if location array found then do needful */
			if(!empty($strLocationArr)){
				/* iterating the loop */
				foreach($strLocationArr as $strLocationArrKey => $strLocationArrValue){
					$strLocationAssiactionArr['zone'][getEncyptionValue($strLocationArrValue['zone_code'])]		= $strLocationArrValue['zone_name'];
					$strLocationAssiactionArr['region'][getEncyptionValue($strLocationArrValue['region_code'])]	= $strLocationArrValue['region_name'];
					$strLocationAssiactionArr['city'][getEncyptionValue($strLocationArrValue['city_code'])]		= $strLocationArrValue['city_name'];
					$strLocationAssiactionArr['area'][getEncyptionValue($strLocationArrValue['area_code'])]		= $strLocationArrValue['area_name'];
					$strLocationAssiactionArr['branch'][getEncyptionValue($strLocationArrValue['branch_code'])]	= $strLocationArrValue['branch_name'];
				}
				/* Setting Location Drop-down list */
				$strLocationAssiactionArr['zone']	= $this->_objForm->getDropDown($strLocationAssiactionArr['zone'],'',false);
				$strLocationAssiactionArr['region']	= $this->_objForm->getDropDown($strLocationAssiactionArr['region'],'',false);
				$strLocationAssiactionArr['city']	= $this->_objForm->getDropDown($strLocationAssiactionArr['city'],'',false);
				$strLocationAssiactionArr['area']	= $this->_objForm->getDropDown($strLocationAssiactionArr['area'],'',false);
				$strLocationAssiactionArr['branch']	= $this->_objForm->getDropDown($strLocationAssiactionArr['branch'],'',false);
				/* Creating final return array */
				$strUserProfileArr					= array_merge($strUserProfileArr, $strLocationAssiactionArr);
			}
			
			
			
			/* Removed used variables */
			unset($objLocation, $strUserLocationArr, $strLocationAssiactionArr);
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strUserProfileArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the user profile details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setUserProfile(){
		/* variable initialization */
		$intUserCode			= ($this->input->post('txtUserCode') != '')?$this->input->post('txtUserCode'):0;
		$strUserName			= ($this->input->post('txtUserName') != '')?$this->input->post('txtUserName'):'';
		$strEmailAddress		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
		$strPassword			= ($this->input->post('txtPassword') != '')?$this->input->post('txtPassword'):'';
		$intUserRoleCode		= ($this->input->post('cboRoleCode') != '')?getDecyptionValue($this->input->post('cboRoleCode')):0;
		$intUserSystemRoleCode	= ($this->input->post('cboUserSystemRole') != '')?getDecyptionValue($this->input->post('cboUserSystemRole')):0;
		$intUserStatusCode		= ($this->input->post('cboUserStatus') != '')?getDecyptionValue($this->input->post('cboUserStatus')):0;
		$strZoneCode			= ($this->input->post('cboZone') != '')?$this->input->post('cboZone'):'';
		$strRegionCode			= ($this->input->post('cboRegion') != '')?$this->input->post('cboRegion'):'';
		$strCityCode			= ($this->input->post('cboCity') != '')?$this->input->post('cboCity'):'';
		$strAreaCode			= ($this->input->post('cboArea') != '')?$this->input->post('cboArea'):'';
		$strBranchCode			= ($this->input->post('cboBranchCode') != '')?$this->input->post('cboBranchCode'):'';
		$intManagerCode			= ($this->input->post('cboReportingManager') != '')?getDecyptionValue($this->input->post('cboReportingManager')):0;
		$blnEditRequest			= (($intUserCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		$strBranchCodeArr		= array();
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strUserName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User name field is empty.'), true);
		}else if(($strEmailAddress == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User email field is empty.'), true);
		}else if(($strPassword == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Password field is empty.'), true);
		}else if(($intUserRoleCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User Custom Role is not selected.'), true);
		}else if(($intUserSystemRoleCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User System Role is not selected.'), true);
		}else if(($strZoneCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Zone is not selected.'), true);
		}else if(($strRegionCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Region is not selected.'), true);
		}else if(($strCityCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'City is not selected.'), true);
		}else if(($strAreaCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Area is not selected.'), true);
		}else if(($strBranchCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Branch code is not selected.'), true);
		}else if(($intManagerCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Reporting Manager is not selected.'), true);
		}
		
		/* Checking enter email address is already register or not */
		$strUserDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('user_email'=>$strEmailAddress)));
		
		/* if status already exists then do needful */
		if(!empty($strUserDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested User is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
											'data'=>array(
														'user_name'=>$strUserName,
														'user_email'=>$strEmailAddress,
														'password'=>md5($strPassword),
														'company_code'=>$this->getCompanyCode(),
														'is_active'=>$intUserStatusCode,
														'is_admin'=>0,
														'role_code'=>$intUserRoleCode,
														'system_role_code'=>$intUserSystemRoleCode
													)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intUserCode);
				/* Updating user profile in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding user profile in the database */
				$intUserCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Creating branch code array of the id's  */
			$strBranchCode	= explode(',',$strBranchCode);
			
			/* iterating the loop */
			foreach($strBranchCode as $strBranchCodeKey => $strBranchCodeValue){
				/* decoding the value */
				$strBranchCodeArr[]	= getDecyptionValue($strBranchCodeValue);
			}
			/* if branch code is empty then do needful */
			if(!empty($strBranchCodeArr)){
				/* De-Activating the user locations */
				$this->_objDataOperation->setUpdateData(array(
																'table'=>'trans_user_location',
																'where'=>array(
																				'user_code'=>$intUserCode
																			),
																'data'=>array(
																				'deleted'=>1,
																				'updated_by'=>$this->getUserCode()
																		)
															)
														);
				/* Iterating the loop */
				foreach($strBranchCodeArr as $strBranchCodeArrKey => $strBranchCodeArrValue){
					/* adding new location */
					$this->_objDataOperation->setDataInTable(array(
																		'table'=>'trans_user_location',
																		'data'=>array(
																						'branch_code'=>$strBranchCodeArrValue,
																						'manager_user_code'=>$intManagerCode,
																						'user_code'=>$intUserCode,
																						'updated_by'=>$this->getUserCode(),
																						'company_code'=>$this->getCompanyCode()
																					)
																	)
															);
				}
				
				/* removed used variables */
				unset($strBranchCodeArr);
			}
			
			/* Removed used variables */
			unset($strDataArr, $strBranchCode);
			
			/* checking last insert id / updated record count */
			if($intUserCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'User Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'User added successfully.'), true);
				}
			}else{
				jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
			}
		}
	}

	/**********************************************************************/
	/*Purpose 	: Delete the record from table of requested code.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function deleteRecord(){
		/* Variable initialization */
		$intUserRoleCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not role code pass then do needful */
		if($intUserRoleCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid user role code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intUserRoleCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested User Role deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/* Purpose 		: Get location details by code.
	/* Inputs		: $pStrFilterParamArr :: Location filter param array.
	/* Returns 		: Location details
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getLocationByCode($pStrFilterParamArr = array()){
		/* variable initialization */
		$strLocationParentCode	= ($this->input->post('txtDataCodes')!='')?$this->input->post('txtDataCodes'):((!empty($pStrFilterParamArr) && (isset($pStrFilterParamArr['code'])))?$pStrFilterParamArr['code']:getEncyptionValue('0'));
		$strLocationType		= ($this->input->post('txtExtraParam')!='')?getDecyptionValue($this->input->post('txtExtraParam')):((!empty($pStrFilterParamArr) && (isset($pStrFilterParamArr['type'])))?$pStrFilterParamArr['type']:'1');
		$strLcoationCodeArr		= $strUserArr	= array();
		
		/* Creating array of the id's  */
		$strLocationParentCodeArr	= explode(',',$strLocationParentCode);
		
		
		/* iterating the loop */
		foreach($strLocationParentCodeArr as $strLocationParentCodeKey => $strLocationParentCodeValue){
			/* decoding the value */
			$strLcoationCodeArr[]	= getDecyptionValue($strLocationParentCodeValue);
		}
		
		/* removed used variables */
		unset($strLocationParentCodeArr, $strLocationParentCode);
		
		/* Creating Location object */
		$objLocation			= new location($this->_objDataOperation, $this->getCompanyCode());
		/* Creating the location array */
		$strLocationArrValue	= $objLocation->getLocationDetails(1,array(),$strLcoationCodeArr);
		
		/* if AJAX request then do needful */
		if(isAjaxRequest()){
			/* get user at that location */
			$strUserArr			= $objLocation->getEmployeeByLocations($strLocationType,$strLcoationCodeArr);
		}
		
		/* removed used variables */
		unset($objLocation);
		
		if(isAjaxRequest()){
			/* variable initialization */
			$strDefaultUserArr	= array('0'=>array('user_code'=>'-1','user_name'=>'System Administrator'));
			/* if empty no uses found then do needful */
			if(empty($strUserArr)){
				/* Setting details Administrator array */
				$strUserArr	= $strDefaultUserArr;
			}else{
				/* Setting details Administrator array with other Reporting user list */
				$strUserArr	= array_merge($strUserArr, $strDefaultUserArr);
			}
			
			/* getting reporting user list */
			$strReturnArr['reporting']	= $this->_objForm->getDropDown(getArrByKeyvaluePairs($strUserArr,'user_code','user_name'),'',true);
			
			/* Removed used variable */
			unset($strUserArr);
			
			/* Return the value */
			$strReturnArr['dataset']	=  $this->_objForm->getDropDown(getArrByKeyvaluePairs($strLocationArrValue,'id','description'),'',false);
			
			jsonReturn($strReturnArr,true);
		}else{
			/* Return the value */
			return $strLocationArrValue;
		}
	}
	
	/**********************************************************************/
	/* Purpose 		: Get role details by type.
	/* Inputs		: pIntRoleType :: Role Type.
	/* Returns 		: Role details
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getRoleDetails($pIntRoleType = 0){
		/* variable initialization */
		$roleObj	= new Role($this->_objDataOperation, $this->getCompanyCode());
		
		/* if custom request then do needful */
		if($pIntRoleType == 0){
			/* Get custom Role List */
			$strRoleArr	= $roleObj->getCustomRoleDetails();
		}else{
			/* Get System Role */
			$strRoleArr	= $roleObj->getSystemRoleDetails();
		}
		/* Removed used variables */
		unset($roleObj);
		
		/* Return drop down list */
		return $this->_objForm->getDropDown(getArrByKeyvaluePairs($strRoleArr,'id','description'),'',false);
	}
}