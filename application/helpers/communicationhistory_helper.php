<?php 
/*******************************************************************************/
/* Purpose 		: Managing the communication history related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class communicationhistory{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "trans_communication_history_";
	private $_strBranchCodeArr	= array();
	
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
		/* Setting communication table reference */
		$this->_strTableName	= $this->_strTableName.strtolower(date('M'));
	}
	
	/***************************************************************************/
	/* Purpose	: Get default task type filter by company code
	/* Inputs 	: $pStrCommuncationArr	:: Communication array.
	/* Returns	: Communication code.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function setCommuncationHistory($pStrCommuncationArr = array()){
		/* variable initialization */
		$intTransStatus		= 0;
		
		/* Communication history details empty then do needful */ 
		if(empty($pStrCommuncationArr)){
			/* Return task transaction status */
			return $intTransStatus;
		}
		
		/* Setting communication history */
		$intTransStatus	= $this->_databaseObject->setDataInTable(
																	array(
																		'table'=>$this->_strTableName,
																		'data'=>$pStrCommuncationArr
																	)
																);
		
		/* Return task transaction status */
		return $intTransStatus;
	}
}
?>