<?php 
/*******************************************************************************/
/* Purpose 		: Managing the modules related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Module{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_modues";
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
	}
	
	/***************************************************************************/
	/* Purpose	: get module list by module code
	/* Inputs 	: pIntRoleCodeArr	:: module code array.
	/* Returns	: module array details.
	/***************************************************************************/
	public function getModulesByCode($pIntRoleCodeArr = array()){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
		/* if location code is passed then do needful */
		if(!empty($pIntRoleCodeArr)){
			/* Setting Location id as filter details */
			$strWhereArr	= array_merge($strWhereArr , array($this->_strTableName.'.id' => $pIntRoleCodeArr));
		}
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>$strWhereArr,
									'column'=>array('id', 'description','parent_code')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	
	/***************************************************************************/
	/* Purpose	: get module access role by roles code.
	/* Inputs 	: $pIntRoleCodeArr = Role code.
	/* Returns	: Role details.
	/***************************************************************************/
	public function getModulesByRoleCode($pIntRoleCodeArr = array()){
		/*If role code is not passed then do needful */
		if(empty($pIntRoleCodeArr)){
			/* return empty array set */
			return array ();
		}
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'trans_module_access',
									'where'=>array('role_code'=>$pIntRoleCodeArr,'company_code'=>$this->_intCompanyCode),
									'column'=>array('module_code','role_code')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
}