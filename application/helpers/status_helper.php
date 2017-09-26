<?php 
/*******************************************************************************/
/* Purpose 		: Managing the status related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class status{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_status";
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
	/* Purpose	: get status by company code.
	/* Inputs 	: None.
	/* Returns	: Status details
	/* Returns	: Lead status.
	/***************************************************************************/
	public function getLeadStatusByCompanyCode(){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>$strWhereArr,
									'column'=>array('id', 'description','parent_id')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: Get default status of requested company.
	/* Inputs 	: None.
	/* Returns	: Default status details.
	/* Returns	: Default Lead status.
	/***************************************************************************/
	public function getDefaultLeadStatusDetilsByCompanyCode(){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode,'is_default'=>1);
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>$strWhereArr,
									'column'=>array('id', 'description','parent_id')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
}