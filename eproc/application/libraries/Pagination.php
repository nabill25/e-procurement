<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
/**
 * CodexWorld is a programming blog. Our mission is to provide the best online resources on programming and web development.
 *
 * This Pagination class helps to integrate ajax pagination in PHP.
 *
 * @class		Pagination
 * @author		CodexWorld
 * @link		http://www.codexworld.com
 * @contact		http://www.codexworld.com/contact-us
 * @version		1.0
 */
class Pagination{
	var $baseURL		= '';
	var $totalRows  	= '';
	var $perPage	 	= 10;
	var $numLinks		=  2;
	var $currentPage	=  0;
	var $firstLink   	= '«';
	var $nextLink		= '›';
	var $prevLink		= '	‹';
	var $lastLink		= '»';
	var $fullTagOpen	= '<div class="col-md-6 pagination">';
	var $fullTagClose	= '</div>';
	var $firstTagOpen	= '';
	var $firstTagClose	= '';
	var $lastTagOpen	= '';
	var $lastTagClose	= '';
	var $curTagOpen		= '<li><a>';
	var $curTagClose	= '</a></li>';
	// var $nextTagOpen	= '</div>';
	var $nextTagOpen	= '';
	var $nextTagClose	= '';
	var $prevTagOpen	= '';
	var $prevTagClose	= '';
	var $numTagOpen		= '';
	var $numTagClose	= '';
	var $anchorClass	= '';
	var $showCount      = true;
	var $currentOffset	= 0;
	var $contentDiv     = '';
    var $additionalParam= '';
    var $searchText = '';
	var $showRecord = 5;
	var $searchVarible = 'reqPencarian';
	var $searchVarible2 = 'reqPencarian2';
	var $searchVarible3 = 'reqPencarian3';
	var $searchVarible4 = 'reqPencarian4';
	var $arrSerialized = '';
    
	function __construct($params = array()){
		if (count($params) > 0){
			$this->initialize($params);		
		}
		
		if ($this->anchorClass != ''){
			$this->anchorClass = 'class="'.$this->anchorClass.'" ';
		}	
	}
	
	function initialize($params = array()){
		if (count($params) > 0){
			foreach ($params as $key => $val){
				if (isset($this->$key)){
					$this->$key = $val;
				}
			}		
		}
	}
	
	/**
	 * Generate the pagination links
	 */	
	function createLinks(){ 
		// If total number of rows is zero, do not need to continue
		if ($this->totalRows == 0 OR $this->perPage == 0){
		   return '';
		}

		// Calculate the total number of pages
		$numPages = ceil($this->totalRows / $this->perPage);

		// Is there only one page? will not need to continue
		if ($numPages == 1){
			if ($this->showCount){
				$info = '<div class="area-pagination"><div class="menampilkan">Menampilkan : ' . $this->totalRows .'</div></div>';
				return $info;
			}else{
				return '';
			}
		}

		// Determine the current page	
		if ( ! is_numeric($this->currentPage)){
			$this->currentPage = 0;
		}
		
		// Links content string variable
		$output = '';
		
		// Showing links notification
		if ($this->showCount){
		   $currentOffset = $this->currentPage;
		   $info = 'Menampilkan ' . ( $currentOffset + 1 ) . ' ke ' ;
		
		   if( ( $currentOffset + $this->perPage ) < ( $this->totalRows -1 ) )
			  $info .= $currentOffset + $this->perPage;
		   else
			  $info .= $this->totalRows;
		
		   $info .= ' dari ' . $this->totalRows . '</div> <div class="col-md-6 pull-right text-right pagination"><ul class="text-right">';
		
		   $output .= $info ;
		}
		
		$this->numLinks = (int)$this->numLinks;
		
		// Is the page number beyond the result range? the last page will show
		if ($this->currentPage > $this->totalRows){
			$this->currentPage = ($numPages - 1) * $this->perPage;
		}
		
		$uriPageNum = $this->currentPage;
		
		$this->currentPage = floor(($this->currentPage/$this->perPage) + 1);

		// Calculate the start and end numbers. 
		$start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
		$end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

		// Render the "First" link
		if  ($this->currentPage > $this->numLinks){
			$output .= $this->firstTagOpen 
				. $this->getAJAXlink( '' , $this->firstLink)
				. $this->firstTagClose; 
		}

		// Render the "previous" link
		if  ($this->currentPage != 1){
			$i = $uriPageNum - $this->perPage;
			if ($i == 0) $i = '';
			$output .= $this->prevTagOpen 
				. $this->getAJAXlink( $i, $this->prevLink )
				. $this->prevTagClose;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++){
			$i = ($loop * $this->perPage) - $this->perPage;
					
			if ($i >= 0){
				if ($this->currentPage == $loop){
					$output .= $this->curTagOpen.$loop.$this->curTagClose;
				}else{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->numTagOpen
						. $this->getAJAXlink( $n, $loop )
						. $this->numTagClose;
				}
			}
		}

		// Render the "next" link
		if ($this->currentPage < $numPages){
			$output .= $this->nextTagOpen 
				. $this->getAJAXlink( $this->currentPage * $this->perPage , $this->nextLink )
				. $this->nextTagClose;
		}

		// Render the "Last" link
		if (($this->currentPage + $this->numLinks) < $numPages){
			$i = (($numPages * $this->perPage) - $this->perPage);
			$output .= $this->lastTagOpen . $this->getAJAXlink( $i, $this->lastLink ) .'</div></div>' . $this->lastTagClose;
			// $output .= $this->lastTagOpen . $this->getAJAXlink( $i, $this->lastLink ) .'</div>' . $this->lastTagClose;
		}

		// Remove double slashes
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->fullTagOpen.$output.$this->fullTagClose;
		
		return $output;		
	}

