var blnFormValidationStatus = true;
var strDefaultErrormessage 	= 'Error occured while processing the request, kindly try after some time.';
var strSecurityErrormessage = 'It\'s looks like your are system data got modified, kindly try after some time.';
var strNoAccessMessage 		= 'Apologies, you are not authorized to do same action.\n\nKindly get tuch with system administrator on "Access" issue.';
var strRowNotActive 		= 'Requested row is not mark active.\n\nPlease tick the checkbox to make to active.';
var objRef					= '';
var intTimeToShowMessage	= 2000;
var strReportType			= {};
var statusUpdateValue		= false;
var strWidgetFormObjectRef	= '';
var objectRefrence			= null;
/**************************************************************************
 Purpose 		: Checking for form object.
 Inputs  		: pObjectRefrence :: Form element object,
 pDataValidationType :: element validation type.
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function checkFormValidation(pObjectRefrence, pDataValidationType) {	
	var strErrorMessage = $ (pObjectRefrence).find('span.valid_error').html();
	var alpha = /^([a-zA-Z]+\s?)*$/;
	var emailAlpha = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;
	var contactAlpha = /^(([0-9]|[\+])?)[0-9\-\(\)]+$/;
	var strValue = $(pObjectRefrence).val();
	/* console.log(strValue + ' :: '+pObjectRefrence.attr('id')); */
	if (($(pObjectRefrence).is("input")) && ($(pObjectRefrence).attr('type') =='checkbox')) {
		if(!$(pObjectRefrence).is(':checked')){
			flag2 = false;
		}
		
		if (!flag2) {
			$(pObjectRefrence).parent().find('.valid-error').show();			
		} else {
			$(pObjectRefrence).parent().find('.valid-error').hide();
		}
	}else if (($(pObjectRefrence).is("input")) && ($(pObjectRefrence).attr('type')!='hidden')) {
		if (strValue == "" || strValue == null) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('valid', 'your');
			flag2 = false;
		/*} else if ((!alpha.test(strValue) && (pDataValidationType == 'alpha'))) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('your', 'valid');
			flag2 = false;*/
		} else if ((!emailAlpha.test(strValue) && (pDataValidationType == 'email'))) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('your', 'valid');
			flag2 = false;
		} else if ((!contactAlpha.test(strValue) && (pDataValidationType == 'contact-number'))) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('your', 'valid');
			flag2 = false;
		}else if ((isNaN(strValue) && (pDataValidationType == 'float'))) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('your', 'valid');
			flag2 = false;
		}else if (pDataValidationType == 'valid-ext') {
			var strValidExtentionValue = $(pObjectRefrence).attr('valid-ext');
			strValidExtentionValue = strValidExtentionValue.split(',');
			var strFileExtention = strValue.split('.');
			var strFileExtentionValue = strFileExtention[strFileExtention.length - 1];				 
			var blnExt = false;
			
			for(var intCounterForLoop = 0; intCounterForLoop < strValidExtentionValue.length; intCounterForLoop++){
				if(strFileExtentionValue.toLowerCase() == strValidExtentionValue[intCounterForLoop]){
					blnExt=true;
				}
			}
			flag2 = blnExt;
		}		
		if (!flag2) {
			$(pObjectRefrence).parent().find('.valid-error').show();			
		} else {
			$(pObjectRefrence).parent().find('.valid-error').hide();
		}
	} else if (($(pObjectRefrence).is("textarea")) && ($(pObjectRefrence).is("file"))) {
		if (strValue == "" || strValue == null) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('valid', 'your');
			flag2 = false;
		}

		if (!flag2) {
			$(pObjectRefrence).parent().find('.valid-error').show();
		} else {
			$(pObjectRefrence).parent().find('.valid-error').hide();
		}
	} else {
		if (strValue == "" || strValue == null) {
			$(pObjectRefrence).parent().find('.valid-error').html().replace('valid', 'your');
			flag2 = false;
		}  

		if (!flag2) {
			$(pObjectRefrence).parent().find('.valid-error').show();
		} else {
			$(pObjectRefrence).parent().find('.valid-error').hide();
		}
	}
	return flag2;
}

/**************************************************************************
 Purpose 		:First get all elements of form and then depending in validation 
				 request, validating the same.
 Inputs  		: formObject :: Form object.
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
 /**************************************************************************/
