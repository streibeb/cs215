<?php
session_start();
include_once("config.php");

$err = "";

$db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    die ("Failed to connect to database: " . mysqli_connect_error());
}

if (isset($_SESSION["uid"])) {
    $uid = $_SESSION["uid"];
    $query = "SELECT Posts.*,
      Users.first_name,
      Users.last_name,
      (SELECT COUNT(Comments.cid) FROM Comments WHERE Posts.pid = Comments.pid) AS 'numComments',
      (SELECT COUNT(1) FROM Likes WHERE Posts.pid = Likes.pid) AS 'numLikes',
      (SELECT 1 FROM Likes WHERE Posts.pid = Likes.pid AND Likes.uid = '$uid') AS 'userLiked'
    FROM Posts
    JOIN Users ON Posts.uid = Users.uid
    ORDER BY Posts.pid
    DESC LIMIT 10";
} else {
    $query = "SELECT Posts.*,
      Users.first_name,
      Users.last_name,
      (SELECT COUNT(Comments.cid) FROM Comments WHERE Posts.pid = Comments.pid) AS 'numComments',
      (SELECT COUNT(1) FROM Likes WHERE Posts.pid = Likes.pid) AS 'numLikes'
    FROM Posts
    JOIN Users ON Posts.uid = Users.uid
    ORDER BY Posts.pid
    DESC LIMIT 10";
}
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) == 0) {
    $err = "There was a problem retrieving posts";
}
mysqli_close($db);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<!--  <meta charset = "utf-8"> -->
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Updatr</title>
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/ajax.js"></script>
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
        <?php if(isset($_SESSION["uid"])): ?>
            <form action="update.php">
			    <fieldset><input class="postUpdate" type="submit" value="Post an Update"/></fieldset>
		    </form>
        <?php endif; ?>
        <div id="PostContainer">
            <?php while($post = mysqli_fetch_assoc($result)) {
                $likeButtonClass = is_null($post["userLiked"]) ? "likeButton" : "likeButtonPressed" ?>
            <div class="post" id="post_<?=$post["pid"]?>">
                <div class="postProfile">
                    <img class="postProfileImage" src="img/default-user.png" alt="Profile"/>
                    <div class="postProfileName"><?php echo $post["first_name"]. " " . $post["last_name"]; ?></div>
                    <div class="postProfileInfo">
                        <div class="right">
                            <button id="likeButton_<?=$post["pid"]?>" class="<?=$likeButtonClass?>"><?=$post["numLikes"]?></button>
                        </div>
                        <div class="right">
                            <span class="postDate"><?php echo $post["timestamp"];?></span>
                            <br/>
                            <a class="commentIndicator" href="<?php echo "post.php?pid=".$post["pid"];?>">
                                <?php echo $post["numComments"];
                                if ($post["numComments"] != 1) {?>
                                    Comments
                                <?php } else { ?>
                                    Comment
                                <?php } ?>
                            </a>
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
        <?php } ?>
        </div>
	</div>
	<footer>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<script type="text/javascript" src="scripts/indexr.js"></script>
</html>