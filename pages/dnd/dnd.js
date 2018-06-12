/* global dataRequester */

"use strict";

$(function () {
	var contentData = {}, item_count = {}, list_count = 0, selected = 0, editMode = false;

    setEventHandlers();
    dataReady();
	//getContent();
	function dataReady() {
	    //createEditList();
	    setSortable();
	}

	function getContent() {
        /*dataRequester.apiCall('/api/edit/GetLinks.php', "GET", null, function (response) {
            if (response.valid) {
                contentData = response.data.Links;
                dataReady();
            } else {
				$('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });*/
	}

	function createEditList() {
		/*$('#editList').empty();
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
		
		list_count = contentData.length;*/
	}

    function setSortable() {
        if (editMode) {
            $("#editList").sortable({
    			placeholder: "ui-state-highlight",
    			start: function (e, ui) {
    				ui.placeholder.height(ui.item.height());
    			},
    			disabled: false
    		});
        }
        else {
            $("#editList").sortable({
    			placeholder: "ui-state-highlight",
    			start: function (e, ui) {
    				ui.placeholder.height(ui.item.height());
    			},
    			disabled: true
    		});
        }
    }

	function setEventHandlers() {
	    $("#form").submit(function (e) {
			e.preventDefault();
			return false;
		});
	    
		$(".addCategoryButton").click(function () {
		    list_count += 1;
		    var item = $('<li id="li_' + list_count + '" class="ui-state-default"></li>');
		    var div = $('<div class="person"></div>');
		    //var img = $('<img height="50" width="50" />');
		    var img = $('<i class="fa fa-user fa-2x profile blue playerTeam"></i>');
            var name = $('<div class="personName"><input class="playerName" style="width:50%" type="text" /> <input class="playerInitiative" style="width:10%" type="number" /></div>');
		    var removeButton = $(' <button id="button_' + list_count + '" class="ui-button deleteCategoryButton"><i class="fa fa-minus"></i></button>');
		    div.append(img).append(name).append(removeButton);
		    $('#editList').append(item.append(div));
			setSortable();
        });

        $("#sortButton").click(function () {
            sortList();
            setSortable();
        });

		$("#editList").on("click", ".deleteCategoryButton", function () {
			var item = $(this).attr("id").replace(/button/g, "li");
			$('#' + item).remove();
		});
		
		$("#editList").on("click", "li", function () {
		    if (!editMode) {
    			$("#editList li").each(function () {
    		        $(this).removeClass("initSelected"); 
    		    });
    		    $(this).addClass("initSelected");
		    }
		});
		
		$("#editButton").click(function () {
	        editMode = !editMode;
	        setSortable();
		    $(".addCategoryButton").toggleClass("hide"); 
            $(".deleteCategoryButton").toggleClass("hide");
            $("#sortButton").toggleClass("hide"); 
		});
		
		$("#editList").on("click", ".profile", function () {
		    if (editMode) {
		        $(this).toggleClass("red blue");
		    }
		});
	}

    function sortList() {
        var editList = $('#editList');

        var listitems = $('li', editList);

        listitems.sort(function (a, b) {
            var initA = Number($(a).find(".playerInitiative").val());
            var initB = Number($(b).find(".playerInitiative").val());

            if (initA === initB) {
                var teamA = $(a).find(".playerTeam").hasClass("blue");
                var teamB = $(b).find(".playerTeam").hasClass("blue");
                if (teamA === teamB) {
                    return 0;
                }
                return teamA ? -1 : 1;
            }
            else {
                return (initA < initB) ? 1 : -1;
            }
        });

        editList.append(listitems);
    }

	function saveChanges() {
	    /*var data = [];

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
			    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: Category "' + category.header + '" contains no bookmarks.');
			    return false;
			}
			data.push(category);
		}

		var postData = { 
            "data" : data
        };
        
        dataRequester.apiCall('/api/edit/SetLinks.php', "POST", postData, function (response) {
            if (response.valid) {
                $('#dialogMessage').html('<i class="fa fa-check-circle"></i> Changes were successfully saved.');
            } else {
                $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });*/
	}
});
