<?php 

require_once THISDIR."modals/blacklist.php";

class BlackList {
	
	public $pagination = false,
			$sorter = false,
			$total = 0,
			$records = array(),
			$selectedCategory = false,
			$search = false,
			$_noActionDays = false,
			$compressedByIp = false;
	
	private function sqlBySearch($prefix = "", $withGroup = true) {
		
		$arraysSql = array();
		$arraysVars = array();

		if (!empty($prefix)) {
			$prefix = "`".$prefix."`.";
		}
		
		if (!empty($this->search)) {
			$arraysSql[] = ' '.$prefix.'`ip` LIKE ? ';
			$arraysVars[] = $this->search;
		}
		
		if (!empty($this->selectedCategory)) {
			$tempArraySqls = array();
			foreach($this->selectedCategory->ranges->mapRangeToSql() as $sql) {
				$tempArraySqls[] = ' INET_ATON('.$prefix.'`ip`) '.$sql;
			}
			$arraysSql[] = " ( ".implode(" OR ", $tempArraySqls)." ) ";
			//$arraysVars[] = ;
		}
		
		if (empty($arraysSql)) {
			return array(
				'sql' => '?',
				'vars' => array(1)
			);
		}else{
			return array(
				'sql' => implode(" ", $arraysSql),
				'vars' => $arraysVars
			);
		}
	}
	
	public function getCount() {
		$db = Flight::db();
		$sqlData = $this->sqlBySearch("b");
		$sql = "SELECT ";
		if ($this->compressedByIp) {
			$sql .= " COUNT( DISTINCT(`b`.`ip`) ) as `total`";
		}else{
			$sql .= " COUNT(`b`.`id`) as `total`";
		}
		$sql .= " FROM `ips_blacked` `b` WHERE ".$sqlData['sql'];
        $query = $db->query($sql, array_merge($sqlData['vars'], array(  )) );
        if (!$db->checkDBResult($query)) {
			$this->total = 0;
            return $this;
        }
        if ($query){
			$this->total = $query['total'];
            return $this;
        }
		$this->total = 0;
		return $this;
	}
	
	public function isIpBlacklisted($ip) {
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
	
	public function getLastHistoryRecordDate($ip) {
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
	
	public function getIpLastOwner($ip) {
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
	
	public function calculateNoAction($ip) {
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
	
	public function getRecordsByFilter() {
		$db = Flight::db();
		$sqlData = $this->sqlBySearch("b");
		$sortOrder = "ORDER BY INET_ATON(`b`.`ip`) ASC";
		switch($this->sorter->_sort) {
			case 1: {
				$sortOrder = "ORDER BY INET_ATON(`b`.`ip`) DESC";
				break;
			}
			case 2: {
				if ($this->compressedByIp) {
					$sortOrder = "ORDER BY `listCount` ASC";
				}else{
					$sortOrder = "ORDER BY `b`.`listName` ASC";
				}
				break;
			}
			case 3: {
				if ($this->compressedByIp) {
					$sortOrder = "ORDER BY `listCount` DESC";
				}else{
					$sortOrder = "ORDER BY `b`.`listName` DESC";
				}
				break;
			}
			case 4: {
				$sortOrder = "ORDER BY `b`.`userID` ASC";
				break;
			}	
			case 5: {
				$sortOrder = "ORDER BY `b`.`userID` DESC";
				break;
			}
			case 6: {
				$sortOrder = "ORDER BY `b`.`date` ASC";
				break;
			}
			case 7: {
				$sortOrder = "ORDER BY `b`.`date` DESC";
				break;
			}
		}
		$sql = "SELECT `b`.*, `c`.`name` as `categoryName` ";
		if ($this->compressedByIp) {
			$sortOrder = " GROUP By `b`.`ip` ".$sortOrder;
			$sql .= " ,(SELECT COUNT(*) FROM `ips_blacked` `z` WHERE `z`.`ip` LIKE `b`.`ip`) as `listCount`";
		}
        $sql .= " FROM `ips_blacked` `b` LEFT JOIN `ips_categories_ranges` `r` ON INET_ATON(`b`.`ip`) BETWEEN `r`.`rangeBegin` AND `r`.`rangeEnd` LEFT JOIN `ips_categories` `c` ON `c`.`id` = `r`.`categoryID` WHERE ".$sqlData['sql']." ".$sortOrder." LIMIT ?,?";
        $query = $db->query($sql, array_merge($sqlData['vars'], array($this->pagination->getStartPoint(), $this->pagination->recordsPerPage)), true);
		$tmp = $query;
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			foreach($query as $row) {
				array_push($this->records, array(
					'id' => $row['id'],
					'ip' => $row['ip'],
					'in' => $row['listName'].(isset($row['listCount']) ? " (".$row['listCount'].")":''),
					'categoryName' => !empty($row['categoryName']) ? $row['categoryName'] : '-',
					'userID' => !is_null($row['userID']) ? $row['userID'] : 0,
					'url' => !is_null($row['url']) ? $row['url'] : '',
					'date' => $row['date']
				));
			}
            return true;
        }
        return false;
		
	}
	
}

?>