function validateFormElement(formObject) {
	var blnVariable = true;
	intErrorCounter = 0;
	
	if ($('#' + formObject).length == 1) {
		$('#' + formObject).find('input').each(function() {
			
			flag2	= true;
			if ($(this).attr('required')) {
				$strObjectDataValidationType = '';
				if ($(this).attr('email')) {
					$strObjectDataValidationType = 'email';
				}

				if ($(this).attr('contact-number')) {
					$strObjectDataValidationType = 'contact-number';
				}

				if ($(this).attr('alpha')) {
					$strObjectDataValidationType = 'alpha';
				}
				
				if ($(this).attr('float')) {
					$strObjectDataValidationType = 'float';
				}
				
				if ($(this).attr('valid-ext')) {
					$strObjectDataValidationType = 'valid-ext';
				}

				
				blnVariable = checkFormValidation($(this), $strObjectDataValidationType);					
				if(!blnVariable){
					intErrorCounter++;
				}
				if ((blnFormValidationStatus) && (!blnVariable)) {
					blnFormValidationStatus = false;
				}
			}
		});

		$('#' + formObject).find('select').each(function() {
			flag2	= true;
			if ($(this).attr('required')) {
				blnVariable = checkFormValidation($(this), '', '');					
				if(!blnVariable){
					intErrorCounter++;
				}
				if ((blnFormValidationStatus) && (!blnVariable)) {
					blnFormValidationStatus = false;
				}
			}
		});

		$('#' + formObject).find('textarea').each(function() {
			flag2	= true;
			if ($(this).attr('required')) {
				blnVariable = checkFormValidation($(this), '', '');
				if(!blnVariable){
					intErrorCounter++;
				}
				if ((blnFormValidationStatus) && (!blnVariable)) {						
					blnFormValidationStatus = false;
				}
			}
		});
	}
	if(!blnFormValidationStatus && parseInt(intErrorCounter) > 0){
		blnVariable = blnFormValidationStatus;
	}
	return blnVariable;
}


/**************************************************************************
 Purpose 		: Submitting the user request using ajax operation.
 Inputs  		: strformName :: Form Name.
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function postUserRequest(strformName){
	var data_string 	= $('form#'+strformName).serialize();
	var strDestionPath	= $('#'+strformName).attr('action');
	var strReturnObject	= '';
	
	//$.fancybox.showLoading()
	$.ajax({
        type: "POST",
        url: strDestionPath,
        data: data_string,
        success: function(output) {
			processRequestAfterResponse(strformName, output);			
        }
	});
}

/**************************************************************************
 Purpose 		: Submitting the user request using ajax operation without Form Object.
 Inputs  		: strVirtualFormaName :: Virtual Form name,
				: strDataSet :: JSON formated data set,
				: strAction :: Target endpoints.
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function postUserRequestVirualForm(strVirtualFormaName, strDataSet, strAction){
	$.ajax({
        type: "POST",
        url: strAction,
        data: strDataSet,
        success: function(output) {
			processRequestAfterResponse(strVirtualFormaName, output);			
        }
	});
}

/**************************************************************************
 Purpose 		: Submitting the user document request using ajax operation.
 Inputs  		: strformName :: Form Name.
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function postUserDocumentRequest(strformName){
	var data_string = new FormData($('form#'+strformName)[0]);
	var strDestionPath	= $('#'+strformName).attr('action');
	var strReturnObject	= '';
	 
	$.ajax({
        type: "POST",
		async: false,
		cache: false,
		contentType: false,
		processData: false,
        url: strDestionPath,
        data: data_string,
        success: function(output) {
			processRequestAfterResponse(strformName, output);			
        }
	});
}

/**************************************************************************
 * Purpose	:	Checking report form object is contains value else 
				display respective element as error.
 * Inputs	:	pFormObjectRefrence :: form object reference .
 * Return	:	None.
 * Created by:	Jaiswar Vipin Kumar R.
****************************************************************************/
function setErrorObject(pFormObjectRefrence){
	var blnReturnValue	= true;
	$('#'+pFormObjectRefrence).find('input').each(function(){
		$(this).removeClass('input-text-error');
		if($.trim($(this).val()) == ''){
			$(this).addClass('input-text-error');
			blnReturnValue	= false;
		}
	});
	return blnReturnValue;
}

/**************************************************************************
 * Purpose	:	Displaying error meessge.
 * Inputs	:	pStrMessage :: Message need to get displayed.
 * Return	:	None.
 * Created by:	Jaiswar Vipin Kumar R.
****************************************************************************/
function showToast(pStrMessage){
	Materialize.toast(pStrMessage, intTimeToShowMessage);
}

/**************************************************************************
 * Purpose	:	Removed all message.
 * Inputs	:	None.
 * Return	:	None.
 * Created by:	Jaiswar Vipin Kumar R.
****************************************************************************/
function clearAllToast(){
	Materialize.Toast.removeAll();
}

/**************************************************************************
 * Purpose	:	Show Loader.
 * Inputs	:	None.
 * Return	:	None.
 * Created by:	Jaiswar Vipin Kumar R.
****************************************************************************/
function showLoader(){
	$.fancybox.showLoading();
}

/**************************************************************************
 * Purpose	:	Hide Loader.
 * Inputs	:	None.
 * Return	:	None.
 * Created by:	Jaiswar Vipin Kumar R.
****************************************************************************/
function hideLoader(){
	$.fancybox.hideLoading();
}