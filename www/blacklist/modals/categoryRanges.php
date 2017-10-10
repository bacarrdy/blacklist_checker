<?php

class CategoryRangesModal {
	public $id = 0,
			$rangeBegin = false,
			$rangeEnd = false,
			$ranges = array(),
			$isRangeCollection = false,
			$validatorError = false,
			$categoryID = 0;
			
	public function createUpdateValidator() {
		if ($this->isRangeCollection) {
			if (empty($this->ranges) || !is_array($this->ranges)) {
				$this->validatorError = "IP Range is required.";
				return false;
			}
			foreach($this->ranges as $range) {
				if (!$range->createUpdateValidator()) {
					$this->validatorError = $range->validatorError;
					return false;
				}		
			}
		}else{
			if (empty($this->rangeBegin) || empty($this->rangeEnd)) {
				$this->validatorError = "IP Range is invalid.";
				return false;
			}
			if (!filter_var($this->rangeBegin, FILTER_VALIDATE_IP) || !filter_var($this->rangeEnd, FILTER_VALIDATE_IP)) {
				$this->validatorError = "IP Range is invalid.";
				return false;
			}
		}
		return true;
	}
	
	public function addRange($rangeBegin, $rangeEnd, $key = 0) {
		$categoryRangesModal = new CategoryRangesModal();
		$categoryRangesModal->rangeBegin = $rangeBegin;
		$categoryRangesModal->rangeEnd = $rangeEnd;
		if ($key > 0) {
			$categoryRangesModal->id = $key;
		}
		array_push($this->ranges, $categoryRangesModal);
		return $this;
	}
	
	public function insert() {
		if ($this->isRangeCollection) {
			foreach($this->ranges as $range) {
				$range->categoryID = $this->categoryID;
				if (!$range->insert()) {
					return false;
				}		
			}
			return true;
		}else{
			$db = Flight::db();
			$sql = "INSERT INTO `ips_categories_ranges` (`categoryID`,`rangeBegin`,`rangeEnd`) VALUES (?,?,?)";
			$query = $db->query($sql, array($this->categoryID, ip2long($this->rangeBegin), ip2long($this->rangeEnd)));
			if (!$db->checkDBResult($query)) {
				return false;
			}
			if ($query){
				return true;
			}
			return false;
		}
	}
	
	public function get() {
		$db = Flight::db();
        $sql = "SELECT * FROM `ips_categories_ranges` WHERE `id` = ?";
        $query = $db->query($sql, array($this->id));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			$this->categoryID = $query['categoryID'];
			$this->rangeBegin = long2ip($query['rangeBegin']);
			$this->rangeEnd = long2ip($query['rangeEnd']);
			return true;
        }
        return false;
	}
	
	public function getByCategory() {
		$this->isRangeCollection = true;
		$db = Flight::db();
        $sql = "SELECT `id` FROM `ips_categories_ranges` WHERE `categoryID` = ?";
        $query = $db->query($sql, array($this->categoryID), true);
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			foreach($query as $row) {
				$categoryRangesModal = new CategoryRangesModal();
				$categoryRangesModal->id = $row['id'];
				if ($categoryRangesModal->get()) {
					array_push($this->ranges, $categoryRangesModal);
				}
			}
			return true;
        }
        return false;
	}
	
	public function delete() {
		if ($this->isRangeCollection) {
			foreach($this->ranges as $range) {
				if (!$range->delete()) {
					return false;
				}		
			}
			return true;
		}else{
			$db = Flight::db();
			$sql = "DELETE FROM `ips_categories_ranges` WHERE `id` = ?";
			$query = $db->query($sql, array($this->id));
			if (!$db->checkDBResult($query)) {
				return false;
			}
			if ($query){
				return true;
			}
			return false;
		}
	}	
	
	public function findRange($id) {
		if ($this->isRangeCollection) {
			foreach($this->ranges as $range) {
				if ($range->findRange($id) !== false) {
					return $range;
				}		
			}
			return false;
		}else{
			return $this->id == $id ? $this : false;
		}
	}
	
	public function update($categoryRangesModalUpdate) {
		if ($this->isRangeCollection) {
			$response = false;
			foreach($this->ranges as $range) {
				if ($range->id > 0) {
					if ($categoryRangesModalUpdate->findRange($range->id) !== false) {
						if ($range->update($categoryRangesModalUpdate)) {
							$response = true;
						}		
					}else{
						if ($range->delete()) {
							$response = true;
						}
					}
				}
			}
			if ($categoryRangesModalUpdate->isRangeCollection) {
				foreach($categoryRangesModalUpdate->ranges as $range) {
					$categoryRangesModalTemp = new CategoryRangesModal();
					$categoryRangesModalTemp->isRangeCollection = true;
					$categoryRangesModalTemp->categoryID = $this->categoryID;
					if ($this->findRange($range->id) === false) {
						$categoryRangesModalTemp->addRange($range->rangeBegin, $range->rangeEnd);
					}		
					if (!empty($categoryRangesModalTemp->ranges)) {
						if ($categoryRangesModalTemp->insert()) {
							$response = true;
						}
					}
				}
			}
			return $response;
		}else{
			if ($this->id <= 0) {
				return false;
			}
			$range = $categoryRangesModalUpdate->findRange($this->id);
			if ($range === false) {
				return false;
			}
			$db = Flight::db();
			$sql = "UPDATE `ips_categories_ranges` SET `rangeBegin` = ?,`rangeEnd` = ? WHERE `id` = ?";
			$query = $db->query($sql, array(ip2long($range->rangeBegin), ip2long($range->rangeEnd), $this->id));
			if (!$db->checkDBResult($query)) {
				return false;
			}
			if ($query){
				return true;
			}
			return false;
		}
	}
	
	public function CheckForUpdate($categoryRangesModalUpdate) {
		if ($this->isRangeCollection) {
			foreach($this->ranges as $range) {
				if ($range->CheckForUpdate($categoryRangesModalUpdate) === false) {
					return false;
				}		
			}
			if ($categoryRangesModalUpdate->isRangeCollection) {
				foreach($categoryRangesModalUpdate->ranges as $range) {
					if ($this->findRange($range->id) === false) {
						return false;
					}		
				}
			}
			return true;
		}else{
			if ($this->id > 0) {
				$range = $categoryRangesModalUpdate->findRange($this->id);
				if ($range === false) {
					return false;
				}
				if ($range->rangeBegin != $this->rangeBegin || $range->rangeEnd != $this->rangeEnd) {
					return false;
				}
			}
			return true;
		}
	}
	
	public function toDataJson() {
		if ($this->isRangeCollection) {
			$array = array();
			foreach($this->ranges as $range) {
				array_push($array, $range->toDataJson());
			}
			return $array;
		}else{
			return array(
				"id" => $this->id,
				"rangeBegin" => $this->rangeBegin,
				"rangeEnd" => $this->rangeEnd
			);
		}
	}
	
	public function mapRangeToView() {
		if ($this->isRangeCollection) {
			$array = array();
			foreach($this->ranges as $range) {
				array_push($array, $range->mapRangeToView());
			}
			return $array;
		}else{
			return $this->rangeBegin."-".$this->rangeEnd;
		}
	}
	
	public function mapRangeToSql() {
		if ($this->isRangeCollection) {
			$array = array();
			foreach($this->ranges as $range) {
				array_push($array, $range->mapRangeToSql());
			}
			return $array;
		}else{
			return "BETWEEN ".ip2long($this->rangeBegin)." AND ".ip2long($this->rangeEnd);
		}
	}
}

?>