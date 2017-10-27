<?php 
/***********************************************************************/
/* Purpose 		: Managing the form related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Form{
	/**********************************************************************/
	/*Purpose 	: Creating Drop Down.
	/*Inputs	: $pStrDropDownElementArr :: drop down elements,
				: $pStrSelecedValue :: Default select options value,
				: $pBlnSelectOne :: select one.
	/*Returns 	: Drop Down HTML.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	function getDropDown($pStrDropDownElementArr = array(), $pStrSelecedValue = '', $pBlnSelectOne = true){
		/* Variable initialization */
		if($pBlnSelectOne){
			/* Select One */
			$strDropDownHTML	= '<option value="">[Select One]</option>';
		}else{
			/* Select All */
			$strDropDownHTML	= '<option value="-1">[Select All]</option>';
		}
		/* if empty record set found then do needful; */
		if(empty($pStrDropDownElementArr)){
			/* return the empty HTML string with default option */
			return $strDropDownHTML;
		}

		/* Iterating the loop */
		foreach($pStrDropDownElementArr as $pStrDropDownElementArrKey => $pStrDropDownElementArrValue){
			/* Checking is its sub array */
			if(is_array($pStrDropDownElementArrValue)){
				/* Checking child */
				if(isset($pStrDropDownElementArrValue['child'])){
					/* Setting Option Group */
					$strDropDownHTML .= '<optgroup label="'.$pStrDropDownElementArrValue['name']['name'].'" values="'.$pStrDropDownElementArrValue['name']['value'].'">';
				
					/* Iterating the loop */
					foreach($pStrDropDownElementArrValue['child'] as $pStrDropDownElementArrValueKey => $pStrDropDownElementArrValueDetails){
						/* Setting the option array */
						$strDropDownHTML .= '<option value="'.$pStrDropDownElementArrValueKey.'">'.$pStrDropDownElementArrValueDetails.'</option>';
					}
				}
				
				/* Setting Option Group */
				$strDropDownHTML .= '</optgroup>';
			}else{
				/* variable initialization */
				$strSelected	= '';
				/* if requested selected value is same as current value then do needful */
				if($pStrDropDownElementArrKey == $pStrSelecedValue){
					/* Value overriding */
					$strSelected	= 'selected="selected"';
				}
				/* Setting the option array */
				$strDropDownHTML .= '<option value="'.getEncyptionValue($pStrDropDownElementArrKey).'" '.$strSelected.'>'.$pStrDropDownElementArrValue.'</option>';
			}
		}
		/* Return Select HTML string */
		return $strDropDownHTML;
	}	
}