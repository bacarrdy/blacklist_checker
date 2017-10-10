<?php

require_once THISDIR."modals/categoryRanges.php";

class CategoryModal {
	public $id = 0,
			$name = false,
			$ranges = false,
			$validatorError = false,
			$pagination = false, 
			$total = 0,
			$isCategoryCollection = false,
			$categories = array(),
			$sorter = false;
			
	public function createUpdateValidator() {
		if (empty($this->name)) {
			$this->validatorError = "Name can't be blank.";
			return false;
		}
		if (empty($this->ranges)) {
			$this->validatorError = "IP Range is required.";
			return false;
		}
		if (!$this->ranges->createUpdateValidator()) {
			$this->validatorError = $this->ranges->validatorError;
			return false;
		}
		return true;
	}
	
	public function getCount() {
		$db = Flight::db();
        $sql = "SELECT COUNT(`id`) as total FROM `ips_categories` WHERE ?";
        $query = $db->query($sql, array(1));
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
	
	public function getRecordsByFilter() {
		$db = Flight::db();
		$sortOrder = "ORDER BY `name` ASC";
		if (!empty($this->sorter)) {
			switch($this->sorter->_sort) {
				case 1: {
					$sortOrder = "ORDER BY `name` DESC";
					break;
				}
			}
		}
        $sql = "SELECT * FROM `ips_categories` WHERE ? ".$sortOrder." ";
		$sqlVars = array(1);
		if (!empty($this->pagination)) {
			$sql .= " LIMIT ?,?";
			$sqlVars[] = $this->pagination->getStartPoint();
			$sqlVars[] = $this->pagination->recordsPerPage;
		}
        $query = $db->query($sql, $sqlVars, true);
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			foreach($query as $row) {
				$categoryModal = new CategoryModal();
				$categoryModal->id = $row['id'];
				$categoryModal->get();
				array_push($this->categories, $categoryModal);
			}
            return true;
        }
        return false;
	}
	
	public function insert() {
		$db = Flight::db();
        $sql = "INSERT INTO `ips_categories` (`name`) VALUES (?)";
        $query = $db->query($sql, array($this->name));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			$categoryID = $db->getInsertID();
			$this->ranges->categoryID = $categoryID;
			return $this->ranges->insert();
        }
        return false;
	}	
	
	public function get() {
		$db = Flight::db();
        $sql = "SELECT * FROM `ips_categories` WHERE `id` = ?";
        $query = $db->query($sql, array($this->id));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			$this->name = $query['name'];
			$categoryRangesModal = new CategoryRangesModal();
			$categoryRangesModal->categoryID = $this->id;
			$categoryRangesModal->getByCategory();
			$this->ranges = $categoryRangesModal;
			return true;
        }
        return false;
	}
	
	public function delete() {
		$db = Flight::db();
        $sql = "DELETE FROM `ips_categories` WHERE `id` = ?";
        $query = $db->query($sql, array($this->id));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			if (!empty($this->ranges)) {
				$this->ranges->delete();
			}
            return true;
        }
        return false;
	}	
	
	public function CheckForUpdate($categoryModalUpdate) {
		if ($categoryModalUpdate->name != $this->name) {
			return false;
		}
		if ($this->ranges->CheckForUpdate($categoryModalUpdate->ranges) === false) {
			return false;
		}
		return true;
	}
	
	public function update($categoryModalUpdate) {
		$db = Flight::db();
        $sql = "UPDATE `ips_categories` SET `name` = ? WHERE `id` = ?";
        $query = $db->query($sql, array($categoryModalUpdate->name, $this->id));
        if (!$db->checkDBResult($query)) {
            return false;
        }
        if ($query){
			$this->name = $categoryModalUpdate->name;
			if (!empty($this->ranges)) {
				$this->ranges->update($categoryModalUpdate->ranges);
			}
			return true;
        }
		if (!empty($this->ranges)) {
			return $this->ranges->update($categoryModalUpdate->ranges);
		}
        return false;
	}
	
	public function toDataJson(){
		if ($this->isCategoryCollection) {
			$array = array();
			foreach($this->categories as $category) {
				array_push($array, $category->toDataJson());
			}
			return $array;
		}else{
			return array(
				"id" => $this->id,
				"name" => $this->name,
				"ranges" => !empty($this->ranges) ? $this->ranges->toDataJson() : array()
			);
		}
	}
}

?>