	function createLinks2(){ 
		// If total number of rows is zero, do not need to continue
		if ($this->totalRows == 0 OR $this->perPage == 0){
		   return '';
		}

		// Calculate the total number of pages
		$numPages = ceil($this->totalRows / $this->perPage);

		// Is there only one page? will not need to continue
		if ($numPages == 1){
			if ($this->showCount){
				$info = '<div class="area-pagination"><div class="menampilkan">Menampilkan : ' . $this->totalRows .'</div></div>';
				return $info;
			}else{
				return '';
			}
		}

		// Determine the current page	
		if ( ! is_numeric($this->currentPage)){
			$this->currentPage = 0;
		}
		
		// Links content string variable
		$output = '';
		
		// Showing links notification
		if ($this->showCount){
		   $currentOffset = $this->currentPage;
		   $info = 'Menampilkan ' . ( $currentOffset + 1 ) . ' ke ' ;
		
		   if( ( $currentOffset + $this->perPage ) < ( $this->totalRows -1 ) )
			  $info .= $currentOffset + $this->perPage;
		   else
			  $info .= $this->totalRows;
		
		   $info .= ' dari ' . $this->totalRows . '</div> <div class="col-md-6 pull-right text-right pagination"><ul class="text-right">';
		
		   $output .= $info ;
		}
		
		$this->numLinks = (int)$this->numLinks;
		
		// Is the page number beyond the result range? the last page will show
		if ($this->currentPage > $this->totalRows){
			$this->currentPage = ($numPages - 1) * $this->perPage;
		}
		
		$uriPageNum = $this->currentPage;
		
		$this->currentPage = floor(($this->currentPage/$this->perPage) + 1);

		// Calculate the start and end numbers. 
		$start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
		$end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

		// Render the "First" link
		if  ($this->currentPage > $this->numLinks){
			$output .= $this->firstTagOpen 
				. $this->getAJAXlink2( '' , $this->firstLink)
				. $this->firstTagClose; 
		}

		// Render the "previous" link
		if  ($this->currentPage != 1){
			$i = $uriPageNum - $this->perPage;
			if ($i == 0) $i = '';
			$output .= $this->prevTagOpen 
				. $this->getAJAXlink2( $i, $this->prevLink )
				. $this->prevTagClose;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++){
			$i = ($loop * $this->perPage) - $this->perPage;
					
			if ($i >= 0){
				if ($this->currentPage == $loop){
					$output .= $this->curTagOpen.$loop.$this->curTagClose;
				}else{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->numTagOpen
						. $this->getAJAXlink2( $n, $loop )
						. $this->numTagClose;
				}
			}
		}

		// Render the "next" link
		if ($this->currentPage < $numPages){
			$output .= $this->nextTagOpen 
				. $this->getAJAXlink2( $this->currentPage * $this->perPage , $this->nextLink )
				. $this->nextTagClose;
		}

		// Render the "Last" link
		if (($this->currentPage + $this->numLinks) < $numPages){
			$i = (($numPages * $this->perPage) - $this->perPage);
			$output .= $this->lastTagOpen . $this->getAJAXlink2( $i, $this->lastLink ) .'</div></div>' . $this->lastTagClose;
			// $output .= $this->lastTagOpen . $this->getAJAXlink2( $i, $this->lastLink ) .'</div>' . $this->lastTagClose;
		}

		// Remove double slashes
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->fullTagOpen.$output.$this->fullTagClose;
		
		return $output;		
	}

