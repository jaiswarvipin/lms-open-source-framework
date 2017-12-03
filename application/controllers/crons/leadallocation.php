<?php
/***********************************************************************/
/* Purpose 		: Lead allocation automated script (CRON).
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadallocation extends Requestprocess {
	/* variable deceleration */
	private $_intFromDate			= 0;
	private $_intToDate				= 0;
	private $_isDebug				= false;
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* Setting operational date */
		$this->_intFromDate	= (isset($_REQUEST['fromdate']))?$_REQUEST['fromdate']:date('YmdHis', mktime(date('H'),date('i')-5,date('s'),date('m'),date('d'),date('Y')));
		$this->_intToDate	= (isset($_REQUEST['todate']))?$_REQUEST['todate']:date('YmdHis', mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')));
		
		/* Setting debug */
		$this->_isDebug			= (isset($_REQUEST['debug']))?true:false;
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		
		$this->_process();
	}
	
	/**********************************************************************/
	/*Purpose 	: process the data and create the array.
	/*Inputs	: none.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _process(){
		/* Variable initialization */
		$strStatusListArr	= $strMessageArr	= $strFilterArr	= array();
		
		/* if Debugging is set the do needful */
		if($this->_isDebug){
			debugVar('----------------Processing For Date ----------------');
			debugVar($this->_intFromDate);
			debugVar($this->_intToDate);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Fetch the in assigned .
	/*Inputs	: none.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
}