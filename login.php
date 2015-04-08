<?php
session_start();
include_once("config.php");

if (isset($_POST["login"]))
{
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $err = "";
    if (strlen($email) == 0 && strlen($password) == 0) {
        $err = "Please enter a non-blank email and password";
    }
    else {
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }

        $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password';";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) < 1) {
            $err = "Email/Password combination is incorrect";
        } elseif (mysqli_num_rows($result) > 1) {
            $err = "An error has occurred";
        } else {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["uid"] = $row["uid"];
            $_SESSION["first_name"] = $row["first_name"];
            header("Location: index.php");
            mysqli_free_result($result);
            exit();
        }
        mysqli_close($db);
    }

    if ($err != "") $_SESSION["error"] = $err;
}

if (isset($_SESSION["uid"])) {
    $_SESSION = array();
    session_destroy();
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Login</title>
<script type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
	<header>
		<div class="content50Percent">
			<a class="siteLogo" href="index.php">updatr</a>
			<div class="account">
				<img class="accountImage" src="img/default-user.png" alt="Profile"/>
                <?php if(isset($_SESSION["uid"])) {
                    $first_name = $_SESSION["first_name"];
                    echo "<a class=\"accountText\" href=\"login.php\">Logout $first_name</a>";
                } else {
                    echo "<a class=\"accountText\" href=\"login.php\">Login / Sign Up</a>";
                }?>
			</div>
		</div>
	</header>
	<div class="content30Percent">
		<a href="<?php echo $_SERVER["HTTP_REFERER"] ?>" class="goBack">Go Back</a>
        <?php if (!isset($_SESSION["uid"])) { ?>
            <div id="loginForm" class="center">
                <h2 class="loginHeader">Login</h2>
                <hr/>
                <form action="login.php" method="post">
                <fieldset>
                    <?php if (isset($_SESSION["error"])) {
                        $err = $_SESSION["error"];
                        unset($_SESSION["error"]);
                        echo ("<p class=\"error\">$err</p>");
                    } ?>
                    <input type="text" name="email" id="login-email" placeholder="Email" value="<?php if(isset($email)) $email?>"/>
                    <br/>
                    <input type="password" name="password" id="login-password" placeholder="Password" />
                    <br/>
                    <input type="submit" name="login" value="Submit" class="submitButton" />
                </fieldset>
                </form>
            </div>
            <hr/>
            <form action="register.php" method="post">
                <fieldset><input class="createAccount" name="register" type="submit" value="Create Account"/></fieldset>
            </form>
        <?php } else { ?>
            <div id="logout" class="center">
                <h2 class="loginHeader">Logout</h2>
                <hr/>
                <form action="login.php" method="post">
                    <fieldset>
                        <p>Are you sure you wish to log out?</p>
                        <input type="submit" name="logout" value="Logout" class="submitButton" />
                    </fieldset>
                </form>
            </div>
        <?php } ?>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<script type="text/javascript" src="scripts/loginr.js"></script>
</html>