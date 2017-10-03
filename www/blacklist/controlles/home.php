<?php 

class HomeController {
	
	private $sortCfg = array(
		0 => 'ip_asc',
		1 => 'ip_desc',
		2 => 'userID_asc',
		3 => 'userID_desc',
		4 => 'date_asc',
		5 => 'date_desc'
	);
	
	private $_sort = 5;
	
	public function __construct() {
		$this->_sort = Flight::request()->query['sort'];
		
		if (!is_numeric($this->_sort)) {
			$this->_sort = 5;
		}
	}
	
	private function getCount() {
		$db = Flight::db();
        $sql = "SELECT COUNT(`id`) as total FROM `ips_blacked` WHERE ?";
        $query = $db->query($sql, array(1));
        if (!$db->checkDBResult($query)) {
            return 0;
        }
        if ($query){
            return $query['total'];
        }
        return 0;
	}
	
	private function getIpHistory($ip) {
		$db = Flight::db();
        $sql = "SELECT * FROM `ips_history` WHERE `ip` LIKE ? ORDER By date DESC";
        $query = $db->query($sql, array($ip), true);
        if (!$db->checkDBResult($query)) {
            return array();
        }
        if ($query){
			$return = array();
			foreach($query as $row) {
				$return[] = array(
					'id' => $row['id'],
					'ip' => $row['ip'],
					'userID' => !is_null($row['userID']) ? $row['userID'] : 0,
					'action' => $row['action'],
					'date' => $row['date']
				);
			}
            return $return;
        }
        return array();
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
        $sql = "SELECT * FROM `ips_blacked` WHERE ? ".$sortOrder." LIMIT ?,?";
        $query = $db->query($sql, array(1, $startpoint, $recordsPerPage), true);
        if (!$db->checkDBResult($query)) {
            return array();
        }
        if ($query){
			$return = array();
			foreach($query as $row) {
				$return[] = array(
					'id' => $row['id'],
					'ip' => $row['ip'],
					'in' => $row['listName'],
					'userID' => !is_null($row['userID']) ? $row['userID'] : 0,
					'url' => !is_null($row['url']) ? $row['url'] : '',
					'date' => $row['date']
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
		
		$numOfRecords = $this->getCount();
		Flight::view()->set('numOfIps', $numOfRecords);
		
		$page = Flight::request()->query['page'];
		
		if (!is_numeric($page)) {
			$page = 1;
		}
		
		$recordsPerPage = 100;
		
		Flight::view()->set('tx', $this);
		
		Flight::view()->set('records', $this->getRecords($page, $recordsPerPage));
		
		Flight::view()->set('pagination', Flight::utils()->pagination($numOfRecords, $recordsPerPage, $page, Flight::utils()->buildQueryExept("page")));
		
		Flight::render('home');
	}
	
	public function ipHistory() {
		$ip = Flight::request()->data['ip'];
		$response = $this->getIpHistory($ip);
		Flight::json($response);
	}
}

?>