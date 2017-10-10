<?php 

class Sorter {
	public $_sort;

	public function __construct($default = 0) {
		$this->_sort = Flight::request()->query['sort'];
		
		if (!is_numeric($this->_sort) || $this->_sort < 0) {
			$this->_sort = $default;
		}
	}
	
	public function sortUrl($sortCfg) {
		$url = Flight::utils()->buildQueryExept("sort");
		if (strpos($url, "?") === false) {
			$url .= "?";
		}else{
			$url .= "&";
		}
		$url .= "sort=";
		if (!in_array($this->_sort, $sortCfg)) {
			$url .= $sortCfg[0];
		}else{
			if ($this->_sort % 2) {
				$url .= $this->_sort-1;
			}else{
				$url .= $this->_sort+1;
			}
		}
		return $url;
	}
	
	public function sortView($sortCfg) {
		if (!in_array($this->_sort, $sortCfg)) {
			return '<span class="ti-arrows-vertical"></span>';
		}
		if ($this->_sort % 2) {
			return '<span class="ti-arrow-up"></span>';
		}
		return '<span class="ti-arrow-down"></span>';
	}
}

?>