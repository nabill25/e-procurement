function doAdd() 
{
    form1.reqMode.value = "requestTambahData";
    form1.action = "<?= $pageAdd ?>"; 
    form1.submit(); 
        
}

function doSubmitEdit() 
{
    form1.reqMode.value = "submitEdit";
    form1.action = "<?= $pageEdit ?>"; 
    form1.submit(); 
        
}

function doEdit(varEdit) 
{
    form1.reqMode.value = "requestEdit";
    form1.action = "<?= $pageEdit ?>?reqBtnId=" + varEdit; 
    form1.submit(); 
        
}

function doDelete() 
{
    if (confirm("Hapus Data?")) { 
        form1.reqMode.value = "submitDelete";
        form1.submit(); 
    }
}

function doViewData() 
{
    form1.reqMode.value = "";
    form1.action = "<?= $pageView ?>"; 
    form1.submit(); 
        
}

function doSubmitSearch() 
{
    form1.reqMode.value = "requestSearch";
    form1.action = "<?= $pageView ?>"; 
    form1.submit(); 
        
}

function doCetak(actionFile) 
{
    form1.reqMode.value = "requestCetak";
    form1.action = actionFile; 
    form1.submit(); 
        
}

function confirmAction(varUrl, varOption)
{
	var varQuestion = new Array();
	
	varQuestion[1] = "Hapus data?";
	varQuestion[2] = "Tambah data?";
	varQuestion[3] = "Anda yakin?";
    varQuestion[4] = "Hapus data image?";

	if(confirm(varQuestion[varOption]))
	{
		document.location = varUrl;
	}
}
function windowOpener(windowHeight, windowWidth, windowName, windowUri)
{
    var centerWidth = (window.screen.width - windowWidth) / 2;
    var centerHeight = (window.screen.height - windowHeight) / 2;

    newWindow = window.open(windowUri, windowName, 'resizable=0,width=' + windowWidth + 
        ',height=' + windowHeight + 
        ',left=' + centerWidth + 
        ',top=' + centerHeight);

    newWindow.focus();
    return newWindow.name;
}

function windowOpenerPopup(windowHeight, windowWidth, windowName, windowUri)
{
    var centerWidth = (window.screen.width - windowWidth) / 2;
    var centerHeight = (window.screen.height - windowHeight) / 2;

    newWindow = window.open(windowUri, windowName, 'resizable=1,scrollbars=yes,width=' + windowWidth + 
        ',height=' + windowHeight + 
        ',left=' + centerWidth + 
        ',top=' + centerHeight);

    newWindow.focus();
    return newWindow.name;
}

function PopupClose(windowName)
{
    windowName.close();
    /*return newWindow.name;*/
}