/***********************************************************************************************************************
DOCUMENT: includes/javascript.js
DEVELOPED BY: Ryan Stemkoski
COMPANY: Zipline Interactive
EMAIL: ryan@gozipline.com
PHONE: 509-321-2849
DATE: 2/26/2009
DESCRIPTION: This is the JavaScript required to create the accordion style menu.  Requires jQuery library
************************************************************************************************************************/

$(document).ready(function() {
	
	/********************************************************************************************************************
	SIMPLE ACCORDIAN STYLE MENU FUNCTION
	********************************************************************************************************************/	
	//$("#wrapper-accordion-menu").hide();
	
	//$(this).addClass('blue')
	$('div.accordionButton').click(function() {
		$('div.accordionContent').slideUp('normal');	
		$(this).next().slideDown('normal');
		
		//$(this).toggleClass("red grey");
		//alert('haii');
		//$(this).addClass('blue');
		//$(this).next().removeClass('blue');
		/*if($(this).hasClass('red'))
		{
			$(this).addClass('blue').removeClass('red');
		}
		else
		{
		   $(this).addClass('red').removeClass('blue');
		}*/
	});
	
	/********************************************************************************************************************
	CLOSES ALL DIVS ON PAGE LOAD
	********************************************************************************************************************/	
	//$("div.accordionContent").hide();
	//$('div.accordionButton').removeClass('blue');
	
//	$("div.accordionContent1").show();
//	$("div.accordionContent").show();
	$('div.accordionContent:first').show();

});
