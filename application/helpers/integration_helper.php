<?php 
/*******************************************************************************/
/* Purpose 		: Managing the Company Location related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Integration{
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/***************************************************************************/
	public function __construct(){
		debugVar($_SERVER , true);
	}
}