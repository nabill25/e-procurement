function countCheckedBayar(reqElementId, reqElement) {
  varElement = reqElement;
  varElementParam = reqElementId;
  
  var output = $("#"+varElementParam+":checked").length;
  //alert(output);
  if(output == 1)
	{
		doEnable();
	}
	else
	{
		doDisable();
		document.getElementById(varElement).value = '';		
	}
}
		
function enableElement(reqElementId, output)
{
	varElement = reqElementId;
	if(output == 1)
	{
		doEnable();
	}
	else
	{
		doDisable();
		document.getElementById(varElement).value = '';		
	}
}

function displayParameter(reqElementId, output)
{
	varElement = reqElementId;
	if(output == 1)
	{
		doShowMenu();
	}
	else
	{
		doHideMenu();
	}
}

function enable_text(reqElementId, status)
{
	varElement = reqElementId;
	status=!status;
	document.getElementById(reqElementId).disabled = status;
}

function doEnable()
{
	document.getElementById(varElement).disabled = false;
}

function doDisable()
{
	document.getElementById(varElement).disabled = true;
}

function displayElement(reqElementId)
{
	varElement = reqElementId;
	if(document.getElementById(varElement).style.display == '' || document.getElementById(varElement).style.display == 'inline')
		doHideMenu();
	else if(document.getElementById(varElement).style.display == 'none')
		doShowMenu();
}

function hideElement(reqElementId)
{
	varElement = reqElementId;
	doHideMenu();
}

function doHideMenu()
{
	document.getElementById(varElement).style.display = 'none';
}

function doShowMenu()
{
	document.getElementById(varElement).style.display = '';
}