
$(document).ready(function() {

	/**
	 * Restore normal display
	 */
	function normal() {
		$("span.text").show();
		$("span.button").show();
		$("span.edit").hide();
		$("td").bind('click', edit);
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
