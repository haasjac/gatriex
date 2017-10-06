<html>
<head>
    <title>Gatriex</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/Gatriex.css">
    <link rel="stylesheet" href="/jquery-ui-1.12.1.custom/jquery-ui.min.css" />
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script src="/jquery-ui-1.12.1.custom/jquery.js"></script>
    <script src="/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="/account/account.js"></script>
    <script src="/date.js"></script>

    <style>#main{ flex-direction: column; }</style>
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
            
            <div id="createDiv" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Reset Password</span>
                </div>
                <div class="content">
                    <form id="resetForm">
                        <fieldset>
                            <legend>Reset Password:</legend>
                            Username: <input type="text" name="username" id="resetUsername" /><br>
                            New Password: <input type="password" name="password" id="resetPassword" /><br>
                            <input type="hidden" name="token" id="resetToken" value="<?php echo (isset($_REQUEST["token"]) ? $_REQUEST["token"] : "") ?>"/>
                            <button id="resetButton" class="ui-button">Reset Password</button>
                        </fieldset>
                    </form>
                </div>
            </div>
            
        </div>
        
        <div id="footer">
            Gatriex.com isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends.
            <br /> 
            League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
        </div>
    </div>
</body>
</html>
