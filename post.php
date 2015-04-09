<?php
session_start();
include_once("config.php");

$pid = $_GET["pid"];

$loggedIn = isset($_SESSION["uid"]);

if (isset($_POST["submit"])) {
    $uid = $_SESSION["uid"];
    $content = htmlspecialchars(addslashes(trim($_POST["post"])));
    $timestamp = date("Y-m-d H:i:s");

    $uploadStatus = false;
    $err = "";
    if (isset($_FILES["fileToUpload"]["name"]) && $_FILES["fileToUpload"]["name"] != "") {
        $target_file = USER_UPLOAD_DIRECTORY . basename($_FILES["fileToUpload"]["name"]);
        $uploadStatus = true;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check == false) {
            $err = $err . "Image not valid" . PHP_EOL;
            $uploadStatus = false;
        }
        if (file_exists($target_file)) {
            $err = $err . "File already exists" . PHP_EOL;
            $uploadStatus = false;
        }
        if ($_FILES["fileToUpload"]["size"] > USER_MAX_FILESIZE) {
            $err = $err . "Image too large: " . $_FILES["fileToUpload"]["size"] . PHP_EOL;
            $uploadStatus = false;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $err = $err . "Invalid file type" . PHP_EOL;
            $uploadStatus = false;
        }
    }

    if (strlen($content) > 1000) {
        $err = $err . "Post is too long" . PHP_EOL;
    }

    if (strlen($content) <= 0) {
        $err = $err . "Post is too short" . PHP_EOL;
    }

    if ($err == "") {
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }

        $query = "INSERT INTO Comments(pid, uid, timestamp, content)
                  VALUES ('$pid', '$uid', '$timestamp', '$content');";
        $result = mysqli_query($db, $query);
        if ($result) {
            if ($uploadStatus) {
                $cid = mysqli_insert_id($db);
                $ext = explode(".", $target_file);
                $image = $uid . "_" . $pid . "_" . $cid ."." . end($ext);
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], USER_UPLOAD_DIRECTORY . $image)) {
                    $query = "UPDATE Comments SET image = '$image' WHERE cid = '$cid';";
                    $result = mysqli_query($db, $query);
                    if (!$result) {
                        $err = "There was a problem uploading the image";
                    } else {
                        echo mysqli_error($db);
                    }
                }
            }
        } else {
            echo mysqli_error($db);
        }
    } else {
        echo $err;
    }
}

if (isset($_GET["pid"])) {
    $err = "";

    $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
    if (!$db) {
        die ("Failed to connect to database: " . mysqli_connect_error());
    }

    $query = "SELECT Posts.*, Users.* FROM Posts JOIN Users ON Posts.uid = Users.uid WHERE pid = '$pid';";
    $posts = mysqli_query($db, $query);
    if (mysqli_num_rows($posts) != 1) {
        $err = "404";
    } else {
        $query = "SELECT Comments.*, Users.* FROM Comments JOIN Users ON Comments.uid = Users.uid WHERE pid = '$pid';";
        $comments = mysqli_query($db, $query);
    }
    mysqli_close($db);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Post</title>
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/ajax.js"></script>
</head>
<body>
	<header>
		<div class="content50Percent">
			<a class="siteLogo" href="index.php">updatr</a>
			<div class="account">
				<img class="accountImage" src="http://th01.deviantart.net/fs71/PRE/i/2013/325/5/b/catbug__by_bronysinc-d6v4gjl.png" alt="Profile"/>
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
        <?php if (isset($posts) && $post = mysqli_fetch_assoc($posts)) { ?>
            <a href="<?php echo $_SERVER["HTTP_REFERER"] ?>" class="goBack">Go Back</a>
            <div class="post" id="post_<?php echo $post["pid"]; ?>">
                <div class="postProfile">
                    <img class="postProfileImage" src="img/default-user.png" alt="Profile"/>
                    <div class="postProfileName"><?php echo $post["first_name"]. " " . $post["last_name"]; ?></div>
                    <div class="postProfileInfo">
                        <div class="right">
                            <button id="<?php echo "likeButton_".$post["pid"];?>" class="likeButton"></button>
                        </div>
                        <div class="right">
                            <span class="postDate"><?php echo $post["timestamp"];?></span>
                        </div>
                    </div>
                </div>
                <div class="postArea">
                    <?php
                    echo "<p>".$post["content"]."</p>";
                    if (isset($post["image"])) { ?>
                        <img class="postImage" src="<?php echo USER_UPLOAD_DIRECTORY.$post["image"];?>" alt="UserPic"/>
                    <?php } ?>
                </div>
            </div>
            <div id="commentContainer">
                <?php while ($comment = mysqli_fetch_assoc($comments)) { ?>
                    <div class="comment" id="comment_<?php echo $comment["cid"];?>">
                        <div class="commentProfile">
                            <img class="commentProfileImage" src="http://showdown.gg/wp-content/uploads/2014/05/default-user.png" alt="Profile"/>
                            <div class="commentProfileName"><?php echo $comment["first_name"]. " " . $comment["last_name"]; ?></div>
                            <div class="postProfileInfo">
                                <div class="right">
                                    <div class="postDate"><?php echo $comment["timestamp"];?></div>
                                </div>
                            </div>
                        </div>
                        <div class="commentArea">
                            <?php
                            echo "<p>".$comment["content"]."</p>";
                            if (isset($comment["image"])) { ?>
                                <img class="postImage" src="<?php echo USER_UPLOAD_DIRECTORY.$comment["image"];?>" alt="UserPic"/>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if(isset($_SESSION["uid"])): ?>
            <div class="makeComment">
                <form action="post.php?pid=<?php echo $post["pid"];?>" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <textarea id="post-comment" name="post" cols="70" rows="5" placeholder="Enter comment here..."></textarea>
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
        <?php endif ?>
        <div class="center centerText">
            <br/>
            <span id="ajaxToggle">Auto-Update Enabled</span>
        </div>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<?php if ($loggedIn) { ?><script type="text/javascript" src="scripts/postr.js"></script><?php } ?>
<script type="text/javascript" src="scripts/post_ajaxr.js"></script>