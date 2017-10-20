function fnList(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.List').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'List';
    });
}
function fnSearch(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Search').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'Search';
    });
}
function fnInsert(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Insert').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'Submit';
    });
}
function fnUpdate(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Update').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'Update';
    });
}
function fnDelete(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Delete').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'Delete';
    });
}
function fnPickup(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Pickup').show();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
        document.getElementById('submitbutton').value = 'Mark as Picked Up';
    });
}
function fnView(callback){
    $('.criteria').fadeOut(500, function(){
        $('.formfields').hide();
        $('.Update').show();
	$('.Buttons').hide();
	if ( callback != null ) callback();
        $('.criteria').fadeIn(500);
    });
}

function convertPhoneFieldIntl(num) {
	$('#Phone' + num + '-1').attr('size', '15');
	$('#Phone' + num + '-2').hide();
	$('#Phone' + num + '-3').hide();
	$('.Phone' + num + 'StdFormatting').hide();
}

function convertPhoneFieldStd(num) {
	$('#Phone' + num + '-1').attr('size', '3');
	$('#Phone' + num + '-2').show();
	$('#Phone' + num + '-3').show();
	$('.Phone' + num + 'StdFormatting').show();
}

function contradict(b) {
  if ( b == "true") {
    return false;
  }
  if ( b == "false") {
    return true;
  }
  if ( b == true ) {
    return false;
  }
  else {
    return true;
  }
}

$(document).ready(function(){

	$('.telEntrySec3').keyup(function(){
            if ($(this).val().length >= 3) {
		var fieldNum = $(this).attr('id').substr(5, 1);
		var secNum = $(this).attr('id').substr(7, 1);
		$("#Phone" + fieldNum + "-" + (1 + parseInt(secNum))).focus();
	    }
	});
	$('.telEntrySec4').keyup(function(){
            if ($(this).val().length >= 4) {
		var fieldNum = $(this).attr('id').substr(5, 1);
		var secNum = $(this).attr('id').substr(7, 1);
		$("#Phone" + (1 + parseInt(fieldNum)) + "-1").focus();
	    }
	});

	$('#Phone1Type').change(function(){
	    if ($(this).val() == 'international') {
		convertPhoneFieldIntl('1');
	    }
	    else {
		convertPhoneFieldStd('1');
	    }
	});
	$('#Phone2Type').change(function(){
	    if ($(this).val() == 'international') {
		convertPhoneFieldIntl('2');
	    }
	    else {
		convertPhoneFieldStd('2');
	    }
	});
	$('#Phone3Type').change(function(){
	    if ($(this).val() == 'international') {
		convertPhoneFieldIntl('3');
	    }
	    else {
		convertPhoneFieldStd('3');
	    }
	});
	$('#Phone4Type').change(function(){
	    if ($(this).val() == 'international') {
		convertPhoneFieldIntl('4');
	    }
	    else {
		convertPhoneFieldStd('4');
	    }
	});

	var phoneTypes = {
		'' : '' ,
    		work : 'Work',
    		home : 'Home',
		cell : 'Cell',
		international : "Int'l",
		fax : "Fax",
		other: "Other"
	};
	$.each(phoneTypes, function(val, text) {
    		$('.PhoneType').append( new Option(text,val) );
	});

	if ( typeof(profilePhoneType) == 'function' ) {
	    profilePhoneType();
	}

	$('#fnList').click(function(){
	    fnList(function(){document.forms['recordinput'].reset();
				 document.getElementById('fnList').checked = true;
				if ( typeof(listCallback) == 'function' )
				    listCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});

	$('#fnSearch').click(function(){
	    fnSearch(function(){document.forms['recordinput'].reset();
				 document.getElementById('fnSearch').checked = true;
				if ( typeof(searchCallback) == 'function' )
				    searchCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});

	$('#fnInsert').click(function(){
	    fnInsert(function(){document.forms['recordinput'].reset();
				document.getElementById('fnInsert').checked = true;
				if ( typeof(insertCallback) == 'function' )
				    insertCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});

	$('#fnUpdate').click(function(){
	    fnUpdate(function(){document.forms['recordinput'].reset();
				 document.getElementById('fnUpdate').checked = true;
				if ( typeof(updateCallback) == 'function' )
				    updateCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});

	$('#fnDelete').click(function(){
	    fnDelete(function(){if ( typeof(deleteCallback) == 'function' )
				    deleteCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});
	$('#fnPickup').click(function(){
	    fnPickup(function(){if ( typeof(pickupCallback) == 'function' )
				    pickupCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});
	$('#fnView').click(function(){
	    fnView(function(){if ( typeof(viewCallback) == 'function' )
				    viewCallback();
				if ( typeof(defaultCallback) == 'function' )
				    defaultCallback(); });
	});
        $('.formfields').hide();

  $('.registration-error').each(function(error) {
    window.alert($(this).html(), 'Registration Error');
  });

	//Default function for all modules
	var submit = document.getElementById('submitbutton');
	if ( submit != null ) {
	    submit.value = 'List';
	}
});

