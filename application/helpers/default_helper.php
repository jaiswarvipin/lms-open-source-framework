<?php 
/**********************************************************************/
/*Purpose 	: Print requested array / string on console.
/*Inputs	: $pStrDataArr :: requested array / string,
			: $pBlnExit	:: Terminate the execution.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function debugVar($pStrDataArr = array(), $pBlnExit = false){
	echo '<pre>';
	print_r($pStrDataArr);
	echo '</pre>';
	/* if termination request then do needful */
	if($pBlnExit){
		exit;
	}
}

/**********************************************************************/
/*Purpose 	: convert the array in JSON and response to caller.
/*Inputs	: $pStrDataArr :: requested array / string,
			: $pBlnExit	:: Terminate the execution.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function jsonReturn($pStrDataArr = array(), $pBlnExit = false){
	/* if termination request then do needful */
	if($pBlnExit){
		/* convert array in json format */
		die(json_encode($pStrDataArr));
	}else{
		/* return the JSON encoded string to caller */
		return json_encode($pStrDataArr);
	}
}

/**********************************************************************/
/*Purpose 	: Return the loader.
/*Inputs	: None.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getLoaderHTML(){
	/* return the loader HTML */
	return '<div class="preloader-wrapper small right hide">
              <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                  <div class="circle"></div>
                </div>
                <div class="gap-patch">
                  <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                  <div class="circle"></div>
                </div>
              </div>
            </div>';
}

/**********************************************************************/
/*Purpose 	: Return the loader.
/*Inputs	: None.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDeleteConfirmation($pStrAction){
	/* return the loader HTML */
	return '<div id="deleteModel" class="modal modal-fixed-footer" style="height: 30% !important;">
    			<div class="modal-content">
      				<h4>Delete Conformation!!!</h4>
     	 			<p>Are you sure, you want to deleted selecetd record ?</p>
     	 			<form method="post" action="'.$pStrAction.'" name="frmDelete" id="frmDelete">
     	 				<input type="hidden" name="txtDeleteRecordCode" id="txtDeleteRecordCode" value="" />
     	 			</form>
			    </div>
			    <div class="modal-footer">
			    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
			    	<button class="btn waves-effect waves-light cmdDeleteRecord" type="submit" name="cmdDeleteRecord" id="cmdStatusManagment" formName="frmDelete" >Delete<i class="material-icons right">delete</i></button>
			    </div>
			</div>';
}

/**********************************************************************/
/*Purpose 	: Get Edit form for getting the requested details.
/*Inputs	: $pStrAction :: get data by code URL.
/*Returns 	: Edit Form HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getEditContentForm($pStrAction){
	return '<form method="post" action="'.$pStrAction.'" name="frmGetDataByCode" id="frmGetDataByCode">
 				<input type="hidden" name="txtCode" id="txtCode" value="" />
 			</form>';
}

/**********************************************************************/
/*Purpose 	: Get data after filter.
/*Inputs	: $pStrAction :: get data by code URL,
			: $pstrFormName :: Form name.
/*Returns 	: Edit Form HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getFormStrecture($pStrAction, $pstrFormName){
	return '<form method="post" action="'.$pStrAction.'" name="'.$pstrFormName.'" id="'.$pstrFormName.'"></form>';
}

/**********************************************************************/
/*Purpose 	: Return the No Record HTML found.
/*Inputs	: $intNumberOfColSpan	= Number of column span.
/*Returns 	: No record HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getNoRecordFoundTemplate($intNumberOfColSpan = 1){
	return '<tr><td colspan="'.$intNumberOfColSpan.'" class="center">No Record Found.</td></tr>';
}

/**********************************************************************/
/*Purpose 	: Generating random string.
/*Inputs	: $pIntLength :: String length.
/*Returns 	: Random string.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getRamdomeString($pIntLength = 10){
	/* variable initialization */
	$strCharactersSet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	/* getting character set length */
    $intCharactersLength = strlen($strCharactersSet);
    /* variable initialization */
    $strRandomString = '';
    /* Iterating the loop for requested number of time */
    for ($intCounterForLoop = 0; $intCounterForLoop < $pIntLength; $intCounterForLoop++) {
    	/* String creating */
        $strRandomString .= $strCharactersSet[rand(0, $intCharactersLength - 1)];
    }
    /*return the string */
    return $strRandomString;
}

