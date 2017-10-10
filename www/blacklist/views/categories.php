
<?php require_once (__DIR__)."/head.php"; ?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
				
				
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">
									Categories (<?=$numOfCategories;?>)
									<button type="button" class="btn btn-default pull-right" data-toggle="modal" data-action-type="new" data-category-id="0" data-target="#categoryManager">New category</button>
								</h4>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
										<tr>
											<th>
												<a href="<?=$sorter->sortUrl( array(0,1) );?>">
													<?=$sorter->sortView( array(0,1) );?> Name
												</a>
											</th>
											<th>Range</th>
											<th>Actions</th>
										</tr>
                                    </thead>
                                    <tbody>
										<?php if ($numOfCategories > 0) { ?>
											<?php foreach($records as $record) { ?>
												<tr>
													<td><?=$record->name;?></td>
													<td>
														<?php if (!empty($record->ranges)) { ?>
															<?php echo implode(";", $record->ranges->mapRangeToView() ); ?>
														<?php }else{ ?>
															-
														<?php } ?>
													</td>
													<td>
														<div class="btn-group" role="group" aria-label="...">
															<button type="button" class="btn btn-default" data-toggle="modal" data-action-type="edit" data-category-id="<?=$record->id;?>" data-target="#categoryManager">Edit</button>
															<button type="button" class="btn btn-default" data-toggle="modal" data-action-type="del" data-category-id="<?=$record->id;?>" data-target="#categoryManager">Delete</button>
														</div>
													</td>
												</tr>
											<?php } ?>
										<?php } else { ?>
                                        <tr>
                                        	<td colspan=6 style="text-align: center;">No categories!</td>
                                        </tr>
										<?php } ?>
                                    </tbody>
                                </table>
								<?php if (!empty($pagination)) { ?>
									<?=$pagination;?>
								<?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>


<?php require_once (__DIR__)."/foot.php"; ?>

<style>
.loader {
	border: 16px solid #f3f3f3; /* Light grey */
	border-top: 16px solid #3498db; /* Blue */
	border-radius: 50%;
	width: 120px;
	height: 120px;
	animation: spin 2s linear infinite;
	margin: auto;
}

@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}
template {
	display: none;
}
</style>

<template class="ipRange">
	<div class="row">
		<div class="col-md-5">
			<div class="form-group">
				<label>IP range begin</label>
				<input type="text" name="rangeBegin[]" class="form-control border-input" placeholder="ex.: 127.0.0.1" value="">
			</div>
		</div>
		<div class="col-md-5">
			<div class="form-group">
				<label>IP range end</label>
				<input type="text" name="rangeEnd[]" class="form-control border-input" placeholder="ex.: 127.0.0.254" value="">
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label>Delete</label>
				<button type="button" class="btn btn-primary btn-remove-range">X</button>
			</div>
		</div>
	</div>
</template>

<template class="categoryCreate">
	<div class="alert alert-danger hide">
		<span><b> Error - </b><span></span></span>
	</div>
	<form onsubmit="return false;" autocomplete="off">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" class="form-control border-input" placeholder="Category name" value="">
				</div>
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col-md-12">
			<button type="button" class="btn btn-primary btn-add-range pull-right">Add new range</button>
		</div>
	</div>
</template>

<div class="modal fade" id="categoryManager" tabindex="-1" role="dialog" aria-labelledby="categoryManagerModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="categoryManagerModalLabel">Loading...</h4>
      </div>
      <div class="modal-body">
        <div class="loader"></div>
      </div>
      <div class="modal-footer hide">
        <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-create hide">Create</button>
		<button type="button" class="btn btn-primary btn-update hide">update</button>
		<button type="button" class="btn btn-primary btn-delete hide">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
