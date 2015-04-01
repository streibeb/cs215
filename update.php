<?php
session_start();
include_once("config.php");

if (!isset($_SESSION["uid"])) {
    $err = "You must be logged in to use this feature";
    $_SESSION["error"] = $err;
    header("Location: login.php");
    exit();
}

if (isset($_POST["submit"])) {
    $uid = $_SESSION["uid"];
    $content = htmlspecialchars(trim($_POST["post"]));
    $timestamp = date("Y-m-d H:i:s");

    $uploadStatus = false;
    $err = "";
    if (isset($_FILES["fileToUpload"]["name"]) && $_FILES["fileToUpload"]["name"] != "") {
        $target_file = USER_UPLOAD_DIRECTORY . basename($_FILES["fileToUpload"]["name"]);
        $uploadStatus = true;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check == false) {
            $err = $err."Image not valid".PHP_EOL;
            $uploadStatus = false;
        }
        if (file_exists($target_file)) {
            $err = $err."File already exists".PHP_EOL;
            $uploadStatus = false;
        }
        if ($_FILES["fileToUpload"]["size"] > USER_MAX_FILESIZE) {
            $err = $err."Image too large: ".$_FILES["fileToUpload"]["size"].PHP_EOL;
            $uploadStatus = false;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $err = $err."Invalid file type".PHP_EOL;
            $uploadStatus = false;
        }
    }

    if (strlen($content) > 1000) {
        $err = $err."Post is too long".PHP_EOL;
    }

    if (strlen($content) <= 0) {
        $err = $err."Post is too short".PHP_EOL;
    }

    if ($err != "") {
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }

        $query = "INSERT INTO Posts(uid, timestamp, content)
              VALUES ('$uid', '$timestamp', '$content');";
        $result = mysqli_query($db, $query);
        if ($result) {
            if ($uploadStatus) {
                $pid = mysqli_insert_id($db);
                $ext = explode(".", $target_file);
                $image = $uid . "_" . $pid . "." . end($ext);
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], USER_UPLOAD_DIRECTORY . $image)) {
                    $query = "UPDATE Posts SET image = '$image' WHERE pid = '$pid';";
                    $result = mysqli_query($db, $query);
                    if (!$result) {
                        $err = "There was a problem uploading the image";
                    }
                }
            }
        } else {
            echo mysqli_error($db);
        }
        mysqli_close($db);
    }
    $_SESSION["error"] = $err;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Update</title>
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
	<div class="content50Percent">
		<a href="index.php" class="goBack">Go Back</a>
		<div id="loginForm" class="center">
			<h2 class="loginHeader">Post Update</h2>
			<hr/>
			<div class="makePost">
				<form action="update.php" method="post" enctype="multipart/form-data">
				<fieldset>
                    <?php if (isset($_SESSION["error"])) {
                        $err = $_SESSION["error"];
                        unset($_SESSION["error"]);
                        echo ("<p class=\"error\">$err</p>");
                    }?>
					<textarea id="update-post" name="post" cols="70" rows="5" placeholder="Enter comment here..."></textarea>
                    <div class="content50Percent">
                        <div class="content20Percent left">
                            <input type="file" name="fileToUpload" class="uploadPicture left"/>
                        </div>
                        <div class="content20Percent right">
                            <p id="charCounter" class="charCounter right">0/1000</p>
                        </div>
                    </div>
				    <input type="submit" name="submit" value="Submit" />
				</fieldset>
				</form>
			</div>
		</div>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<script type="text/javascript" src="scripts/updater.js"></script>
</html>