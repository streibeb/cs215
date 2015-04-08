<?php
session_start();
include_once("config.php");

if (isset($_POST["submit"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $passwordConf = trim($_POST["passwordConf"]);
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $dobDay = $_POST["dobDay"];
    $dobMonth = $_POST["dobMonth"];
    $dobYear = $_POST["dobYear"];
    $birthday = $dobYear.'/'.$dobMonth.'/'.$dobDay;
    $gender = $_POST["gender"];

    $err = "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = $err."Invalid email".PHP_EOL;
    }

    if (strlen($password) < 8) {
        $err = $err."Password is too short".PHP_EOL;
    }

    if ($password != $passwordConf) {
        $err = $err."Passwords do not match".PHP_EOL;
    }

    if ($err == "") {
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }

        $query = "SELECT * FROM Users WHERE email = '$email';";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) <= 0) {
            $query = "INSERT INTO Users(email, password, first_name, last_name, birthday, gender)
                  VALUES ('$email', '$password', '$firstName', '$lastName', '$birthday', '$gender');";
            $result = mysqli_query($db, $query);
            if (!$result) {
                $err = $err . "Could not insert to database: " . mysqli_error($db) . PHP_EOL;
            } else {
                session_destroy();
                mysqli_close($db);
                header("Location: login.php");
            }
            mysqli_close($db);
        } else {
            $err = $err . "A user with this email already exists" . PHP_EOL;
        }
    }
    if ($err != "") $_SESSION["error"] = $err;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Register</title>
<script type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
	<header>
		<div class="content50Percent">
			<a class="siteLogo" href="index.php">updatr</a>
		</div>
	</header>
	<div class="content40Percent">
        <a href="<?php echo $_SERVER["HTTP_REFERER"] ?>" class="goBack">Go Back</a>
		<div id="loginForm" class="center">
			<h2 class="loginHeader">Create Account</h2>
			<hr/>
			<form action="register.php" method="post">
			<fieldset>
                <?php if (isset($_SESSION["error"])) {
                    $err = $_SESSION["error"];
                    unset($_SESSION["error"]);
                    echo ("<p class=\"error\">$err</p>");
                }?>
				<input type="text" id="register-fname" name="firstName" placeholder="First Name" value="<?php if(isset($firstName)) $firstName?>" />
				<br/>
				<input type="text" id="register-lname" name="lastName" placeholder="Last Name" value="<?php if (isset($lastName)) $lastName?>"/>
				<br/>
				<fieldset class="registerCenter">
					<div class="left" id="registerDateOfBirth">
						<select id="register-dob-month" name="dobMonth">
							<option value="01">January</option>
							<option value="02">February</option>
							<option value="03">March</option>
							<option value="04">April</option>
							<option value="05">May</option>
							<option value="06">June</option>
							<option value="07">July</option>
							<option value="08">August</option>
							<option value="09">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
						<select id="register-dob-day" name="dobDay">
							<option value = "01">1</option>
							<option value = "02">2</option>
							<option value = "03">3</option>
							<option value = "04">4</option>
							<option value = "05">5</option>
							<option value = "06">6</option>
							<option value = "07">7</option>
							<option value = "08">8</option>
							<option value = "09">9</option>
							<option value = "10">10</option>
							<option value = "11">11</option>
							<option value = "12">12</option>
							<option value = "13">13</option>
							<option value = "14">14</option>
							<option value = "15">15</option>
							<option value = "16">16</option>
							<option value = "17">17</option>
							<option value = "18">18</option>
							<option value = "19">19</option>
							<option value = "20">20</option>
							<option value = "21">21</option>
							<option value = "22">22</option>
							<option value = "23">23</option>
							<option value = "24">24</option>
							<option value = "25">25</option>
							<option value = "26">26</option>
							<option value = "27">27</option>
							<option value = "28">28</option>
							<option value = "29">29</option>
							<option value = "30">30</option>
							<option value = "31">31</option>
						</select>		
						<select id="register-dob-year" name="dobYear">
							<option value = "2015">2015</option>
							<option value = "2014">2014</option>
							<option value = "2013">2013</option>
							<option value = "2012">2012</option>
							<option value = "2011">2011</option>
							<option value = "2010">2010</option>
							<option value = "2009">2009</option>
							<option value = "2008">2008</option>
							<option value = "2007">2007</option>
							<option value = "2006">2006</option>
							<option value = "2005">2005</option>
							<option value = "2004">2004</option>
							<option value = "2003">2003</option>
							<option value = "2002">2002</option>
							<option value = "2001">2001</option>
							<option value = "2000">2000</option>
							<option value = "1999">1999</option>
							<option value = "1998">1998</option>
							<option value = "1997">1997</option>
							<option value = "1996">1996</option>
							<option value = "1995">1995</option>
							<option value = "1994">1994</option>
							<option value = "1993">1993</option>
							<option value = "1992">1992</option>
							<option value = "1991">1991</option>
							<option value = "1990">1990</option>
							<option value = "1989">1989</option>
							<option value = "1988">1988</option>
							<option value = "1987">1987</option>
							<option value = "1986">1986</option>
							<option value = "1985">1985</option>
							<option value = "1984">1984</option>
							<option value = "1983">1983</option>
							<option value = "1982">1982</option>
							<option value = "1981">1981</option>
							<option value = "1980">1980</option>
						</select>
					</div>
					<div class="right">
						<select name="gender">
							<option value = "Male">Male</option>
							<option value = "Female">Female</option>
							<option value = "Other">Other</option>
						</select>
					</div>
				</fieldset>
				<br/>
				<input type="text" id="register-email" name="email" placeholder="Email" value=""<?php if(isset($email)) $email?>/>
				<br/>
				<input type="password" id="register-password" name="password" placeholder="Password" />
				<br/>
				<input type="password" id="register-passwordConf" name="passwordConf" placeholder="Confirm Password" />
				<br/>
				<input type="submit" name="submit" value="Submit" class="submitButton" />
			</fieldset>
			</form>
		</div>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<script type="text/javascript" src="scripts/registerr.js"></script>
</html>