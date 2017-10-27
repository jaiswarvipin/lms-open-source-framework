/**************************************************************************
 Purpose 		: Processing customer request after system response.
 Inputs  		: pStrFormName :: Form Name.
				: pStrResponse :: System response object
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function processRequestAfterResponse(pStrFormName, pStrResponseObject){
	/* JONE decoding the response Arr */
	var objResponse = jQuery.parseJSON(pStrResponseObject);

	if(objResponse.status == 0){
		if($('.addItemInModule').length == 0){
			$('#'+objectRefrence).find('input').each(function(){
				if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
					$(this).removeAttr('checked');
				}
			});
		}
		
		if(objectRefrence == 'divFieldMapping'){
			$('#tblLeadAttribute').html('');
		}		
		showToast(objResponse.message);
	}else{
		$.fancybox.hideLoading();
		switch(pStrFormName){		
			case 'frmAuthencation':
						
				break;
			case 'frmCompanyRegistration':
				
				break;
			case 'frmStatusClassification':
				/* Variable Initialization */
				var intResponseStatusParentCode = objResponse.isopen;
				/* Checking for open repose status */
				if(intResponseStatusParentCode == 1){
					$('.hideOnCloseStatus').show();
				}else{
					$('.hideOnCloseStatus').hide();
				}
				return;
				break;
			case 'frmDynamicEventDataSet':
				$('#'+objectRefrence).find("option").hide();
				
				var strReturnArr	= jQuery.parseJSON(objResponse.message);
				$.each(strReturnArr, function(strKeyColumn, strColumnValue){
					$('#'+objectRefrence).children('option[value="'+strColumnValue+'"]').show();
				});
				$('#'+objectRefrence).material_select();
				return false;
				
				break;
			case 'frmLeadProfileDetails':
				$.each(objResponse.message, function(strKeyColumn, strColumnValue){
					$.each(strColumnValue, function(strColumnValuekey , strColumnValueDetails){
						/* Search the lead attribute and set the value */
						if($('#txtProfile'+strColumnValuekey).length == 1){
							/* Setting the value */
							$('#txtProfile'+strColumnValuekey).val(strColumnValueDetails);
						}else if(strColumnValuekey == 'strHistory'){
							$('#divCommuncationHistoryContrainer').html(strColumnValueDetails);
						}
					});
				});
				return false;
				break;
			case 'frmGetDataByCode':
				if(objResponse.message){
					showToast(objResponse.message);
				}else{
					var blnUserProfile	= false;
					if(objectRefrence == 'userProfileModel'){
						blnUserProfile = true;
					}
					
					if(objectRefrence == 'divFieldMapping'){
						$('#tblLeadAttribute').html('');
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#tblLeadAttribute').append(addFieldRowInTable(strColumnValue.attri_code, strColumnValue.attri_slug_name));
							$('#cboLeadAttributeCode').find('option[value="'+strColumnValue.attri_code+'"]').remove();
						});
						$('#cboLeadAttributeCode').material_select();
					}
					
					if($('.addItemInModule').length == 0){
						$('#'+objectRefrence).find('input').each(function(){
							if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
								$(this).removeAttr('checked');
							}
						});
						
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$.each(strColumnValue, function(strColumnValueKey, strColumnValueArr){
								$('#'+objectRefrence).find('input').each(function(){
									if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
										if(($(this).attr('data-set') == strColumnValueKey) && ($(this).val() == strColumnValueArr)){
											$(this).attr('checked','checked');
										}
									}
								});
							});
						});
						return false;
					}
					
					if($('#'+objectRefrence).find(':input').length > 0){
						$('#'+objectRefrence).find(':input:enabled:visible:first').focus();
						
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('input').each(function(){
								if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
									console.log($(this).attr('data-set') + ' -> ' + strKeyColumn);
									if(($(this).attr('data-set') == strKeyColumn) && ($(this).val() == strColumnValue)){
										$(this).attr('checked','checked');
									}
								}else{
									if(blnUserProfile){
										if((strKeyColumn == '0')){
											var strObjectRefrence	= $(this);
											$.each(strColumnValue, function(strColumnValueKey, strColumnValueDetails){
												if($(strObjectRefrence).attr('data-set') == strColumnValueKey){
													$(strObjectRefrence).val(strColumnValueDetails);
												}
											});
										}
									}else{
										if($(this).attr('data-set') == strKeyColumn){
											$(this).val(strColumnValue);
										}
									}
								}
							});
						});
					}
					
					if($('#'+objectRefrence).find('select').length > 0){
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('select').each(function(){
								if((strKeyColumn == '0')){
									if(blnUserProfile){
										var strObjectRefrence	= $(this);
										$.each(strColumnValue, function(strColumnValueKey, strColumnValueDetails){
											if($(strObjectRefrence).attr('data-set') == strColumnValueKey){
												$(strObjectRefrence).val(strColumnValueDetails);
												$(strObjectRefrence).material_select();
											}
										});
									}
								}else if($(this).attr('data-set') == strKeyColumn){
									if((strKeyColumn == 'zone') || (strKeyColumn == 'region') || (strKeyColumn == 'city') || (strKeyColumn == 'area') || (strKeyColumn == 'branch')){
										$(this).html(strColumnValue);
									}else{
										$(this).val(strColumnValue);
									}
									$(this).material_select();
								}
							});
						});
					}
				}
				return false;
				break;
			case 'frmCustom':
				$.each(objResponse, function(strKeyColumn, strColumnValue){
					if(strKeyColumn == 'dataset'){
						if(objectRefrence != ''){
							$('#'+objectRefrence).html(strColumnValue).material_select();
						}
					}else if(strKeyColumn == 'reporting'){
						$('#cboReportingManager').html(strColumnValue).material_select();
					}
				});
				return false;
				break;
		}
		
		showToast(objResponse.message);

		/* If redirection URL is set then do needful */
		if (typeof objResponse.destinationURL != typeof undefined && objResponse.destinationURL != false && objResponse.destinationURL != '') {
			setTimeout(function(){
				/* Redirecting the URL */
				window.location.href =  objResponse.destinationURL;
			},intTimeToShowMessage)
		}else{
			setTimeout(function(){
				/* Redirecting the URL */
				window.location.reload();
			},intTimeToShowMessage)
		}
	}

	hideLoader();
	
	return false;
}

