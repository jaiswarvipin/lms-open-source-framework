<?php 
/***********************************************************************/
/* Purpose 		: Managing the logger request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger{
	/* Variable initialization */
	private $_objDefaultModel	= null;
	private $_strPrimaryTable	= "trans_logger";
	private $_strLoggerCode		= '';
	
	/*******************************************************************/
	/*Purpose	: Default method to be executed.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function __construct(){
		/* Creating the default model */
		$this->_objDefaultModel	= new Dbrequestprocess_model();
	}


	/*******************************************************************/
	/*Purpose	: Setting logger object.
	/*Inputs	: $pIntUserCode :: User code.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	/* Checking for requested user authincation */
	public function setLogger($pIntUserCode = 0){
		/* variable initialization */
		$strLoggerArr	= array();

		/* if user code is not passed then do needful */
		if($pIntUserCode == 0){
			return;
		}

		/* Getting user details */
		$strResponseArr	= $this->_objDefaultModel->getDataFromTable(array('table'=>'master_user', 'where'=>array('id'=>$pIntUserCode)));

		/* if not response found then do needful */
		if(empty($strResponseArr)){
			/* Return eror details */
			jsonReturn(array('status'=>0,'message'=>'Error occurred while generating the login instance. please try after some time.'),true);
		}else{
			/* Set logger personal and logger information */
			$strLoggerArr['user_info']	= $strResponseArr[0];
		}
		
		/* if user object found then do needful */
		if(isset($strLoggerArr['user_info']) && (!empty($strLoggerArr['user_info']))){
			/* Getting module access details */
			$strModuleArr	= $this->_objDefaultModel->getDataFromTable(
																			array(
																					'table'=>array('trans_module_access','master_modues'),
																					'join'=>array('','trans_module_access.module_code = master_modues.id'),
																					'column'=>array('master_modues.id', 'master_modues.description','master_modues.module_url','master_modues.parent_code'),
																					'where'=>array(
																									'role_code'=>$strLoggerArr['user_info']['role_code']
																								),
																					'order'=>array('master_modues.id'=>'asc')
																				)
																		);
			
			/* if module access details found  then do needful */
			if(!empty($strModuleArr)){
				/* Variable initialization */
				$strModuleAccessArr	= array();
				$strMenuArr['main']	= '<ul id="nav-mobile" class="hide-on-med-and-down"><li class="w100">&nbsp;</li>';
				$strMenuArr['child']= '';
				
				/* Iterating the loop */
				foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
					if($strModuleArrValue['parent_code'] == 0){
						$strModuleAccessArr[$strModuleArrValue['id']]												= $strModuleArrValue;
					}else{
						$strModuleAccessArr[$strModuleArrValue['parent_code']]['child'][$strModuleArrValue['id']]	= $strModuleArrValue;
					}					
				}
				
				/* if module align array fund then do needful */
				if(!empty($strModuleAccessArr)){
					/* Iterating the loop */
					foreach($strModuleAccessArr as $strModuleAccessArrKey => $strModuleAccessArrValue){
						/* variable initialization */
						$blnHavingChild	= false;
						
						/* Checking for child menu */
						if(isset($strModuleAccessArrValue['child'])){
							$blnHavingChild		= true;
							$strMenuArr['child']  .= '<ul id="'.getSlugify(strtolower($strModuleAccessArrValue['description'])).'" class="dropdown-content">';
							/* Iterating the loop */
							foreach($strModuleAccessArrValue['child'] as $strModuleAccessArrValueKey => $strModuleAccessArrValueDetails){
								/* Setting inner menu */
								$strMenuArr['child'] .= '<li><a href="'.SITE_URL.$strModuleAccessArrValueDetails['module_url'].'">'.str_replace('[divider]','',$strModuleAccessArrValueDetails['description']).'</a></li>';
								/* if divider found then do needful */
								if(strstr($strModuleAccessArrValueDetails['description'],'[divider]')!=''){
									/* Setting inner menu */
									$strMenuArr['child'] .= '<li class="divider"></li>';
								}
							}
							$strMenuArr['child'] .= '</ul>';
						}
						/* Having child menu */
						if($blnHavingChild){
							/* Setting columns */
							$strMenuArr['main'] .= '<li><a class="dropdown-button" href="javascript:void(0);" data-activates="'.getSlugify(strtolower($strModuleAccessArrValue['description'])).'">'.$strModuleAccessArrValue['description'].'<i class="material-icons right">arrow_drop_down</i></a></li>';
						}else{
							/* Setting columns */
							$strMenuArr['main'] .= '<li><a class="dropdown-button" href="'.SITE_URL.$strModuleAccessArrValue['module_url'].'">'.$strModuleAccessArrValue['description'].'</li>';
						}
					}
				}
				
				$strMenuArr['main']	.= '</ul>';
				
				$strLoggerArr['main_menu'] = $strMenuArr['main'];
				$strLoggerArr['child_menu'] = $strMenuArr['child'];
				
				/* Removed used variables */
				unset($strMenuArr);
			}
			
			/* Creating location object */ 
			$locatioObj	= new Location($this->_objDefaultModel , $strLoggerArr['user_info']['company_code']);
				
			/* Getting the Branch and Region Assign to logger user */
			if($strLoggerArr['user_info']['is_admin'] == 1){
				/* Getting Branch and location */
				$strLocationArr	= $locatioObj->getLocationsByUserCode(-1);
			}else{
				/* Getting Branch and location */
				$strLocationArr	= $locatioObj->getLocationsByUserCode($strLoggerArr['user_info']['id']);
			}
			
			/* if user location details found then do needful */
			if(!empty($strLocationArr)){
				/* iterating the loop */
				foreach($strLocationArr as $strLocationArrKey => $strLocationArrValue){
					$strLoggerArr['region'][getEncyptionValue($strLocationArrValue['region_code'])]	= $strLocationArrValue['region_name'];
					$strLoggerArr['branch'][getEncyptionValue($strLocationArrValue['branch_code'])]	= $strLocationArrValue['branch_name'];
				}
			}
			/* Removed used variables */
			unset($locatioObj);
			
			
			/* Creating lead object */
			$leadObj 				= new Lead($this->_objDefaultModel , $strLoggerArr['user_info']['company_code']);
			/* Get lead attribute array */
			$strLeadArrtirbuteArr 	= $leadObj->getLeadAttributesListByCompnayCode();
			/* If lead attributes are not empty then do needful */
			if(!empty($strLeadArrtirbuteArr)){
				/* Iterating the loop */
				foreach($strLeadArrtirbuteArr as $strLeadArrtirbuteArrKey => $strLeadArrtirbuteArrValue){
					$strLoggerArr['leadAttr'][$strLeadArrtirbuteArrValue['attri_slug_key']] = array('label'=>$strLeadArrtirbuteArrValue['attri_slug_name'],'options'=>$strLeadArrtirbuteArrValue['attri_value_list'],'mandatory'=>$strLeadArrtirbuteArrValue['is_mandatory']);
				}
			}else{
				$strLoggerArr['leadAttr']	= array();
			}
			/* Removed used object */
			unset($leadObj, $strLeadArrtirbuteArr);
			
			
			/* Creating lead source object */
			$leadSourceObj 		= new leadSource($this->_objDefaultModel , $strLoggerArr['user_info']['company_code']);
			/* Get lead source array */
			$strLeadSourceArr 	= $leadSourceObj->getLeadSourceByCompanyCode();
			/* If lead source are not empty then do needful */
			if(!empty($strLeadSourceArr)){
				/* Iterating the loop */
				foreach($strLeadSourceArr as $strLeadSourceArrKey => $strLeadSourceArrValue){
					/* Settings lead source */
					$strLoggerArr['leadSource'][getEncyptionValue($strLeadArrtirbuteArrValue['id'])] = $strLeadSourceArrValue;
				}
			}else{
				$strLoggerArr['leadSource']	= array();
			}
			
			/* Removed used object */
			unset($leadSourceObj, $strLeadSourceArr);
			
			
			/* Creating lead status object */
			$leadStatusObj 		= new status($this->_objDefaultModel , $strLoggerArr['user_info']['company_code']);
			/* Get lead status array */
			$strLeadStatusArr 	= $leadStatusObj->getLeadStatusByCompanyCode();
			/* If lead status are not empty then do needful */
			if(!empty($strLeadStatusArr)){
				/* Iterating the loop */
				foreach($strLeadStatusArr as $strLeadStatusArrKey => $strLeadStatusArrValue){
					/* Settings lead source */
					$strLoggerArr['leadStatus'][getEncyptionValue($strLeadStatusArrValue['id'])] = $strLeadStatusArrValue;
				}
			}else{
				$strLoggerArr['leadStatus']	= array();
			}
			
			/* Removed used object */
			unset($leadStatusObj, $strLeadStatusArr);
			
		}
		
		

		/* Creating logger session string */
		$this->_strLoggerCode	= getRamdomeString(50);

		/* register the logger */
		$intRegisterCode 	= $this->_objDefaultModel->setDataInTable(array('table'=>$this->_strPrimaryTable,'data'=>array('user_code'=>$pIntUserCode,'token'=>$this->_strLoggerCode,'logger_data'=>json_encode($strLoggerArr),'logger_source'=>'a')));
		
		/* If logger successfully register in the database */
		if($intRegisterCode > 0){
			/* removed existing all cookies */
			$this->doDistryLoginCookie();
			/* Creating logger key */
			$this->_setLoginCookies();
		}else{
			/* Return error details */
			jsonReturn(array('status'=>0,'message'=>'Error occurred while generating the login instance to objects. please try after some time.'),true);
		}
	}

	/*******************************************************************/
	/*Purpose	: Setting login cookies.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	private function _setLoginCookies(){
		/* Setting logger cookie for 1 month */
		setcookie('_xAyBzCwD', $this->_strLoggerCode, time() + (2678400), "/");
	}

	/*******************************************************************/
	/*Purpose	: Removed login cookies.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function doDistryLoginCookie(){
		unset($_COOKIE['_xAyBzCwD']);
		setcookie('_xAyBzCwD', null, -1, '/');
	}
}
?>