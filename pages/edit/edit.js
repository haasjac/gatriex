"use strict";

$(function () {
	var contentData = {}, item_count = {}, list_count = 0;

    setEventHandlers();
	getContent();
	function dataReady() {
	    createEditList();
	    setSortable();
	}

	function getContent() {
	    $.ajax({
			url: '/api/edit/GetLinks.php',
			type: 'GET',
			dataType: 'json',
			success: function (data) {
				contentData = data;
				dataReady();
			},
			error: function (xhr, status, error) {
				$('#error').html(error);
				$('#error-details').html(xhr.responseText);
				$('#error-message').dialog("open");
			}
		});
	}

	function createEditList() {
		$('#editList').empty();
		for (var list_num = 0; list_num < contentData.length; list_num++) {
			var category = $('<li id="category_' + list_num + '" class="ui-state-default category"></li>');
			
			var header = $('<div>' +
				'<i class="fa fa-arrows-v"></i>' +
				' Category: <input type="text" name="header_' + list_num + '" id="header_' + list_num + '" value="' + contentData[list_num].header + '" /> ' +
				'<button class="ui-button collapseButton" data-num="' + list_num + '"><i class="fa fa-compress"></i> <span>Collaspe</span></button>' +
				'<button id="delete_button_' + list_num + '" class="ui-button deleteCategoryButton"><i class="fa fa-remove"></i></button>' +
				'</div>');
				
			var addList = $('<ul class="addList" id="addList_' + list_num + '"></ul>');
			var footer = $('<li class="ui-state-default item" id="list_' + list_num + '_add_item"></li>');
			footer.append('Add Bookmark');
			footer.append('<button type="button" id="button_' + list_num + '" class="ui-button addButton"><i class="fa fa-plus"></i></button>');
			var footerdiv = $('<div class="entryInputs"></div>');
			footerdiv.append('Text: <input type="text" id="list_' + list_num + '_add_text" placeholder="Gatriex" />');
			footerdiv.append('<br />Link: <input class="inputLink" type="text" id="list_' + list_num + '_add_link" placeholder="https://gatriex.com" />');
			footer.append(footerdiv);
			addList.append(footer);
			
			var list = $('<ul id="list_' + list_num + '" class="list"></ul>');

			for (var i = 0; i < contentData[list_num].items.length; i++) {
				var item = $('<li class="ui-state-default item" id="list_' + list_num + '_item_' + i + '"></li>');
				item.append('<i class="fa fa-arrows-v"></i> Bookmark');
				item.append('<button type="button" id="list_' + list_num + '_button_' + i + '" class="ui-button deleteButton"><i class="fa fa-remove"></i></button>');
				var div = $('<div class="entryInputs"></div>');
				div.append('Text: <input type="text" name="list_' + list_num + '_text_' + i + '" id="list_' + list_num + '_text_' + i + '" value="' + contentData[list_num].items[i].text + '" />');
				div.append('<br />Link: <input class="inputLink" type="text" name="list_' + list_num + '_link_' + i + '" id="list_' + list_num + '_link_' + i + '" value="' + contentData[list_num].items[i].link + '" />');
				item.append(div);
				list.append(item);
			}
			item_count[list_num] = i;
			category.append(header).append(list).append(addList);
			$('#editList').append(category);
		}
		
		list_count = contentData.length;
	}

    function setSortable() {
        $("#editList").sortable({
			placeholder: "ui-state-highlight",
			start: function (e, ui) {
				ui.placeholder.height(ui.item.height());
			}
		});

		$(".list").sortable({
			placeholder: "ui-state-highlight",
			start: function (e, ui) {
				ui.placeholder.height(ui.item.height());
			},
			connectWith: ".list",
		});
    }

	function setEventHandlers() {
	    $('#submitForm').click(function() {
	        $('#dialogMessage').html("");
	        $('#password').val("");
	        $('#dialogBox').dialog("open");
	    });
	    
	    $('#revertForm').click(function() {
	        getContent();
	    });
	    
		$("#form").submit(function (e) {
			e.preventDefault();
			return false;
		});

		

		$("#editList").on("click", ".addButton", function () {
			var list_num = $(this).attr("id").replace(/button_/g, "");
			
			var item = $('<li class="ui-state-default item" id="list_' + list_num + '_item_' + item_count[list_num] + '"></li>');
			item.append('<i class="fa fa-arrows-v"></i> Bookmark');
			item.append('<button type="button" id="list_' + list_num + '_button_' + item_count[list_num] + '" class="ui-button deleteButton"><i class="fa fa-remove"></i></button>');
			var div = $('<div class="entryInputs"></div>');
			div.append('Text: <input type="text" name="list_' + list_num + '_text_' + item_count[list_num] + '" id="list_' + list_num + '_text_' + item_count[list_num] + '" value="' + $('#list_' + list_num + '_add_text').val() + '" />');
			div.append('<br />Link: <input class="inputLink" type="text" name="list_' + list_num + '_link_' + item_count[list_num] + '" id="list_' + list_num + '_link_' + item_count[list_num] + '" value="' + $('#list_' + list_num + '_add_link').val() + '" />');
			item.append(div);
			$('#list_' + list_num).append(item);
			
			item_count[list_num] = item_count[list_num] + 1;
			$('#list_' + list_num + '_add_text').val("");
			$('#list_' + list_num + '_add_link').val("");
		});

		$("#editList").on("click", ".deleteButton", function () {
			var item = $(this).attr("id").replace(/button/g, "item");
			$('#' + item).remove();
		});

		$("#form").on("click", ".addCategoryButton", function () {
			var category = $('<li id="category_' + list_count + '" class="ui-state-default category"></li>');
			
			var header = $('<div>' +
				'<i class="fa fa-arrows-v"></i>' +
				' Category: <input type="text" name="header_' + list_count + '" id="header_' + list_count + '" placeholder="Category" value="' + $('#addCategoryText').val() + '"/> ' +
				'<button class="ui-button collapseButton" data-num="' + list_count + '"><i class="fa fa-compress"></i> <span>Collaspe</span></button>' +
				'<button type="button" id="delete_button_' + list_count + '" class="ui-button deleteCategoryButton"><i class="fa fa-remove"></i></button>' +
				'</div>');
				
			var addList = $('<ul class="addList" id="addList_' + list_count + '"></ul>');
			var footer = $('<li class="ui-state-default item" id="list_' + list_count + '_add_item"></li>');
			footer.append('Add Bookmark');
			footer.append('<button type="button" id="button_' + list_count + '" class="ui-button addButton"><i class="fa fa-plus"></i></button>');
			var footerdiv = $('<div class="entryInputs"></div>');
			footerdiv.append('Text: <input type="text" id="list_' + list_count + '_add_text" placeholder="Gatriex" />');
			footerdiv.append('<br />Link: <input class="inputLink" type="text" id="list_' + list_count + '_add_link" placeholder="https://gatriex.com" />');
			footer.append(footerdiv);
			addList.append(footer);
			
			var list = $('<ul id="list_' + list_count + '" class="list"></ul>');

			item_count[list_count] = 0;
			category.append(header).append(list).append(addList);
			$('#editList').append(category);
			item_count[list_count] = 0;
			list_count++;
			$('#addCategoryText').val("");
			setSortable();
		});

		$("#editList").on("click", ".deleteCategoryButton", function () {
			var item = $(this).attr("id").replace(/delete_button/g, "category");
			$('#' + item).remove();
		});
		
		$("#editList").on("click", ".collapseButton", function () {
			var list_num = $(this).attr("data-num");
			var i = $(this).find("i");
			var span = $(this).find("span");
			if (i.hasClass("fa-compress")) {
			    span.html("Expand");
			} else {
			    span.html("Collapse");
			}
			i.toggleClass("fa-compress");
			i.toggleClass("fa-expand");
			$("#list_" + list_num).toggle();
			$("#addList_" + list_num).toggle();
		});
		
		$("#collapseAll").click(function () {
		    $.each($(".collapseButton"), function() {
		        if ($(this).find("i").hasClass("fa-compress")) {
		            $(this).click(); 
		        }
		    });
		});
		
		$("#expandAll").click(function () {
		    $.each($(".collapseButton"), function() {
		        if ($(this).find("i").hasClass("fa-expand")) {
		            $(this).click(); 
		        }
		    });
		});
		
		$("#dialogBox").dialog({
			autoOpen: false,
			modal: true,
			width: 400,
			buttons: {
			    Save: function () {
					saveChanges();
				},
				Close: function () {
					$(this).dialog("close");
				}
			}
		});
	}
	
	function saveChanges() {
	    var data = [];

		var categories = $("#editList").sortable("toArray");

		for (var i = 0; i < categories.length; i++) {
			var list_num = categories[i].replace(/category_/g, "");
			var header = $("#header_" + list_num).val();
			var items = $("#list_" + list_num).sortable("toArray");
			var category = {};
			category.header = header;
			category.items = [];
			for (var j = 0; j < items.length; j++) {
				var item_list_num = items[j].replace(/_item_\d+/g, "").replace(/list_/g, "");
				var item_id = items[j].replace(/list_\d+/g, "").replace(/_item_/g, "");
				var obj = {
					text: $("#list_" + item_list_num + "_text_" + item_id).val(),
					link: $("#list_" + item_list_num + "_link_" + item_id).val()
				};
				category.items.push(obj);
			}
			if (category.header === "") {
			    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: Categories must have a name.');
			    return false;
			}
			if (category.items.length <= 0) {
			    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error:<br>Category "' + category.header + '" contains no bookmarks.<br><br>Categories must contain at least one bookmark.');
			    return false;
			}
			data.push(category);
		}

		var jsonData = JSON.stringify({ "data" : data, "password" : $('#password').val() });
		$.ajax({
			url: '/api/edit/SetLinks.php',
			type: 'POST',
			contentType: 'application/json',
			data: jsonData,
			success: function (data) {
			    $('#dialogMessage').html('<i class="fa fa-check-circle"></i> Changes were successfully saved.');
			},
			error: function (xhr, status, error) {
			    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + xhr.responseText);
			}
		});
	}
});