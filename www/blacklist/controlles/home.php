<?php 

class HomeController {
	public function __construct() {
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
	
	private function getRecords() {
		$db = Flight::db();
        $sql = "SELECT * FROM `ips_blacked` WHERE ?";
        $query = $db->query($sql, array(1), true);
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
	
	public function view() {
		
		Flight::view()->set('baseOfUrl', Flight::get('baseOfUrl'));
		
		Flight::view()->set('numOfIps', $this->getCount());
		
		Flight::view()->set('records', $this->getRecords());
		
		Flight::render('home');
	}
	
	public function ipHistory() {
		$ip = Flight::request()->data['ip'];
		$response = $this->getIpHistory($ip);
		Flight::json($response);
	}
}

?>