/**************************************************************************
 Purpose 		: Getting result set of requested page number.
 Inputs  		: intpageNumber :: page Number,
				: fromObject :: From Object
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function goToPage(intpageNumber, fromObject){
	if(isNaN(intpageNumber)){
		showToast('Invalid page number request.');
	}else{
		$('#frmModuleSearch').html('');
		$('#frmModuleSearch').append('<input type="hidden" name="txtPageNumber" id="txtPageNumber" value="'+intpageNumber+'" />');
		
		if($('#'+fromObject).length > 0){
			$('#'+fromObject).find('input').each(function(){
				if((($(this).attr('type') == 'checkbox') || ($(this).attr('type') == 'radio')) && ($(this).attr('checked'))){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}else if((($(this).attr('type') != 'checkbox') && ($(this).attr('type') != 'radio'))){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}
			});
			
			$('#'+fromObject).find('select').each(function(){
				$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
			});
		}
		
		$('#frmModuleSearch').submit();
	}
}

/**************************************************************************
 Purpose 		: Initialization.
 Inputs  		: None.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function init(){
	$('.modal').modal();
	$('select').material_select();
	$('checkbox').material_select();
	$('ul.tabs').tabs();
	$('input#input_text, textarea#textarea1').characterCounter();
	
	$('.datepicker').pickadate({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 15, // Creates a dropdown of 15 years to control year,
		today: 'Today',
		clear: 'Clear',
		close: 'Ok',
		format:'yyyy/mm/dd',
		closeOnSelect: false // Close upon selecting a date,
	});
	
	$('.timepicker').pickatime({
		default: 'now', // Set default time: 'now', '1:30AM', '16:30'
		fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
		twelvehour: false, // Use AM/PM or 24-hour format
		donetext: 'OK', // text for done-button
		cleartext: 'Clear', // text for clear-button
		canceltext: 'Cancel', // Text for cancel-button
		autoclose: false, // automatic close timepicker
		ampmclickable: true, // make AM PM clickable
		aftershow: function(){} //Function for after opening timepicker
	});
	
	/* Register the events */
	setPullDownEvents();
}

