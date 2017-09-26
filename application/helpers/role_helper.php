<?php 
/*******************************************************************************/
/* Purpose 		: Managing the Company Custom and System Role related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Role{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_role";
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
	/* Purpose	: get custom roles by filer
	/* Inputs 	: pIntRoleCodeArr	:: Role code array.
	/* Returns	: Role details.
	/***************************************************************************/
	public function getCustomRoleDetails($pIntRoleCodeArr = array()){
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
									'column'=>array('id', 'description')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: get system roles by filer
	/* Inputs 	: None.
	/* Returns	: Role details.
	/***************************************************************************/
	public function getSystemRoleDetails(){
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>'master_system_roles',
									'where'=>array(),
									'column'=>array('id', 'description')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
}