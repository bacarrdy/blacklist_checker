<?php 

require_once THISDIR."modals/blacklist.php";
require_once THISDIR."utils/pagination.php";
require_once THISDIR."utils/sorter.php";

class SearchController {
	
	private $_searchActive = false;
	
	private $_ip = false;
	
	private $_noActionDays = false;
	
	public function __construct() {
		
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
	
	public function view() {
		
		$sorter = new Sorter(5);
		
		Flight::view()->set('sorter', $sorter);
		
		Flight::view()->set('baseOfUrl', Flight::get('baseOfUrl'));

		Flight::view()->set('searchActive', $this->_searchActive);
		Flight::view()->set('tx', $this);
		
		if ($this->_searchActive) {
		
			$paginationUtils = new PaginationUtils();
			$paginationUtils->recordsPerPage = 100;
			$paginationUtils->page = Flight::request()->query['page'];
			$paginationUtils->url = Flight::utils()->buildQueryExept("page");
			
			$blackList = new BlackList();
			$blackList->search = $this->_ip;
			$blackList->_noActionDays = $this->_noActionDays;
			$blackList->getCount();
			$paginationUtils->total = $blackList->total;
			$blackList->pagination = $paginationUtils;
			$blackList->sorter = $sorter;

			$blackList->getRecordsByFilter();
			
			foreach($blackList->records as $rID => $record) {
				$record['inBlackList'] = $blackList->isIpBlacklisted($record['ip']);
				$record['lastDate'] = $blackList->getLastHistoryRecordDate($record['ip']);
				$record['noAction'] = $blackList->calculateNoAction($record['ip']);
				$record['lastOwner'] = $blackList->getIpLastOwner($record['ip']);
				$blackList->records[$rID] = $record;
			}

			Flight::view()->set('numOfIps', $blackList->total);
			
			Flight::view()->set('records', $blackList->records);
			
			Flight::view()->set('pagination', $paginationUtils->build());
			
		}else{
			Flight::view()->set('numOfIps', 0);
			Flight::view()->set('records', array());
			Flight::view()->set('pagination', false);
		}
		
		Flight::render('search');
	}
}

?>