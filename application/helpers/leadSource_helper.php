<?php 
/*******************************************************************************/
/* Purpose 		: Managing the lead source related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class leadSource{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_lead_source";
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
	/* Purpose	: get lead source roles by company code.
	/* Inputs 	: None.
	/* Returns	: lead source.
	/***************************************************************************/
	public function getLeadSourceByCompanyCode(){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
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
}