	function createLinks3(){ 
		// If total number of rows is zero, do not need to continue
		if ($this->totalRows == 0 OR $this->perPage == 0){
		   return '';
		}

		// Calculate the total number of pages
		$numPages = ceil($this->totalRows / $this->perPage);

		// Is there only one page? will not need to continue
		if ($numPages == 1){
			if ($this->showCount){
				$info = '<div class="area-pagination"><div class="menampilkan">Menampilkan : ' . $this->totalRows .'</div></div>';
				return $info;
			}else{
				return '';
			}
		}

		// Determine the current page	
		if ( ! is_numeric($this->currentPage)){
			$this->currentPage = 0;
		}
		
		// Links content string variable
		$output = '';
		
		// Showing links notification
		if ($this->showCount){
		   $currentOffset = $this->currentPage;
		   $info = 'Menampilkan ' . ( $currentOffset + 1 ) . ' ke ' ;
		
		   if( ( $currentOffset + $this->perPage ) < ( $this->totalRows -1 ) )
			  $info .= $currentOffset + $this->perPage;
		   else
			  $info .= $this->totalRows;
		
		   $info .= ' dari ' . $this->totalRows . '</div> <div class="col-md-6 pull-right text-right pagination"><ul class="text-right">';
		
		   $output .= $info ;
		}
		
		$this->numLinks = (int)$this->numLinks;
		
		// Is the page number beyond the result range? the last page will show
		if ($this->currentPage > $this->totalRows){
			$this->currentPage = ($numPages - 1) * $this->perPage;
		}
		
		$uriPageNum = $this->currentPage;
		
		$this->currentPage = floor(($this->currentPage/$this->perPage) + 1);

		// Calculate the start and end numbers. 
		$start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
		$end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

		// Render the "First" link
		if  ($this->currentPage > $this->numLinks){
			$output .= $this->firstTagOpen 
				. $this->getAJAXlink3( '' , $this->firstLink)
				. $this->firstTagClose; 
		}

		// Render the "previous" link
		if  ($this->currentPage != 1){
			$i = $uriPageNum - $this->perPage;
			if ($i == 0) $i = '';
			$output .= $this->prevTagOpen 
				. $this->getAJAXlink3( $i, $this->prevLink )
				. $this->prevTagClose;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++){
			$i = ($loop * $this->perPage) - $this->perPage;
					
			if ($i >= 0){
				if ($this->currentPage == $loop){
					$output .= $this->curTagOpen.$loop.$this->curTagClose;
				}else{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->numTagOpen
						. $this->getAJAXlink3( $n, $loop )
						. $this->numTagClose;
				}
			}
		}

		// Render the "next" link
		if ($this->currentPage < $numPages){
			$output .= $this->nextTagOpen 
				. $this->getAJAXlink3( $this->currentPage * $this->perPage , $this->nextLink )
				. $this->nextTagClose;
		}

		// Render the "Last" link
		if (($this->currentPage + $this->numLinks) < $numPages){
			$i = (($numPages * $this->perPage) - $this->perPage);
			$output .= $this->lastTagOpen . $this->getAJAXlink3( $i, $this->lastLink ) .'</div></div>' . $this->lastTagClose;
		}

		// Remove double slashes
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->fullTagOpen.$output.$this->fullTagClose;
		
		return $output;		
	}

	function createLinks4(){ 
		// If total number of rows is zero, do not need to continue
		if ($this->totalRows == 0 OR $this->perPage == 0){
		   return '';
		}

		// Calculate the total number of pages
		$numPages = ceil($this->totalRows / $this->perPage);

		// Is there only one page? will not need to continue
		if ($numPages == 1){
			if ($this->showCount){
				$info = '<div class="area-pagination"><div class="menampilkan">Menampilkan : ' . $this->totalRows .'</div></div>';
				return $info;
			}else{
				return '';
			}
		}

		// Determine the current page	
		if ( ! is_numeric($this->currentPage)){
			$this->currentPage = 0;
		}
		
		// Links content string variable
		$output = '';
		
		// Showing links notification
		if ($this->showCount){
		   $currentOffset = $this->currentPage;
		   $info = 'Menampilkan ' . ( $currentOffset + 1 ) . ' ke ' ;
		
		   if( ( $currentOffset + $this->perPage ) < ( $this->totalRows -1 ) )
			  $info .= $currentOffset + $this->perPage;
		   else
			  $info .= $this->totalRows;
		
		   $info .= ' dari ' . $this->totalRows . '</div> <div class="col-md-6 pull-right text-right pagination"><ul class="text-right">';
		
		   $output .= $info ;
		}
		
		$this->numLinks = (int)$this->numLinks;
		
		// Is the page number beyond the result range? the last page will show
		if ($this->currentPage > $this->totalRows){
			$this->currentPage = ($numPages - 1) * $this->perPage;
		}
		
		$uriPageNum = $this->currentPage;
		
		$this->currentPage = floor(($this->currentPage/$this->perPage) + 1);

		// Calculate the start and end numbers. 
		$start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
		$end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

		// Render the "First" link
		if  ($this->currentPage > $this->numLinks){
			$output .= $this->firstTagOpen 
				. $this->getAJAXlink4( '' , $this->firstLink)
				. $this->firstTagClose; 
		}

		// Render the "previous" link
		if  ($this->currentPage != 1){
			$i = $uriPageNum - $this->perPage;
			if ($i == 0) $i = '';
			$output .= $this->prevTagOpen 
				. $this->getAJAXlink4( $i, $this->prevLink )
				. $this->prevTagClose;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++){
			$i = ($loop * $this->perPage) - $this->perPage;
					
			if ($i >= 0){
				if ($this->currentPage == $loop){
					$output .= $this->curTagOpen.$loop.$this->curTagClose;
				}else{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->numTagOpen
						. $this->getAJAXlink4( $n, $loop )
						. $this->numTagClose;
				}
			}
		}

		// Render the "next" link
		if ($this->currentPage < $numPages){
			$output .= $this->nextTagOpen 
				. $this->getAJAXlink4( $this->currentPage * $this->perPage , $this->nextLink )
				. $this->nextTagClose;
		}

		// Render the "Last" link
		if (($this->currentPage + $this->numLinks) < $numPages){
			$i = (($numPages * $this->perPage) - $this->perPage);
			$output .= $this->lastTagOpen . $this->getAJAXlink4( $i, $this->lastLink ) .'</div></div>' . $this->lastTagClose;
		}

		// Remove double slashes
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->fullTagOpen.$output.$this->fullTagClose;
		
		return $output;		
	}
	
