<?php
session_start();
include_once("config.php");

if (isset($_GET["pid"])) {
    $pid = $_GET["pid"];
    $err = "";

    $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
    if (!$db) {
        die ("Failed to connect to database: " . mysqli_connect_error());
    }

    $query = "SELECT Posts.*, Users.* FROM Posts JOIN Users ON Posts.uid = Users.uid WHERE pid = '$pid';";
    $posts = mysqli_query($db, $query);
    if (mysqli_num_rows($posts) != 1) {
        $err = "404";
        echo $err;
    } else {
        $query = "SELECT * FROM Comments WHERE pid = '$pid';";
        $comments = mysqli_query($db, $query);
    }
    mysqli_close($db);
}

if (isset($_POST["submit"])) {

}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Post</title>
<script type="text/javascript" src="scripts/scripts.js"></script>
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
            <div class="post" id="post<?php echo $post["pid"]; ?>">
                <div class="postProfile">
                    <img class="postProfileImage" src="img/default-user.png" alt="Profile"/>
                    <div class="postProfileName"><?php echo $post["first_name"]. " " . $post["last_name"]; ?></div>
                    <div class="postProfileInfo">
                        <div class="right">
                            <button id="<?php echo "likeButton".$post["pid"];?>" class="likeButton"></button>
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
            <?php while ($comment = mysqli_fetch_assoc($comments)) { ?>
                <div class="comment" id="<?php echo $comment["cid"];?>">
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
            <!--<div class="comment">
                <div class="commentProfile">
                    <img class="commentProfileImage" src="http://showdown.gg/wp-content/uploads/2014/05/default-user.png" alt="Profile"/>
                    <div class="commentProfileName">Catbug</div>
                    <div class="right">
                        <div class="postDate">Jan 1 2000</div>
                    </div>
                </div>
                <div class="commentArea">
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis nibh ac pharetra commodo.
                    Donec sit amet luctus ipsum, nec egestas ligula. Ut rutrum tincidunt dolor, sed sagittis mauris ultrices sagittis.
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    </p>
                    <img class="postImage" src="http://i.ytimg.com/vi/xnhI-vaBxY4/maxresdefault.jpg" alt="UserPic"></img>
                </div>
            </div>-->
            <?php if(isset($_SESSION["uid"])): ?>
                <div class="makeComment">
                    <form action="post.php?pid=<?php echo $post["pid"];?>" method="post" enctype="multipart/form-data">
                        <fieldset>
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
            <?php endif ?>
        <?php } else { ?>
         <h1>404: Page Not Found</h1>
            <a href="index.php" class="goBack">Go Home</a>
        <?php } ?>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<script type="text/javascript" src="scripts/postr.js"></script>
</html>