
$(document).ready(function() {

	/**
	 * Restore normal table display (non-edit mode)
	 */
	function normal() {
		$("span.text").show();
		$("span.button").show();
		$("span.edit").hide();
		$("table tr").bind('click', edit);
		// make sure last row is editable for adding new stuff
	}

	/**
	 * Click to edit handler
	 */
	function edit(event) {
		normal();
		console.log("clicky");
		$("span.text", this).hide();
		$("span.button", this).hide();
		$("span.edit", this).show();
		return true;
	}

	/**
	 * Needed
	 */
	$("td form span.button input.needed").click(function() {
		console.log("needed");
		var form = $(this).closest("form");
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: form.serialize()+"&mode=needed", // serializes the form's elements.
			success: function(data) {
				console.log("received: data="+JSON.stringify(data));
				$("td#"+data.id+" li.needed span.text").text(data.needed);
				$("td#"+data.id+" li.needed input.needed").val(data.needed);
				normal();
			}
		});
		return false;
	});

	/**
	 * Built
	 */
	$("td form span.button input.built").click(function() {
		console.log("built");
		var form = $(this).closest("form");
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: form.serialize()+"&mode=built", // serializes the form's elements.
			success: function(data) {
				console.log("received: data="+JSON.stringify(data));
				$("td#"+data.id+" li.inventory span.text").text(data.inventory);				
				$("td#"+data.id+" li.inventory input.inventory").val(data.inventory);				
				$("td#"+data.id+" li.needed span.text").text(data.needed);
				$("td#"+data.id+" li.needed input.needed").val(data.needed);
				normal();
			}
		});
		return false;
	});
	 
	/**
	 * Sold
	 */
	$("td form span.button input.sold").click(function() {
		console.log("sold");
		var form = $(this).closest("form");
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: form.serialize()+"&mode=sold", // serializes the form's elements.
			success: function(data) {
				console.log("received: data="+JSON.stringify(data));
				$("td#"+data.id+" li.inventory span.text").text(data.inventory);				
				$("td#"+data.id+" li.inventory input.inventory").val(data.inventory);				
				$("td#"+data.id+" li.sold span.text").text(data.sold);
				$("td#"+data.id+" li.sold input.sold").val(data.sold);
				normal();
			}
		});
		return false;
	});

	/**
	 * Received
	 */
	$("td form span.button input.received").click(function() {
		console.log("received");
		var form = $(this).closest("form");
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: form.serialize()+"&mode=received", // serializes the form's elements.
			success: function(data) {
				console.log("received: data="+JSON.stringify(data));
				$("td#"+data.id+" li.ordered span.text").text(data.ordered);
				$("td#"+data.id+" li.ordered input.ordered").val(data.ordered);
				$("td#"+data.id+" li.inventory span.text").text(data.inventory);				
				$("td#"+data.id+" li.inventory input.inventory").val(data.inventory);				
				normal();
			}
		});
		return false;
	});

	/**
	 * Ordered
	 */
	$("td form span.button input.ordered").click(function() {
		console.log("ordered");
		return false;
	});
	
	/**
	 * Form delete click handler
	 */
	$("td form input.delete").click(function() {
		var form = $(this).closest("form");
		var row = $(this).closest("td");
		var container = $(this).closest("ul");
		console.log("delete "+form.serialize());
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			// Can I use $(this).id or something to change mode to add / update ?
			data: form.serialize()+"&mode=delete", // serializes the form's elements.
			success: function(data) {
				console.log("form.edit submit success: data="+JSON.stringify(data));
				// need to remove row at this point
				row.slideUp(300, function() {
					row.remove();
				});
				normal();
			}
		});
		return false;
	 });

	/**
	 * Handle adds, reloads window until I can figure out a better way...
	 */
	function addHandler(data) {
		console.log("form.edit submit success: data="+JSON.stringify(data));
		// reload so we display the new row. This isn't AJAXy. Oh well.
		window.location.reload();
		normal();
	}

	/**
	 * Handle updates, reloads window until I can figure out a better way...
	 */
	function updateHandler(data) {
		console.log("form.edit submit success: data="+JSON.stringify(data));
		// might be better to animate...
		// loop thru data and set form text values
		// The problem here is that some of these values have to be translated
		// maybe we could pass the data over to the render php to render a single row
		$("td#"+data.id).css('background-color', 'Green');
		$.each(data, function(col, val) {
			$("td#"+data.id+" li."+col+" span.text").text(val);
		});
		// best to just reload the damn data
		window.location.reload();
		normal();
	}

	/**
	 * Form add submit handler
	 */
	$("tbody tr td form").submit(function () {
		var tr = $(this).closest("tr");
		var mydata = $(this).serialize();
		var myhandler;
		
		// id=last if we're adding a new entry
		if (tr.attr('id') == "last") {
			mydata += "&mode=add";
			myhandler = addHandler;
		} else {
			mydata += "&mode=update";
			myhandler = updateHandler;
		}
		console.log(mydata);
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: mydata, // serializes the form's elements.
			success: myhandler
		});

		return false;
	});

	/**
	 * BOM select
	 */
	$("form#bom-select select").change(function() {
		$("li.bom span").hide();
		var id = $("form#bom-select option:selected").attr('value');
		$("li#"+id+".bom span").show().find("span.edit").hide(); // have to hide edit stuff too :(
	});

	/**
	 * BOM add, show part selector popup box
	 * 
	 * @param id is the ID of the prod (BOM) to which we're adding a part
	 */
	function showPopupBox(id) {    // To Load the Popupbox
		$('#popup_box table').attr('id', id); // save prod id for later
		$('#popup_box').fadeIn("slow");
		$("#container").css({ // this is just for style
			"opacity": "0.3"  
		});         
	}     
	
	/**
	 * BOM add, hide part selector popup box
	 */
	function hidePopupBox() {
		$('#popup_box').fadeOut("fast");
	}

	/**
	 * BOM add, loads popup box for parts selection
	 */
	$('form.bom-add').click(function() {
		var id = $(this).closest('table').attr('id'); // find prod id for BOM
		showPopupBox(id);
		return false;
	});
	
	/**
	 * Add button clicked, now we add the selected part
	 */
	$('#popup_box form input.add').click(function() {		
		var partId = $(this).closest('form').attr('id');
		var prodId = $(this).closest('table').attr('id');
		var mydata = "&mode=add&qty=1&parts_id="+partId+"&products_id="+prodId;
		myhandler = addHandler;
		console.log(mydata);
		$.ajax({
			dataType: "json",
			type: "POST",
			url: actionUrl,
			data: mydata, // serializes the form's elements.
			success: myhandler
		});
		return false;
	});

	/** Make sure popup box height is correct */
	$("#popup_box").css("height", "auto");

	/**
	 * Close button clicked, to cancel / hide the popup
	 */
	$("#popupBoxClose").click(function() {
		hidePopupBox();
		return false;
	});

	/**
	 * Table search/filter
	 */
	 
	// NEW selector
	jQuery.expr[':'].search = function(a, i, m) {
	  return jQuery(a).text().toUpperCase()
		  .indexOf(m[3].toUpperCase()) >= 0;
	};
 
 	/**
 	 * Initialize all the trs with "found" so they are available for pagination
 	 */
 	$("table.filtered tbody tr").addClass("found");

	/**
	 * Live search for trs with span.text containing variable in #searchInput
	 */ 
	$("#searchInput").keyup(function() {
		var data = this.value.split(" ");
		var table = $("table.filtered");
		if (data == '') {
			table.find("tbody tr").addClass("found");
		} else {
			table.find("tbody tr").removeClass("found");
			table.find("tr:last").show();
			$.each(data, function(i, v) {
				var text = table.find("tr span.text:search('"+v+"')").closest("tr").addClass("found");
			});
		}
		table.trigger('reformat');
	});	 

	/**
	 * Capture submit for search/filter form
	 */
	// TODO

	/**
	 * Table pagination
	 * 
	 * Must set numPerPage externally
	 */
	$('table.paginated').each(function() {
		var currentPage = 0;
		var $table = $(this);
		// Reformat after load or after search filters stuff
		$table.bind('reformat', function() {
			$("div.pager").remove(); // remove the old pager
			var $pager = $('<div class="pager"></div>');
			var numRows = $table.find('tbody tr.found').length;
			var numPages = Math.ceil(numRows / numPerPage);
			// If search filters out everything...
			if (numPages == 0) numPages = 1; 
			// Recreate the pager buttons
			$("div.pager span").remove();
			for (var page = 0; page < numPages; page++) {
				$('<span class="page-number"></span>').text(page + 1).bind('click', {
					newPage: page
				}, function(event) {
					currentPage = event.data['newPage'];
					$table.trigger('repaginate');
					$(this).addClass('active').siblings().removeClass('active');
				}).appendTo($pager).addClass('clickable');
			}
			// Put the new pager buttons in place and make the first page/button active
			$pager.insertBefore($table).find('span.page-number:first').addClass('active');
			currentPage = 0;
			$table.trigger('repaginate');
		});
		// Repaginate the table so we're showing the selected page		
		$table.bind('repaginate', function() {
			$table.find('tbody tr').hide();
			$table.find('tbody tr.found').slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
			$table.find('tr:last').show();
		});
		$table.trigger('reformat');
	});

	/**
	 * Form update submit handler
	 */
	$("tbody tr td form").submit(function () {
		return false;
	});

	/**
	 * Form cancel handler
	 */
	$("td input.cancel").click(function() {
		console.log("cancel");
		normal();
		return false;
	});

	/**
	 * Display normally, show/hide as appropriate
	 */	
	normal();
});
