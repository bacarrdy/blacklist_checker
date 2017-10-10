<?php 

require_once THISDIR."modals/blacklist.php";
require_once THISDIR."modals/category.php";
require_once THISDIR."utils/pagination.php";
require_once THISDIR."utils/sorter.php";

class HomeController {
	
	private $_sort = 5,
			$_selectedCat = false,
			$compressedByIp = false;
	
	public function __construct() {
		$compressed = Flight::request()->query['compressed'];
		if (strtolower($compressed) == "true") {
			$this->compressedByIp = strtolower($compressed) == "true" ? true : false;
		}
		$selectedCat = Flight::request()->query['category'];
		if (!empty($selectedCat)) {
			$categoryModal = new CategoryModal();
			$categoryModal->id = $selectedCat;
			$response = $categoryModal->get();
			if ($response !== false) {
				$this->_selectedCat = $categoryModal;
				$this->selectedCatId = $categoryModal->id;
				$this->selectedCatName = $categoryModal->name;
			}
		}
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
	
	public function view() {
		
		$sorter = new Sorter(7);
		
		$paginationUtils = new PaginationUtils();
		$paginationUtils->recordsPerPage = 100;
		$paginationUtils->page = Flight::request()->query['page'];
		$paginationUtils->url = Flight::utils()->buildQueryExept("page");
		
		$blackList = new BlackList();
		$blackList->selectedCategory = $this->_selectedCat;
		$blackList->compressedByIp = $this->compressedByIp;
		$blackList->getCount();
		$paginationUtils->total = $blackList->total;
		$blackList->pagination = $paginationUtils;
		$blackList->sorter = $sorter;

		$blackList->getRecordsByFilter();
		
		$categoryModal = new CategoryModal();
		$categoryModal->isCategoryCollection = true;
		$categoryModal->getCount();		
		$categoryModal->getRecordsByFilter();
		
		Flight::view()->set('baseOfUrl', Flight::get('baseOfUrl'));

		Flight::view()->set('numOfIps', $blackList->total);
		
		Flight::view()->set('tx', $this);
		
		Flight::view()->set('sorter', $sorter);
		
		Flight::view()->set('selectCatLink', Flight::utils()->prepareUrlWithQuery(Flight::utils()->buildQueryExept("category")));
		Flight::view()->set('selectedCat', $this->_selectedCat);
		Flight::view()->set('categories', $categoryModal->categories);
		
		Flight::view()->set('compressedLink', Flight::utils()->prepareUrlWithQuery(Flight::utils()->buildQueryExept("compressed")));
		Flight::view()->set('compressedByIp', $this->compressedByIp);
		
		Flight::view()->set('records', $blackList->records);
		
		Flight::view()->set('pagination', $paginationUtils->build());
		
		Flight::render('home');
	}
	
	public function ipHistory() {
		$ip = Flight::request()->data['ip'];
		$response = $this->getIpHistory($ip);
		Flight::json($response);
	}
}

?>