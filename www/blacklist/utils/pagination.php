<?php

class PaginationUtils {
	public $page = 1,
			$recordsPerPage = 10,
			$url = false,
			$total = 0;
	public function testPage() {
		if (!is_numeric($this->page)) {
			$this->page = 1;
		}
		if ($this->url === false) {
			$this->url = "?";
		}
	}
	public function getStartPoint() {
		$this->testPage();
		return ($this->page * $this->recordsPerPage) - $this->recordsPerPage;
	}
	public function build() {
		$this->testPage();
		$total = $this->total;
		$per_page = $this->recordsPerPage;
		$page = $this->page;
		$url = $this->url;
		if ($total == 0) { return ""; }
		if (strpos($url, "?") === false) {
			$url .= "?";
		}else{
			$url .= "&";
		}
		$adjacents = "2"; 
		  
		$prevlabel = "&lsaquo; Prev";
		$nextlabel = "Next &rsaquo;";
		$lastlabel = "Last &rsaquo;&rsaquo;";
		  
		$page = ($page == 0 ? 1 : $page);  
		$start = ($page - 1) * $per_page;                               
		  
		$prev = $page - 1;                          
		$next = $page + 1;
		  
		$lastpage = ceil($total/$per_page);
		  
		$lpm1 = $lastpage - 1; // //last page minus 1
		  
		$pagination = "";
		if($lastpage > 1){   
			$pagination .= "<ul class='pagination'>";
			$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
				  
				if ($page > 1) $pagination.= "<li><a href='{$url}page={$prev}'>{$prevlabel}</a></li>";
				  
			if ($lastpage < 7 + ($adjacents * 2)){   
				for ($counter = 1; $counter <= $lastpage; $counter++){
					if ($counter == $page)
						$pagination.= "<li><a class='current'>{$counter}</a></li>";
					else
						$pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
				}
			  
			} elseif($lastpage > 5 + ($adjacents * 2)){
				  
				if($page < 1 + ($adjacents * 2)) {
					  
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
						if ($counter == $page)
							$pagination.= "<li><a class='current'>{$counter}</a></li>";
						else
							$pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
					}
					$pagination.= "<li class='dot'>...</li>";
					$pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
					$pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";  
						  
				} elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
					  
					$pagination.= "<li><a href='{$url}page=1'>1</a></li>";
					$pagination.= "<li><a href='{$url}page=2'>2</a></li>";
					$pagination.= "<li class='dot'>...</li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
						if ($counter == $page)
							$pagination.= "<li><a class='current'>{$counter}</a></li>";
						else
							$pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
					}
					$pagination.= "<li class='dot'>..</li>";
					$pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
					$pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";      
					  
				} else {
					  
					$pagination.= "<li><a href='{$url}page=1'>1</a></li>";
					$pagination.= "<li><a href='{$url}page=2'>2</a></li>";
					$pagination.= "<li class='dot'>..</li>";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
						if ($counter == $page)
							$pagination.= "<li><a class='current'>{$counter}</a></li>";
						else
							$pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
					}
				}
			}
			  
				if ($page < $counter - 1) {
					$pagination.= "<li><a href='{$url}page={$next}'>{$nextlabel}</a></li>";
					$pagination.= "<li><a href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
				}
			  
			$pagination.= "</ul>";        
		}
		  
		return $pagination;
	}
}

?>