function array_values (input) {
  var tmpArr = [];
  var key = '';
  if (input.length > 0) {
	  for (key in input) {
		tmpArr[tmpArr.length] = input[key];
	  }
  }
  return tmpArr;
}
$('body').on('click', '.btn-add-range', function() {
	var _template = $($("body").find("template.ipRange").html())
	$("#categoryManager").find(".modal-body").find("form").append(_template);
});
$('body').on('click', '.btn-remove-range', function() {
	var $_self = $(this);
	$_self.closest(".row").remove();
});
$(".btn-create").click(function() {
	var $_self = $(this);
	$_self.prop("disabled", true);
	var _name = $("#categoryManager").find(".modal-body").find('input[name="name"]').val();
	var _ranges = {};
	$("#categoryManager").find(".modal-body").find('input[name^="rangeBegin"]').map(function(i,e){ if (!_ranges[i]) { _ranges[i] = {}; } _ranges[i]['rangeBegin'] = $(this).val(); return $(this); });
	$("#categoryManager").find(".modal-body").find('input[name^="rangeEnd"]').map(function(i,e){ if (!_ranges[i]) { _ranges[i] = {}; } _ranges[i]['rangeEnd'] = $(this).val(); return $(this); });
	$("#categoryManager").find(".modal-body .alert").addClass("hide");
	$.post( "categories/create", { name: _name, ranges: _ranges }, function( _data ) {
		if (_data.status == true) {
			$( ".main-panel > .content" ).load( "categories .main-panel > .content > .container-fluid" );
			$('#categoryManager').modal('hide');
		}else{
			$("#categoryManager").find(".modal-body .alert").removeClass("hide");
			$("#categoryManager").find(".modal-body .alert span span").text(_data.response);
		}
		$_self.prop("disabled", false);
	}, "json")
	.fail(function() {
		$("#categoryManager").find(".modal-body .alert").removeClass("hide");
		$("#categoryManager").find(".modal-body .alert span span").text("Error creating category...");
		$_self.prop("disabled", false);
	});
});
$(".btn-update").click(function() {
	var $_self = $(this);
	$_self.prop("disabled", true);
	var _name = $("#categoryManager").find(".modal-body").find('input[name="name"]').val();
	var _ranges = [];
	$("#categoryManager").find(".modal-body").find('input[name^="rangeBegin"]').map(function(i,e){ if ($(this).data("id")) { return $(this); } if (!_ranges[i]) { _ranges[i] = {}; } _ranges[i]['rangeBegin'] = $(this).val(); return $(this); });
	$("#categoryManager").find(".modal-body").find('input[name^="rangeEnd"]').map(function(i,e){ if ($(this).data("id")) { return $(this); } _ranges[i]['rangeEnd'] = $(this).val(); return $(this); });
	$("#categoryManager").find(".modal-body .alert").addClass("hide");
	_ranges = array_values(_ranges);
	$("#categoryManager").find(".modal-body").find('input[name^="rangeBegin"]').map(function(i,e){ if (!$(this).data("id")) { return $(this); } i = $(this).data("id"); if (_ranges[i]) { _ranges.splice( i, 0, {} ); } if (!_ranges[i]) { _ranges[i] = {}; } _ranges[i]['rangeBegin'] = $(this).val(); return $(this); });
	$("#categoryManager").find(".modal-body").find('input[name^="rangeEnd"]').map(function(i,e){ if (!$(this).data("id")) { return $(this); } i = $(this).data("id"); _ranges[i]['rangeEnd'] = $(this).val(); return $(this); });
	$.post( "categories/update", { id: $_self.data("category-id"), name: _name, ranges: _ranges }, function( _data ) {
		if (_data.status == true) {
			$( ".main-panel > .content" ).load( "categories .main-panel > .content > .container-fluid" );
			$('#categoryManager').modal('hide');
		}else{
			$("#categoryManager").find(".modal-body .alert").removeClass("hide");
			$("#categoryManager").find(".modal-body .alert span span").html(_data.response);
		}
		$_self.prop("disabled", false);
	}, "json")
	.fail(function() {
		$("#categoryManager").find(".modal-body .alert").removeClass("hide");
		$("#categoryManager").find(".modal-body .alert span span").text("Error updating category...");
		$_self.prop("disabled", false);
	});
});
$(".btn-delete").click(function() {
	var $_self = $(this);
	$_self.prop("disabled", true);
	$("#categoryManager").find(".modal-body .alert").addClass("hide");
	$.post( "categories/delete", { id: $_self.data("category-id") }, function( _data ) {
		if (_data.status == true) {
			$( ".main-panel > .content" ).load( "categories .main-panel > .content > .container-fluid" );
			$('#categoryManager').modal('hide');
		}else{
			$("#categoryManager").find(".modal-body .alert").removeClass("hide");
			$("#categoryManager").find(".modal-body .alert span span").text(_data.response);
		}
		$_self.prop("disabled", false);
	}, "json")
	.fail(function() {
		$("#categoryManager").find(".modal-body .alert").removeClass("hide");
		$("#categoryManager").find(".modal-body .alert span span").text("Error deleting category...");
		$_self.prop("disabled", false);
	});
});
$('#categoryManager').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var _action = button.data('action-type');
	var _id = button.data('category-id');
	var modal = $(this);
	
	switch(_action) {
		case "edit": {
			modal.find('.modal-title').text("Edit category");
			modal.find('.modal-footer').removeClass('hide');
			modal.find('.modal-footer').find('.btn-update').removeClass('hide');
			modal.find('.modal-footer').find('.btn-update').data("category-id", _id);
			
			$.post( "categories/get", { id: _id }, function( _data ) {
				modal.find('.modal-body').html($("body").find("template.categoryCreate").html());
				modal.find(".modal-body .alert span span").text("");
				if (_data.status == true) {
					$("#categoryManager").find(".modal-body").find('input[name="name"]').val(_data.data.name);
					$.each(_data.data.ranges, function(k,v) {
						var _template = $($("body").find("template.ipRange").html())
						_template.find('input[name^="rangeBegin"]').data("id", v.id).val(v.rangeBegin);
						_template.find('input[name^="rangeEnd"]').data("id", v.id).val(v.rangeEnd);
						$("#categoryManager").find(".modal-body").find("form").append(_template);
					});
				}else{
					modal.find(".modal-body form").addClass("hide");
					modal.find('.modal-footer').find('.btn-update').addClass('hide');
					modal.find(".modal-body .alert").removeClass("hide");
					modal.find(".modal-body .alert span span").text(_data.response);
				}
			}, "json")
			.fail(function() {
				modal.find('.modal-body').html($("body").find("template.categoryCreate").html());
				modal.find(".modal-body .alert form").addClass("hide");
				modal.find('.modal-footer').find('.btn-update').addClass('hide');
				modal.find(".modal-body .alert").removeClass("hide");
				modal.find(".modal-body .alert span span").text("Error deleting category...");
			});
			
			break;
		}
		case "del": {
			modal.find('.modal-title').text("Delete category");
			modal.find('.modal-footer').removeClass('hide');
			modal.find('.modal-footer').find('.btn-delete').data("category-id", _id);
			modal.find('.modal-footer').find('.btn-delete').removeClass('hide');
			modal.find('.modal-body').html("Do you really wanna to delete this category?");
			break;
		}
		default: {
			modal.find('.modal-title').text("Create new category");
			modal.find('.modal-footer').removeClass('hide');
			modal.find('.modal-footer').find('.btn-create').removeClass('hide');
			modal.find('.modal-body').html($("body").find("template.categoryCreate").html());
			modal.find(".modal-body .alert span span").text("");
		}
	}
	/*
	setTimeout(function() {
		$.post( "ipHistory", { ip: _ip }, function( _data ) {
			console.log(_data);
			if (typeof _data == "string") {
				modal.find('.modal-body').fadeOut("slow", function() {
					modal.find('.modal-body').find('.loader').remove();
					modal.find('.modal-body').html(_data);
					modal.find('.modal-body').fadeIn("fast");
				});
				return;
			}
			if (_data.length == 0) {
				modal.find('.modal-body').fadeOut("slow", function() {
					modal.find('.modal-body').find('.loader').remove();
					modal.find('.modal-body').html(
					'<div class="alert alert-info">'+
						'<span><b> Info - </b> This IP hasn\'t history...</span>'+
					'</div>'
					);
					modal.find('.modal-body').fadeIn("fast");
				});
			}else{
				var template = 
						'<div class="card">'+
                            '<div class="content table-responsive table-full-width">'+
                                '<table class="table table-striped">'+
                                    '<thead>'+
										'<tr>'+
											'<th>#</th>'+
											'<th>Action</th>'+
											'<th>Date</th>'+
										'</tr>'+
                                    '</thead>'+
                                    '<tbody>'+
                                    '</tbody>'+
                                '</table>'+
							'</div>'+
						'</div>';
				template = $(template);
				$.each(_data, function(key, data){
					template.find("tbody").append($(
						'<tr>'+
							'<td>'+data.id+'</td>'+
							'<td>'+data.action+'</td>'+
							'<td>'+data.date+'</td>'+
						'</tr>'
					));
				});
				modal.find('.modal-body').fadeOut("slow", function() {
					modal.find('.modal-body').find('.loader').remove();
					modal.find('.modal-body').append(template);
					modal.find('.modal-body').fadeIn("fast");
				});
			}
		}, "json")
		.fail(function() {
			modal.find('.modal-body').fadeOut("slow", function() {
				modal.find('.modal-body').find('.loader').remove();
				modal.find('.modal-body').html(
				'<div class="alert alert-danger">'+
					'<span><b> Error - </b> While loading IP history...</span>'+
				'</div>'
				);
				modal.find('.modal-body').fadeIn("fast");
			});
		});
	}, 1000);
	*/
});
$('#categoryManager').on('hide.bs.modal', function (event) {
	var modal = $(this);
	modal.find('.modal-title').html('Loading...');
	modal.find('.modal-body').html('<div class="loader"></div>');
	modal.find('.modal-footer').addClass('hide');
	modal.find('.modal-footer').find('.btn-create').addClass('hide');
	modal.find('.modal-footer').find('.btn-update').addClass('hide');
	modal.find('.modal-footer').find('.btn-update').removeData("category-id");
	modal.find('.modal-footer').find('.btn-delete').addClass('hide');
	modal.find('.modal-footer').find('.btn-delete').removeData("category-id");
});
</script>