/**********************************************************************/
/*Purpose 	: Generating the array by key value pair.
/*Inputs	: $pstrDataArr :: data array,
			: $pStrKey :: Key value,
			: $pStrValue :: Value.
/*Returns 	: Array.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getArrByKeyvaluePairs($pstrDataArr = array(), $pStrKey, $pStrValue){
	/* variable initialization */
	$strReturnArr	= array();

	/* if data set is empty then do needful */
	if(empty($pstrDataArr)){
		/* Return empty array */
		return $strReturnArr;
	}

	/* Checking shared key value exists */
	if((!isset($pstrDataArr[0][$pStrKey])) || (!isset($pstrDataArr[0][$pStrValue]))){
		/* Return empty array */
		return $strReturnArr;	
	}

	/* Iterating the loop */
	foreach($pstrDataArr as $pStrDataArrKey => $pStrDataArrValue){
		/* Setting values */
		$strReturnArr[$pStrDataArrValue[$pStrKey]]	= $pStrDataArrValue[$pStrValue];
	}
    /*return the string */
    return $strReturnArr;
}

/*************************************************************************
/*Purpose	: Generating the pagination HTML.
/*Input		: $pIntNumberOfPecordsArr :: Number of records,
			: $pIntCurrentPageNumber :: Current page number,
			: $strFormName :: From name.
/*Returns	: Pagination HTML
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getPagniation($pIntNumberOfPecordsArr = array(), $pIntCurrentPageNumber = 1, $strFormName = ''){
	/* Number of records */
	$intNumberofRecords	= (!empty($pIntNumberOfPecordsArr) && isset($pIntNumberOfPecordsArr[0]['recordCount']))?$pIntNumberOfPecordsArr[0]['recordCount']:0;
	
	/* Setting number of pages */
	$intNumberOfpages	= ceil($intNumberofRecords / DEFAULT_RECORDS_ON_PER_PAGE);
	
	$strDefaultPageClass	= 'disabled';
	//
	/* Variable initialization */
	$strPagnationHTML	= '<ul class="pagination right">
							<li class="active green"><a>Total Records : '.$intNumberofRecords.'</a></li>';
	
	/* if user on second page then do needful */
	if($pIntCurrentPageNumber > 1){
		/* Get previous page number */
		$intPreviousPageNumber	= $pIntCurrentPageNumber - 1;
		/* Checking previous page should not less then first page */ 
		if($intPreviousPageNumber < 1){
			/* Set first page a default */
			$intPreviousPageNumber	= 1;
		}
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage(1,\''.$strFormName.'\');"><i class="material-icons">first_page</i></a></li>
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intPreviousPageNumber.',\''.$strFormName.'\');"><i class="material-icons">chevron_left</i></a></li>
								';
	}else{
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">first_page</i></a></li>
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">chevron_left</i></a></li>
								';
	}
	
	/* If Number of pages found then show input box */
	if($intNumberOfpages > 0){
		$strPagnationHTML	.= '<li class="active"><input type="text" style="width: 20px;" value="'.$pIntCurrentPageNumber.'" /></li>';
	}else{
		/* Show page number label */ 
		$strPagnationHTML	.= '<li class="active">'.$pIntCurrentPageNumber.'</li>';
	}
	
	$strPagnationHTML	.= '<li class="active"><a href="javascript:void(0);">of '.$intNumberOfpages.'</a></li>';
	
	/* if user on second page then do needful */
	if(($pIntCurrentPageNumber >= 1) && ( $pIntCurrentPageNumber < $intNumberOfpages )) {
		/* Get next page number */
		$intNextPageNumber	= $pIntCurrentPageNumber + 1;
		/* Checking previous page should not less then first page */ 
		if($intNextPageNumber > $intNumberOfpages){
			/* Set first page a default */
			$intNextPageNumber	= $intNumberOfpages;
		}
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intNextPageNumber.',\''.$strFormName.'\');"><i class="material-icons">chevron_right</i></a></li>
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intNumberOfpages.',\''.$strFormName.'\');"><i class="material-icons">last_page</i></a></li>
								';
	}else{
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">chevron_right</i></a></li>
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">last_page</i></a></li>
								';
	}
							
	$strPagnationHTML	.= '</ul>';
	
	/* Return Pagination */
	return $strPagnationHTML;
}

/*************************************************************************
/*Purpose	: Getting encrypted value of human readable value.
/*Input		: $strValue :: Value.
/*Returns	: Encryption value
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getEncyptionValue($strValue){
	/* Encrypting the string */
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(TOKEN), $strValue, MCRYPT_MODE_CBC, md5(md5(TOKEN))));
}