/**************************************************************************
 Purpose 		: Opening the model in edit case.
 Inputs  		: pModelRefenceObject :: model reference object.,
				: pIntRecordCode :: Record then needs to be edit,
				: isEdit :: is edit request.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R.
/**************************************************************************/
function openEditModel(pModelRefenceObject, pIntRecordCode, isEdit){
	var objFrom		= $('#'+$('#'+pModelRefenceObject).find('form').attr('id'));
	
	/* Mass updated */
	if(pIntRecordCode == 'selected'){	
		/* Iterating the code */
		if($('input[name="chkLeadCode[]"]:checked').length  == 0){
			showToast('Atleast one lead should selected.');
		}else{
			var strleadArr	= strLeadCode	= strLeadOwnerCode = '';
			
			$('input[name="chkLeadCode[]"]').each(function (){
				var strleadArr	= $(this).val().split(DELIMITER);
				if(strLeadCode == ''){
					strLeadCode			= strleadArr[0];
					strLeadOwnerCode	= strleadArr[1];
				}else{
					strLeadCode			= strLeadCode + DELIMITER + strleadArr[0];
					strLeadOwnerCode	= strLeadOwnerCode + DELIMITER + strleadArr[1];
				}
			});
			
			$(objFrom).find('#txtLeadCode').val(strLeadCode);
			$(objFrom).find('#txtLeadOwnerCode').val(strLeadOwnerCode);
			$('#'+pModelRefenceObject).modal('open');
		}
		return false;
	}else{
		
		$('#'+pModelRefenceObject).modal('open');
		$('#txtDeleteRecordCode').val(pIntRecordCode);
		$('#txtCode').val(pIntRecordCode);
		$('.cmdSearchReset').addClass('hide');
		$('.no-search').removeClass('hide');
		$('.no-add').addClass('hide');
		objectRefrence	= null;
		
		if($('.addItemInModule').length == 0){
			$('#txtRoleCode').val(pIntRecordCode);
		}
		
		if('divFieldMapping' == pModelRefenceObject){
			$('#txtModuleFieldCode').val(pIntRecordCode);
			$('#frmGetDataByCode').append('<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="'+pIntRecordCode+'" />');
		}
		
		switch(parseInt(isEdit)){
			case 1:
				showLoader();
				postUserRequest('frmGetDataByCode');
				objectRefrence	= pModelRefenceObject;
				$('.spnActionText').html('Edit');
				break;
			case 2:
				if($('#frmLeadsColumnSearch').length == 1){
					objFrom	= 'frmAddNewLead';
					$('.cmdDMLAction').attr('formname','frmAddNewLead');
				}
				$('.spnActionText').html('Add New');
				$(objFrom)[0].reset();
				$(objFrom).find('input[type=hidden]').val('');
				$(objFrom).find('select').material_select();
				break;
			case 3:
				if($('#frmLeadsColumnSearch').length == 1){
					objFrom	= 'frmLeadsColumnSearch';
					$('.cmdDMLAction').attr('formname','frmLeadsColumnSearch');
					$('#'+objFrom).find('input[id="txtSearch"]').val('1');
				}else{
					$(objFrom).find("#txtSearch").val('1');
				}
				$('.spnActionText').html('Search');
				
				$('.cmdSearchReset').removeClass('hide');
				$('.no-search').addClass('hide');
				$('.no-add').removeClass('hide');
				if($('#txtSearchFilters').html() != ''){
					$(objFrom).find(':input:enabled:visible:first').focus();
					var strSearchArr	= jQuery.parseJSON($('#txtSearchFilters').html());
					$.each(strSearchArr, function(strElementRefObj, strElementValue){
						$(objFrom).find('#'+strElementRefObj).val(strElementValue);
					});
					$(objFrom).find('select').material_select();
				}
				
				break;
			/* Setting Lead follow-up details */
			case 4:
				var strleadArr	= pIntRecordCode.split(DELIMITER);
				$(objFrom).find('#txtLeadCode').val(strleadArr[0]);
				$(objFrom).find('#txtLeadOwnerCode').val(strleadArr[1]);
				
				if(strleadArr[1] == ''){
					postUserRequest('frmLeadProfileDetails');
				}
				break;
			
		}
	}
}

$(document).ready(function(){
	init();
	/* Submitting the from */
	$('#cmdLogin, #cmdCompanyRegister, #cmdStatusManagment, #cmdDeleteRecord, .cmdDMLAction').click(function(){
		
		/* Checking for custom attributes */
		var strFormName = $(this).attr('formName');
		/* Checking attributes values */
		if (typeof strFormName == typeof undefined || strFormName == false) {
			/* Displaying error message */
			showToast('formName attributes is missing on Action button.');
		}else{
			showLoader();
			clearAllToast();
			if($('#'+strFormName).find('input[id="txtSearch"]').val() == '1'){
				goToPage(0,strFormName);
				//$('#'+strFormName).submit();
				return;
			}else{
				postUserRequest(strFormName);
			}
		}
		return false;
	});

	/* Setting search filter */
	$('.cmdSearchReset').click(function(){
		
		$('.maintain').each(function(){
				$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />')
		});
		$('#frmModuleSearch').submit();
	});
	
	/* Filed Mapping */
	$('#cmdLeadAttributeAdding').click(function(){
		if($('#cboLeadAttributeCode').val() != ''){
			var leadAttributeObj	= $('#cboLeadAttributeCode option:selected');
			$('#tblLeadAttribute').append(addFieldRowInTable($(leadAttributeObj).val(), $(leadAttributeObj).text()));
			$(leadAttributeObj).remove();
			$('#cboLeadAttributeCode').material_select();
		}
	});
	
	/* Select all leads of page */
	$('#chkBoxSelectAllLeads').click(function(){
		var blnIsCheked	= $(this).is(':checked');
		$('input[name="chkLeadCode[]"]').each(function(){
			$(this).removeAttr('checked');
			if(blnIsCheked){
				$(this).attr('checked','checked');
			}
		});
	});
});

