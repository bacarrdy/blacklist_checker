<?php 

require_once THISDIR."modals/category.php";
require_once THISDIR."utils/pagination.php";

class CategoriesController {
	
	public function __construct() {

	}

	public function view() {
		
		$sorter = new Sorter(0);
		
		$paginationUtils = new PaginationUtils();
		$paginationUtils->recordsPerPage = 100;
		$paginationUtils->page = Flight::request()->query['page'];
		$paginationUtils->url = Flight::utils()->buildQueryExept("page");
		
		$categoryModal = new CategoryModal();
		$categoryModal->isCategoryCollection = true;
		$categoryModal->getCount();
		$paginationUtils->total = $categoryModal->total;
		$categoryModal->pagination = $paginationUtils;
		$categoryModal->sorter = $sorter;
		
		$categoryModal->getRecordsByFilter();
		
		Flight::view()->set('baseOfUrl', Flight::get('baseOfUrl'));
		
		Flight::view()->set('sorter', $sorter);
		
		Flight::view()->set('numOfCategories', $categoryModal->total);
		
		Flight::view()->set('tx', $this);
		
		Flight::view()->set('records', $categoryModal->categories);
		
		Flight::view()->set('pagination', $paginationUtils->build());
		
		Flight::render('categories');
	}
	
	public function create() {
		$name = Flight::request()->data['name'];
		$ranges = Flight::request()->data['ranges'];
		
		$categoryModal = new CategoryModal();
		$categoryModal->name = $name;
		
		$categoryRangesModal = new CategoryRangesModal();
		$categoryRangesModal->isRangeCollection = true;
		
		if (is_array($ranges)) {
			foreach($ranges as $range) {
				$categoryRangesModal->addRange($range['rangeBegin'], $range['rangeEnd']);
			}
		}
		
		$categoryModal->ranges = $categoryRangesModal;
		
		if (!$categoryModal->createUpdateValidator()) { 
			Flight::json(array("status" => false, "response" => $categoryModal->validatorError));	
			return; 
		}
		
		$response = $categoryModal->insert();
		
		Flight::json(array( "status" => $response, "response" => "Error creating category." . var_export($categoryModal, true) ));
	}	
	public function delete() {
		$id = Flight::request()->data['id'];
		if (empty($id)) {
			Flight::json(array("status" => false, "response" => "ID is invalid."));
			return;
		}
		$categoryModal = new CategoryModal();
		$categoryModal->id = $id;
		if (!$categoryModal->get()) {
			Flight::json(array("status" => false, "response" => "Category with this ID not found."));
			return;
		}
		$response = $categoryModal->delete();
		Flight::json(array("status" => $response, "response" => "Error deleting category."));
	}
	public function get() {
		$id = Flight::request()->data['id'];
		if (empty($id)) {
			Flight::json(array("status" => false, "response" => "ID is invalid."));
			return;
		}
		$categoryModal = new CategoryModal();
		$categoryModal->id = $id;
		$response = $categoryModal->get();
		Flight::json(array("status" => $response !== false ? true : false, "response" => "Category not found.", "data" => $categoryModal->toDataJson()));
	}
	public function update() {
		$id = Flight::request()->data['id'];
		$name = Flight::request()->data['name'];
		$ranges = Flight::request()->data['ranges'];
		if (empty($id)) {
			Flight::json(array("status" => false, "response" => "ID is invalid."));
			return;
		}
		
		$categoryModalUpdate = new CategoryModal();
		$categoryModalUpdate->name = $name;
		
		$categoryRangesModalUpdate = new CategoryRangesModal();
		$categoryRangesModalUpdate->isRangeCollection = true;
		
		if (is_array($ranges)) {
			foreach($ranges as $key => $range) {
				if (!is_array($range)) { continue; }
				$categoryRangesModalUpdate->addRange($range['rangeBegin'], $range['rangeEnd'], $key);
			}
		}
		
		$categoryModalUpdate->ranges = $categoryRangesModalUpdate;
	
		if (!$categoryModalUpdate->createUpdateValidator()) { 
			Flight::json(array("status" => false, "response" => $categoryModalUpdate->validatorError));	
			return; 
		}
		
		$categoryModal = new CategoryModal();
		$categoryModal->id = $id;
		$response = $categoryModal->get();
		if ($response === false) {
			Flight::json(array("status" => false, "response" => "ID not found."));
			return;
		}
		
		//Flight::json(array("status" => false, "response" => "ID not found. <pre>" . print_r($categoryModal, true) ." " . print_r($categoryModalUpdate, true) ." </pre>" ));
		//return;
		
		$response = $categoryModal->CheckForUpdate($categoryModalUpdate);
		if ($response === true) {
			Flight::json(array("status" => false, "response" => "No changes made..."));
		}
		
		$response = $categoryModal->update($categoryModalUpdate);
		Flight::json(array("status" => $response, "response" => "Error updating category."));
	}
}

?>