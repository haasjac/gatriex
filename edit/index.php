<html>
<head>
    <title>Edit</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="stylesheet" href="/jquery-ui-1.12.1.custom/jquery-ui.min.css" />
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/Gatriex.css">
    <link rel="stylesheet" type="text/css" href="/edit/edit.css">
    <script src="/jquery-ui-1.12.1.custom/jquery.js"></script>
    <script src="/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="/edit/edit.js"></script>
	<script src="/date.js"></script>
</head>

<body>
    <div id="wrapper">
        <div id="header">
            <div>
                <span><a href="/"><img src="/images/Logo.png" width="100" height="100" /></a></span>
                <div id="mydate"></div>
            </div>
        </div>
        
        <div id="main">
            <div id="edit" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Edit Bookmarks</span>
                </div>
                <div class="content">
			        <form id="form" style="margin-bottom:0;">
			            <div id="listDiv">
    			            <ul id="editList"></ul> 
    			            <br />
			                <ul id="addList">
                                <li id="category_add" class="ui-state-default category">
                                    <div>
                                        Add Category: <input type="text" name="addCategory" id="addCategoryText" placeholder="Category" />
                                        <button type="button" class="ui-button addCategoryButton"><i class="fa fa-plus"></i></button>
    			                    </div>
			                    </li>
        			       </ul>
        			    </div>
    			    </form>
			    </div>
			    <div class="formButtons">
			        <button id="revertForm" class="ui-button"><i class="fa fa-refresh"></i> Revert Changes</button>
			        <button id="submitForm" class="ui-button"><i class="fa fa-floppy-o"></i> Save Changes</button>
			    </div>
    		</div>
    	</div>
        
        <div id="footer">
            Gatriex.com isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends.
            <br /> 
            League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
        </div>
    </div>
	
	<div id="dialogBox" title="Save Changes">
	    <div>
	        Password: <input type="password" name="password" id="password" />
	    </div>
		<p id="dialogMessage"></p>
	</div>
</body>
</html>