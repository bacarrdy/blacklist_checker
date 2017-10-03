<?php 

class SearchController {
	
	private $sortCfg = array(
		0 => 'ip_asc',
		1 => 'ip_desc',
		2 => 'userID_asc',
		3 => 'userID_desc',
		4 => 'date_asc',
		5 => 'date_desc'
	);
	
	private $_sort = 5;
	
	private $_searchActive = false;
	
	private $_ip = false;
	
	private $_noActionDays = false;
	
	public function __construct() {
		$this->_sort = Flight::request()->query['sort'];
		
		if (!is_numeric($this->_sort)) {
			$this->_sort = 5;
		}
		
		$_searchActive = Flight::request()->query['search'];
		
		$_noActionDays = Flight::request()->query['noActionDays'];
		if (is_numeric($_noActionDays)) {
			$this->_noActionDays = $_noActionDays;
		}
		
		$_ip = Flight::request()->query['ip'];
		
		if (!empty($_ip)) {
			$this->_ip = $_ip;
			$this->_ip = str_replace("*", "%", $this->_ip);
		}
		
		if ($_searchActive == "true") {
			$this->_searchActive = true;
		}
	}
	
	private function getCount() {
		$db = Flight::db();
        $sql = "SELECT `ip` FROM `ips_history` WHERE `ip` LIKE ? GROUP By `ip`";
        $query = $db->query($sql, array( $this->_ip  ), true);
        if (!$db->checkDBResult($query)) {
            return 0;
        }
        if ($query){
            return count($query);
        }
        return 0;
	}
	
	private function isIpBlacklisted($ip) {
		$db = Flight::db();
        $sql = "SELECT `ip` FROM `ips_blacked` WHERE `ip` LIKE ? GROUP By `ip`";
        $query = $db->query($sql, array( $ip  ), true);
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
            return count($query) > 0;
        }
        return false;
	}
	
	private function getLastHistoryRecordDate($ip) {
		$db = Flight::db();
        $sql = "SELECT `date` FROM `ips_history` WHERE `date` IS NOT NULL AND `date` NOT LIKE '' AND `ip` LIKE ? GROUP By `date` ORDER By `id` DESC LIMIT 1";
        $query = $db->query($sql, array( $ip ));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
            return $query['date'];
        }
        return true;
	}
	
	private function getIpLastOwner($ip) {
		$db = Flight::db();
        $sql = "SELECT `userID` FROM `ips_history` WHERE `userID` IS NOT NULL AND `userID` NOT LIKE '' AND `ip` LIKE ? GROUP By `userID` ORDER By `id` DESC LIMIT 1";
        $query = $db->query($sql, array( $ip ));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
            return $query['userID'];
        }
        return true;
	}
	
	private function calculateNoAction($ip) {
		$date = $this->getLastHistoryRecordDate($ip);
		try {
			$first_date = new DateTime(date('Y-m-d', time()));
			$second_date = new DateTime(date('Y-m-d', strtotime($date)));

			$difference = $first_date->diff($second_date);

			if ($difference->invert == 0 && intval($difference->format("%a")) == 0)
				return false;
			if (intval($difference->format("%a")) >= $this->_noActionDays) 
				return true;
		}catch(Exeption $e) {
		}
		return false;
	}
	
	private function getRecords($page=1, $recordsPerPage=10) {
		$db = Flight::db();
		$startpoint = ($page * $recordsPerPage) - $recordsPerPage;
		$sortOrder = "ORDER BY `ip` ASC";
		switch($this->_sort) {
			case 1: {
				$sortOrder = "ORDER BY `ip` DESC";
				break;
			}
			case 2: {
				$sortOrder = "ORDER BY `userID` ASC";
				break;
			}
			case 3: {
				$sortOrder = "ORDER BY `userID` DESC";
				break;
			}
			case 4: {
				$sortOrder = "ORDER BY `date` ASC";
				break;
			}
			case 5: {
				$sortOrder = "ORDER BY `date` DESC";
				break;
			}
		}
        $sql = "SELECT `ip` FROM `ips_history` WHERE `ip` LIKE ? GROUP By `ip` ".$sortOrder." LIMIT ?,?";
        $query = $db->query($sql, array($this->_ip, $startpoint, $recordsPerPage), true);
        if (!$db->checkDBResult($query)) {
            return array();
        }
        if ($query){
			$return = array();
			foreach($query as $row) {
				$return[] = array(
					'ip' => $row['ip'],
					'inBlackList' => $this->isIpBlacklisted($row['ip']),
					'lastDate' => $this->getLastHistoryRecordDate($row['ip']),
					'noAction' => $this->calculateNoAction($row['ip']),
					'lastOwner' => $this->getIpLastOwner($row['ip'])
				);
			}
            return $return;
        }
        return array();
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
			if ($sortCfg == array(0,1)) {
				$url .= 0;
			}else if ($sortCfg == array(2,3)) {
				$url .= 2;
			}else{
				$url .= 4;
			}
		}else{
			if (in_array($this->_sort, array(0,2,4))) {
				$url .= $this->_sort+1;
			}else{
				$url .= $this->_sort-1;
			}
		}
		return $url;
	}
	
	public function sortView($sortCfg) {
		if (!in_array($this->_sort, $sortCfg)) {
			return '<span class="ti-arrows-vertical"></span>';
		}
		if (in_array($this->_sort, array(0,2,4))) {
			return '<span class="ti-arrow-up"></span>';
		}
		return '<span class="ti-arrow-down"></span>';
	}
	
	public function view() {
		
		Flight::view()->set('baseOfUrl', Flight::get('baseOfUrl'));

		Flight::view()->set('searchActive', $this->_searchActive);
		Flight::view()->set('tx', $this);
		
		if ($this->_searchActive) {
		
			$numOfRecords = $this->getCount();
			Flight::view()->set('numOfIps', $numOfRecords);
			
			$page = Flight::request()->query['page'];
			
			if (!is_numeric($page)) {
				$page = 1;
			}
			
			$recordsPerPage = 100;
			
			Flight::view()->set('records', $this->getRecords($page, $recordsPerPage));
			
			Flight::view()->set('pagination', Flight::utils()->pagination($numOfRecords, $recordsPerPage, $page, Flight::utils()->buildQueryExept("page")));
			
		}else{
			Flight::view()->set('numOfIps', 0);
			Flight::view()->set('records', array());
			Flight::view()->set('pagination', false);
		}
		
		Flight::render('search');
	}
}

?>