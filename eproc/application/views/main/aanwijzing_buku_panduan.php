<?php
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?=base_url();?>" />

<link rel="stylesheet" href="css/core.css" type="text/css">
<link rel="stylesheet" href="css/core-bootstrap.css" type="text/css">

<!-- BOOTSTRAP -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
<link href="lib/bootstrap/bootstrap.css" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

<script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquery.min.js"></script>
<!-- FULLSCREEN BOOKBLOCK -->
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/FullscreenBookBlock/css/jquery.jscrollpane.custom.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/FullscreenBookBlock/css/bookblock.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/FullscreenBookBlock/css/custom.css" />
<script src="<?= base_url() ?>lib/FullscreenBookBlock/js/modernizr.custom.79639.js"></script>

<link rel="stylesheet" href="<?= base_url() ?>lib/DHTMLWindow/windowfiles/dhtmlwindow.css" type="text/css" />
<script type="text/javascript" src="<?= base_url() ?>lib/DHTMLWindow/windowfiles/dhtmlwindow.js"></script>

</head>

<body>
	
    <div class="container-fluid container-aps">
    	
        <div class="row">
        	<div class="col-md-12">
            	
                <div class="menu-panel" style="overflow:scroll;">
                    <h3 style="position:fixed; background:#71cee8; width:225px; display:block;"><?=translate("Daftar Isi", "table of contents")?></h3>
                    <ul id="menu-toc" class="menu-toc" style="margin-top:57px;">
                        <li class="menu-toc-current"><a href="#item1"><?=translate("Hal", "Page")?> 1</a></li>                    
                        <li><a href="#item2"><?=translate("Hal", "Page")?> 2</a></li>
                    </ul>
                    
                </div>
    
                <div class="bb-custom-wrapper">
                    <div id="bb-bookblock" class="bb-bookblock">
                        <div class="bb-item" id="item1">
                            <div id="separuh-kiri" style="background-size:100% 100%;">
                                <img style="width: 100%" src="<?= base_url() ?>images/panduan1.jpg">
                            </div>
                            <div id="separuh-kanan">
                                <img style="width: 100%" src="<?= base_url() ?>images/panduan2.jpg">                        	
                            </div>
                        </div>
                        <div class="bb-item" id="item2">
                            <div id="separuh-kiri" style="background-size:100% 100%;">
                                <img style="width: 100%" src="<?= base_url() ?>images/panduan3.jpg">
                            </div>
                            <div id="separuh-kanan">
                                <img style="width: 100%" src="<?= base_url() ?>images/panduan4.jpg">
                            </div>
                        </div>
                    </div>
                    
                    <nav>
                        <span id="bb-nav-prev">&larr;</span>
                        <span id="bb-nav-next">&rarr;</span>
                    </nav>
    
                    <span id="tblcontents" class="menu-button"><?=translate("Daftar Isi", "table of contents")?></span>
    
                </div>
                
            </div>
        </div>        
    </div>
    
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
    <!--<script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquery.min.js"></script>-->
    <script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquery.mousewheel.js"></script>
    <script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquery.jscrollpane.min.js"></script>
    <script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquerypp.custom.js"></script>
    <script src="<?= base_url() ?>lib/FullscreenBookBlock/js/jquery.bookblock.js"></script>
    <script src="<?= base_url() ?>lib/FullscreenBookBlock/js/page.js"></script>
    <script>
        $(function() {

            Page.init();

        });
    </script>
    
</body>
</html>