/*************************************************************************
/*Purpose	: Getting decrypted value of human readable value.
/*Input		: $strValue :: Value.
/*Returns	: De-Encryption value
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getDecyptionValue($strValue){
	/* Encrypting the string */
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(TOKEN), base64_decode($strValue), MCRYPT_MODE_CBC, md5(md5(TOKEN))), "\0");
}

/*************************************************************************
/*Purpose	: identifying is request is AJAX /POST/ GET type.
/*Input		: None
/*Returns	: tTRUE / FALSE
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function isAjaxRequest(){
	/* checking is request is ajax */
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){    
		/* return confirmation */
		return true;
	}
	/* return confirmation */
	return false;
}

/*************************************************************************
/*Purpose	: Return Yes / No.
/*Input		: $pIntValue :: Value
/*Returns	: Yes / No
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getYesNo($pIntValue = 0){
	if($pIntValue == 1){
		return 'Yes';
	}else{
		return 'No';
	}
}
/*************************************************************************
/*Purpose	: Generating SLUG.
/*Input		: pStrNormaString :: Normal String
/*Returns	: Slug
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getSlugify($pStrNormaString){
	/* replace non letter or digits by - */
	$pStrNormaString = preg_replace('~[^\pL\d]+~u', '-', $pStrNormaString);
	/* transliterate */
	$pStrNormaString = iconv('utf-8', 'us-ascii//TRANSLIT', $pStrNormaString);
	/* remove unwanted characters */
	$pStrNormaString = preg_replace('~[^-\w]+~', '', $pStrNormaString);
	/* trim */
	$pStrNormaString = trim($pStrNormaString, '_');
	/* Lower case */
	$pStrNormaString = strtolower($pStrNormaString);
	/* If string is empty then do needful */
	if (empty($pStrNormaString)) {
		return '';
	}
	/* Return String */
	return $pStrNormaString;
}

/**********************************************************************/
/*Purpose 	: Getting date format.
/*Inputs	: $pIntDate :: Date,
			: $intDateFormat	:: Date format type.
/*Returns 	: Format Date .
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDateFormat($pIntDate = 0, $intDateFormat = 0){
	/*IF date is not passed then do needful */
	if($pIntDate == 0){
		/* Return empty */
		return '-';
	}
	
	/* Return date format type */
	switch($intDateFormat){
		default:
			return date('d M Y<\b\\r>H:i:s',strtotime($pIntDate));
			break;
		/* Get database insertion format date and time  */
		case 1:
			return str_replace(array('/',':'),array('',''),$pIntDate);
			break;
		case 2:
			return date('d M Y',strtotime($pIntDate));
			break;
	}
}

/**********************************************************************/
/*Purpose 	: Decoding the array key to normal value.
/*Inputs	: $pStrValueSetArr	:: Value array
			: $isValueDecode : value need to decode.
/*Returns 	: Decoded array.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function decodeKeyValueArr($pStrValueSetArr = array(), $isValueDecode = false){
	/* Variable initialization */
	$strReturnArr	= array();
	
	/* if empty array found then do needful */
	if(empty($pStrValueSetArr)){
		/* return array */
		return $strReturnArr;
	}
	/* Iterating the loop */
	foreach($pStrValueSetArr as $pStrValueSetArrKey => $pStrValueSetArrValue){
		/* if value needs to decode then do needful */
		if($isValueDecode){
			/* Setting the value */
			$strReturnArr[$pStrValueSetArrKey]	= getDecyptionValue($pStrValueSetArrValue);
		}else{
			/* Setting the value */
			$strReturnArr[getDecyptionValue($pStrValueSetArrKey)]	= $pStrValueSetArrValue;
		}
	}
	/* return array */
	return $strReturnArr;
}


/**********************************************************************/
/*Purpose 	: Number formating.
/*Inputs	: $pNumber	:: Value,
			: $pIntFormatingType :: Formating type.
/*Returns 	: Formated number.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function numberFormating($pNumber = 0, $pIntFormatingType = 0){
	/* Checking passed value is not number */
	if(!is_numeric($pNumber)){
		/* setting default value */
		$pNumber	= 0;
	}
	
	/* based on the formating type doing processing */
	switch($pIntFormatingType){
		case 0:
		default:
			$pNumber	= number_format($pNumber, 2, '.','');
			break;
	}
	
	/* return formatted number */
	return $pNumber;
}