	function createSearching(){
		
        $this->additionalParam = "{'page' : 0, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){ $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;";
	
	}

	function createSearching2(){
		
        $this->additionalParam = "{'page' : 0, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){ $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;";
	
	}	

	function createSearching3(){
		
        $this->additionalParam = "{'page' : 0, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'search3' : $('#".$this->searchVarible3."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){ $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;";
	
	}	


	function createSearching4(){
		
        $this->additionalParam = "{'page' : 0, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'search3' : $('#".$this->searchVarible3."').val(), 'search4' : $('#".$this->searchVarible4."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){ $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;";
	
	}	

	function getAJAXlink( $count, $text) {
        if( $this->contentDiv == '')
            return '<li><a href="'. $this->anchorClass . ' ' . $this->baseURL . $count . '" class="pager-next active">'. $text .'</a></li>';
				
        $pageCount = $count?$count:0;
        $this->additionalParam = "{'page' : $pageCount, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : '".$this->searchText."', 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "<li><a href=\"javascript:void(0);\" " . $this->anchorClass . "
				onclick=\"$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){
					   $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;\"  class=\"pager-next active\">"
			   . $text .'</a></li>';
	}

	function getAJAXlink2( $count, $text) {
        if( $this->contentDiv == '')
            return '<li><a href="'. $this->anchorClass . ' ' . $this->baseURL . $count . '" class="pager-next active">'. $text .'</a></li>';
				
        $pageCount = $count?$count:0;
        $this->additionalParam = "{'page' : $pageCount, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "<li><a href=\"javascript:void(0);\" " . $this->anchorClass . "
				onclick=\"$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){
					   $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;\"  class=\"pager-next active\">"
			   . $text .'</a></li>';
	}

	function getAJAXlink3( $count, $text) {
        if( $this->contentDiv == '')
            return '<li><a href="'. $this->anchorClass . ' ' . $this->baseURL . $count . '" class="pager-next active">'. $text .'</a></li>';
				
        $pageCount = $count?$count:0;
        $this->additionalParam = "{'page' : $pageCount, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'search3' : $('#".$this->searchVarible3."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "<li><a href=\"javascript:void(0);\" " . $this->anchorClass . "
				onclick=\"$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){
					   $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;\"  class=\"pager-next active\">"
			   . $text .'</a></li>';
	}

	function getAJAXlink4( $count, $text) {
        if( $this->contentDiv == '')
            return '<li><a href="'. $this->anchorClass . ' ' . $this->baseURL . $count . '" class="pager-next active">'. $text .'</a></li>';
				
        $pageCount = $count?$count:0;
        $this->additionalParam = "{'page' : $pageCount, 'content' : '".$this->contentDiv."', 'show' : ".$this->showRecord.", 'search' : $('#".$this->searchVarible."').val(), 'search2' : $('#".$this->searchVarible2."').val(), 'search3' : $('#".$this->searchVarible3."').val(), 'search4' : $('#".$this->searchVarible4."').val(), 'array_serialized' : '".$this->arrSerialized."' }";
		
	    return "<li><a href=\"javascript:void(0);\" " . $this->anchorClass . "
				onclick=\"$.post('". $this->baseURL."', ". $this->additionalParam .", function(data){
					   $('#". $this->contentDiv . "').html(data); }); window.scroll(0,0); return false;\"  class=\"pager-next active\">"
			   . $text .'</a></li>';
	}
}
?>