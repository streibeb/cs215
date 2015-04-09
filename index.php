<?php
session_start();
include_once("config.php");

$err = "";

$db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    die ("Failed to connect to database: " . mysqli_connect_error());
}

$loggedIn = isset($_SESSION["uid"]);
$postsPerPage = POSTS_PER_PAGE;

if ($loggedIn) {
    $uid = $_SESSION["uid"];
    $query = "SELECT Posts.*,
      Users.first_name,
      Users.last_name,
      COUNT(Comments.cid) as 'numComments',
      COUNT(Likes.pid) as 'numLikes',
      (SELECT 1 FROM Likes WHERE Likes.uid = '$uid' AND Posts.pid = Likes.pid) AS 'userLiked'
    FROM Posts
      JOIN Users ON Posts.uid = Users.uid
      LEFT JOIN Comments ON Posts.pid = Comments.pid
      LEFT JOIN Likes ON Posts.pid = Likes.pid
    WHERE Posts.timestamp >= '$updateTime'
    GROUP BY Posts.timestamp
    ORDER BY Posts.timestamp DESC
    LIMIT 0, $postsPerPage;";
} else {
    $query = "SELECT Posts.*,
      Users.first_name,
      Users.last_name,
      COUNT(Comments.cid) as 'numComments',
      COUNT(Likes.pid) as 'numLikes'
    FROM Posts
      JOIN Users ON Posts.uid = Users.uid
      LEFT JOIN Comments ON Posts.pid = Comments.pid
      LEFT JOIN Likes ON Posts.pid = Likes.pid
    WHERE Posts.timestamp >= '$updateTime'
    GROUP BY Posts.timestamp
    ORDER BY Posts.timestamp DESC
    LIMIT 0, $postsPerPage;";
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
                <?php if($loggedIn) {
                    $first_name = $_SESSION["first_name"];
                    echo "<a class=\"accountText\" href=\"login.php\">Logout $first_name</a>";
                } else {
                    echo "<a class=\"accountText\" href=\"login.php\">Login / Sign Up</a>";
                }?>
			</div>
		</div>
	</header>
	<div class="content50Percent">
        <?php if($loggedIn): ?>
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
                            <a id="commentIndicator_<?=$post["pid"]?>" class="commentIndicator" href="<?php echo "post.php?pid=".$post["pid"];?>">
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
        <span id="ajaxToggle">Auto-Update Enabled</span>
		<hr/>
		&copy; Brayden Streibel 2015
	</footer>
</body>
<?php if ($loggedIn) { ?><script type="text/javascript" src="scripts/indexr.js"></script><?php } ?>
<script type="text/javascript" src="scripts/ajaxr.js"></script>
</html>