<?php
/***********************************************************************/
/* Purpose 		: Authentication of user.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	/* variable decelarition */
	private $_strPrimaryTableName	= 'master_user';

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initilaization */
		$dataArr	= array();

		/* Load the login */
		$dataArr['body']	= $this->load->view('auth/login', array(), true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
}