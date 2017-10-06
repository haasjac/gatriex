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
                    <span class="mainHeader">User Info</span>
                </div>
                <div class="content">
                    <span id="userInfo">
                        <?php
                            include($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
                            
                            $user = getCurrentUser();
                            
                            if ($user != "") {
                                echo "Logged in as " . $user;
                            } else {
                                echo "Logged out";
                            }
                        ?>
                    </span>
                </div>
            </div>
            
            <div id="createDiv" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Create</span>
                </div>
                <div class="content">
                    <form id="createForm">
                        <fieldset>
                            <legend>Create Account:</legend>
                            Username: <input type="text" name="username" id="createUsername" /><br>
                            Password: <input type="password" name="password" id="createPassword" /><br>
                            Email: <input type="text" name="email" id="createEmail" /><br>
                            Summoner name: <input type="text" name="summoner" id="createSummoner" /><br>
                            <button id="createButton" class="ui-button">Create Account</button>
                        </fieldset>
                    </form>
                </div>
            </div>
            
            <div id="loginDiv" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Login</span>
                </div>
                <div class="content">
                    <form id="loginForm">
                        <fieldset>
                            <legend>Login:</legend>
                            Username: <input type="text" name="username" id="loginUsername" /><br>
                            Password: <input type="password" name="password" id="loginPassword" /><br>
                            <button id="loginButton" class="ui-button">Login</button>
                        </fieldset>
                    </form>
                    
                    <button id="logoutButton" class="ui-button">Logout</button>
                </div>
            </div>
        
        	<div id="forgetDiv" class="section">
                <div class="mainHeaderDiv">
                    <span class="mainHeader">Forget</span>
                </div>
                <div class="content">
                    <form id="forgetForm">
                        <fieldset>
                            <legend>Forgotten Username:</legend>
                            Email: <input type="text" name="email" id="forgetEmail" /> <button id="forgetUsernameButton" class="ui-button">Send Email</button><br>
                        </fieldset>
                        <fieldset>
                            <legend>Forgotten Password:</legend>
                            Username: <input type="text" name="username" id="forgetUsername" /> <button id="forgetPasswordButton" class="ui-button">Send Email</button><br>
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
