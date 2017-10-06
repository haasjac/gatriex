<html>
<head>
    <title>Gatriex</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/Gatriex.css">
    <link rel="stylesheet" type="text/css" href="/index.css">
    <link rel="stylesheet" href="/jquery-ui-1.12.1.custom/jquery-ui.min.css" />
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script src="/jquery-ui-1.12.1.custom/jquery.js"></script>
    <script src="/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="/gatriex.js"></script>
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
            <div id="nav" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Bookmarks</span>
                    <a href="/edit" target="_blank" class="iconRight">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div class="content">
                    <dl id="navList"></dl>
                </div>
            </div>
        
        	<div id="content" >
        	    <div id="SummonerStats" class="section">
            	    <div class="mainHeaderDiv">
                        <span class="mainHeader">Summoner Stats</span>
                        <i id="refreshSummoner" class="fa fa-refresh iconRight"></i>
                        <span id="SummonerError" class="errorIcon iconRight"></span>
                    </div>
            		<div class="content">
            		    <div>
            		        <div>
            		            <input type="text" name="searchSummonerName" placeholder="Summoner Name" id="searchSummonerName" />
            		            <button id="searchButton" class="ui-button">Search</button>
        		            </div>
            		        <div style="padding-top: 15px;">
            		            <div style="float:left;">
                    				<img id="SummonerIcon" src="/images/Logo.png">
                    			</div>
                    			<div style="float:left; padding-left: 30px;">
                    				<div id="SummonerName"></div>
                    				<div id="League"></div>
                    				<div id="MiniSeries"></div>
                    			</div>
                    			<div style="clear: both;"></div>
            		        </div>
            			</div>
            		</div>
        		</div>
        		<div id="ServerStatus" class="section">
            		<div class="mainHeaderDiv">
                        <span class="mainHeader">Server Status<div id="Status" style="display:inline; text-transform:capitalize"> - North America</div></span>
                        <i id="refreshStatus" class="fa fa-refresh iconRight"></i>
                        <span id="StatusError" class="errorIcon iconRight"></span>
                    </div>
            		<div class="content">
            		    <div id="Incidents"></div>
            		</div>
        		</div>
        	</div>
        	
        	<div style="clear: both;"></div>
        </div>
        
        <div id="footer">
            Gatriex.com isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends.
            <br /> 
            League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
        </div>
    </div>
    
    <div id="dialogBox" title="Error Updating Data">
	    <div>
	        Error code: <span id="errorCode"></span>
	    </div>
		<p id="dialogMessage"></p>
	</div>
</body>
</html>