/**************************************************************************
 Purpose 		: Navigate the page to destination with key value.
 Inputs  		: strDestiantionURl : Destination URL,
				: strJSONEKeyValuepariString : Key value pair string
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setNavigation(strDestiantionUrl, strJSONEKeyValuepariString){
	/* Converting keyValue String to Array */
	var strValueArr	= (strJSONEKeyValuepariString).split('~V~');
	var strReturnString = '<form name="frmNavigatetoDestination" id="frmNavigatetoDestination" method="post" action="'+strDestiantionUrl+'">';
	
	/* Iterating the loop */
	$.each(strValueArr , function(strElementKey , strElementValue){
		var strKeyValue = strElementValue.split('=>');
		strReturnString +='<input type="hidden" name="'+strKeyValue[0]+'" id="'+strKeyValue[0]+'" value="'+strKeyValue[1]+'" />';
	});
	
	/* Closing the form */
	strReturnString +='</form>';
	
	$(document).find('body').append(strReturnString);
	$('#frmNavigatetoDestination').submit();
}


/**************************************************************************
 Purpose 		: Creating the dependency data fill method.
 Inputs  		: pObjectRefrence : Action object reference name,
				: pDestinationObject : Object data needs to fill,
				: pExtraParameters :: Extra parameter
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function getDependencyData(pObjectRefrence, pDestinationObject, pExtraParameters){
	/* Variable initialization */
	var strSelectValueString = $(pObjectRefrence).val();
	/* Setting the mandatory */
	$('#frmCustom').append('<input type="hidden" name="txtDataCodes" id="txtDataCodes" value="'+strSelectValueString+'" />');
	/* Setting optional parameter */
	if(pExtraParameters != ''){
		/* setting parameter */
		$('#frmCustom').append('<input type="hidden" name="txtExtraParam" id="txtExtraParam" value="'+pExtraParameters+'" />');
	}
	objectRefrence	= pDestinationObject;
	postUserRequest('frmCustom');
}

/**************************************************************************
 Purpose 		: Remove options / row from table.
 Inputs  		: objRefrence : Row reference 
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function removeOptions(objRefrence){
	var currentRowRefrence = $(objRefrence).parent().parent();
	var strLabel		   = $(currentRowRefrence).find('span').html();
	var strValue		   = $(currentRowRefrence).find('input').val();
	$(currentRowRefrence).remove();
	$('#cboLeadAttributeCode').append('<option value="'+strValue+'">'+strLabel+'</option>').material_select();
}

/**************************************************************************
 Purpose 		: Adding attributes field .
 Inputs  		: pAttriniteCode : Attribute code,
				: pStrAttributeName :: Attribute Name.
 Return 		: ROW HTML.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function addFieldRowInTable(pAttriniteCode , pStrAttributeName){
	return '<tr><td><input type="hidden" id="txtFiledCode[]" name="txtFiledCode[]" value="'+pAttriniteCode+'" /><span>'+pStrAttributeName+'</span></td><td><a href="javascript:void(0);" onclick="removeOptions(this);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;</td></tr>';
}

/**************************************************************************
 Purpose 		: Adding the pull down event on change event based on object.
 Inputs  		: pFormRefrence :: Form Reference.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setPullDownEvents(){
	/* Checking the select object to check the dependency */
	$('select').each(function(){
		/* checking the dependency the target element */
		if($(this).attr('check-dependency') && $(this).attr('dependency-element')){
			var strAction	= $(this).attr('check-dependency');
			objectRefrence	= $(this).attr('dependency-element');
			$(this).bind('change',function(){
				$('#frmDynamicEventDataSet').append('<input type="hidden" name="txtRegionCode" id="txtRegionCode" value="'+$(this).val()+'" />');
				$('#frmDynamicEventDataSet').attr('action',SITE_URL+'leadsoperation/leadsoperation/'+strAction);
				postUserRequest('frmDynamicEventDataSet');
			});
		}
		
	})
}

/**************************************************************************
 Purpose 		: Setting the lead follow up view based on selected status.
 Inputs  		: pObjectRefrence :: Status element Reference.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setFollowUpView(pObjectRefrence){
	var strValue = {'statusCode':$(pObjectRefrence).val()};
	
	postUserRequestVirualForm('frmStatusClassification',strValue,SITE_URL+'leadsoperation/leadsoperation/isOpenStatusCheck');
}