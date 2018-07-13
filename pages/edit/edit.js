/* global dataRequester */

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
        dataRequester.apiCall('/api/edit/GetLinks.php', "GET", null, function (response) {
            if (response.valid) {
                contentData = response.data.Links;
                dataReady();
            } else {
				$('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
	}

	function createEditList() {
		$('#editList').empty();
		for (var list_num = 0; list_num < contentData.length; list_num++) {
			var category = $('<li id="category_' + list_num + '" class="ui-state-default category"></li>');
			
			var header = $('<div>' +
				'<i class="fas fa-arrows-alt-v"></i>' +
				' Category: <input type="text" name="header_' + list_num + '" id="header_' + list_num + '" value="' + contentData[list_num].header + '" /> ' +
				'<button class="ui-button ui-button-fa collapseButton" data-num="' + list_num + '"><i class="fas fa-fw fa-compress-alt"></i> <span>Collaspe</span></button>' +
                '<button id="delete_button_' + list_num + '" class="ui-button ui-button-fa deleteCategoryButton"><i class="fas fa-fw fa-times"></i></button>' +
				'</div>');
				
			var addList = $('<ul class="addList" id="addList_' + list_num + '"></ul>');
			var footer = $('<li class="ui-state-default item" id="list_' + list_num + '_add_item"></li>');
			footer.append('Add Bookmark');
            footer.append('<button type="button" id="button_' + list_num + '" class="ui-button ui-button-fa addButton"><i class="fas fa-fw fa-plus"></i></button>');
			var footerdiv = $('<div class="entryInputs"></div>');
			footerdiv.append('Text: <input type="text" id="list_' + list_num + '_add_text" placeholder="Gatriex" />');
			footerdiv.append('<br />Link: <input class="inputLink" type="text" id="list_' + list_num + '_add_link" placeholder="https://gatriex.com" />');
			footer.append(footerdiv);
			addList.append(footer);
			
			var list = $('<ul id="list_' + list_num + '" class="list"></ul>');

			for (var i = 0; i < contentData[list_num].items.length; i++) {
				var item = $('<li class="ui-state-default item" id="list_' + list_num + '_item_' + i + '"></li>');
				item.append('<i class="fas fa-arrows-alt-v"></i> Bookmark');
                item.append('<button type="button" id="list_' + list_num + '_button_' + i + '" class="ui-button ui-button-fa deleteButton"><i class="fas fa-fw fa-times"></i></button>');
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
			connectWith: ".list"
		});
    }

	function setEventHandlers() {
	    $('#submitForm').click(function() {
	        $('#dialogMessage').html("");
	        saveChanges();
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
			item.append('<i class="fas fa-arrows-alt-v"></i> Bookmark');
            item.append('<button type="button" id="list_' + list_num + '_button_' + item_count[list_num] + '" class="ui-button ui-button-fa deleteButton"><i class="fas fa-fw fa-times"></i></button>');
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
				'<i class="fas fa-arrows-alt-v"></i>' +
				' Category: <input type="text" name="header_' + list_count + '" id="header_' + list_count + '" placeholder="Category" value="' + $('#addCategoryText').val() + '"/> ' +
                '<button class="ui-button ui-button-fa collapseButton" data-num="' + list_count + '"><i class="fas fa-fw fa-compress-alt"></i> <span>Collaspe</span></button>' +
                '<button type="button" id="delete_button_' + list_count + '" class="ui-button ui-button-fa deleteCategoryButton"><i class="fas fa-fw fa-times"></i></button>' +
				'</div>');
				
			var addList = $('<ul class="addList" id="addList_' + list_count + '"></ul>');
			var footer = $('<li class="ui-state-default item" id="list_' + list_count + '_add_item"></li>');
			footer.append('Add Bookmark');
            footer.append('<button type="button" id="button_' + list_count + '" class="ui-button ui-button-fa addButton"><i class="fas fa-fw fa-plus"></i></button>');
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
			if (i.hasClass("fa-compress-alt")) {
			    span.html("Expand");
			} else {
			    span.html("Collapse");
			}
			i.toggleClass("fa-compress-alt");
			i.toggleClass("fa-expand-alt");
			$("#list_" + list_num).toggle();
			$("#addList_" + list_num).toggle();
		});
		
		$("#collapseAll").click(function () {
		    $.each($(".collapseButton"), function() {
		        if ($(this).find("i").hasClass("fa-compress-alt")) {
		            $(this).click(); 
		        }
		    });
		});
		
		$("#expandAll").click(function () {
		    $.each($(".collapseButton"), function() {
		        if ($(this).find("i").hasClass("fa-expand-alt")) {
		            $(this).click(); 
		        }
		    });
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
			    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: Categories must have a name.');
			    return false;
			}
			if (category.items.length <= 0) {
			    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: Category "' + category.header + '" contains no bookmarks.');
			    return false;
			}
			data.push(category);
		}

		var postData = { 
            "data" : data
        };
        
        dataRequester.apiCall('/api/edit/SetLinks.php', "POST", postData, function (response) {
            if (response.valid) {
                $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
            } else {
                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
	}
});
