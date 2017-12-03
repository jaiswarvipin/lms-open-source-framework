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
	/* Purpose	: Setting the communication history details.
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
	
	
	/***************************************************************************/
	/* Purpose	: Get communication details by lead code
	/* Inputs 	: $pStrFilterArr	:: Communication filter array.
	/* Returns	: Communication details.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getCommuncationHistory($pStrFilterArr = array()){
		/* variable initialization */
		$strReturnArr		= array();
		$strQuery			= '';
		
		/* Communication history filter details empty then do needful */ 
		if(empty($pStrFilterArr)){
			/* Return task transaction status */
			return array();
		}
		/* Iterating the loop */
		for($intCounterForLoop = 11; $intCounterForLoop >= 0; $intCounterForLoop --){
			/* Creating the month name based on month counter */
			$strMonthName	= strtolower(date('M',mktime(date('H'),date('i'),date('s'),date('m')-$intCounterForLoop, date('d'), date('Y'))));
			/* Creating the communication history pulling history query */
			$strQuery	.= 'select id, lead_owner_code, status_code, comments, is_system, task_type_code, record_date from trans_communication_history_'.$strMonthName.'  where deleted = 0 and lead_code in('.implode(',',$pStrFilterArr).')';
			/* If not last counter then joining the query */
			if($intCounterForLoop != 0){
				/* Join the UNION */
				$strQuery	.= 'UNION ALL ';
			}else{
				/* Setting the Order by DESC */
				$strQuery	.= ' Order by 6 DESC ';
				
				/* default limit is not set then do needful */
				if(!isset($pStrFilterArr['limit'])){
					/* Setting limit */
					$strQuery	.= ' LIMIT 5 ';
				}
			}
		}
		/* Executing the created the query */
		$strReturnArr	= $this->_databaseObject->getDirectQueryResult($strQuery);
		/* Return the communication result array */
		return $strReturnArr;
	}
}
?>