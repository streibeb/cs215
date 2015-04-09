<?php
session_start();
include_once("config.php");

$db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    die ("Failed to connect to database: " . mysqli_connect_error());
}

if (isset($_GET["updateTime"])) {
    $updateTime = date("Y-m-d H:i:s", $_GET["updateTime"]);
} else  {
    $updateTime = date("Y-m-d H:i:s", "0");
}

if (isset($_GET["page"])) {
    $page = ($_GET["page"] * 10)-1;
} else  {
    $page = 0;
}

$loggedIn = isset($_SESSION["uid"]);

$query = "SELECT COUNT(pid) as 'count' FROM Posts;";
$result = mysqli_query($db, $query);
$totalPages = 0;
if ($r = mysqli_fetch_assoc($result)){
    $totalPages = ceil($r["count"]/POSTS_PER_PAGE);
}
$postsPerPage = POSTS_PER_PAGE;
$sResp = array("pages" => $totalPages,"add" => array(), "update" => array());

// Get new posts
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
    ORDER BY Posts.timestamp
    LIMIT $page, $postsPerPage;";
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
    ORDER BY Posts.timestamp
    LIMIT $page, $postsPerPage;";
}

$result = mysqli_query($db, $query);
echo mysqli_error($db);
while ($row = mysqli_fetch_assoc($result)) {
    $sRow["pid"] = $row["pid"];
    $sRow["timestamp"] = $row["timestamp"];
    $sRow["content"] = $row["content"];
    $sRow["image"] = !is_null($row["image"]) ? USER_UPLOAD_DIRECTORY.$row["image"] : null;
    $sRow["first_name"] = $row["first_name"];
    $sRow["last_name"] = $row["last_name"];
    $sRow["numComments"] = $row["numComments"];
    $sRow["numLikes"] = $row["numLikes"];
    $sRow["userLiked"] = $row["userLiked"] ? true : false;
    $sResp["add"][] = $sRow;
}

// Get likes for posts on page
if ($loggedIn) {
    $uid = $_SESSION["uid"];
    $query = "SELECT Posts.pid,
       COUNT(Comments.cid) as 'numComments',
       COUNT(Likes.pid) as 'numLikes',
       (SELECT 1 FROM Likes WHERE Likes.uid = '$uid' AND Posts.pid = Likes.pid) AS 'userLiked'
     FROM Posts
     JOIN Users ON Posts.uid = Users.uid
     LEFT JOIN Comments ON Posts.pid = Comments.pid
     LEFT JOIN Likes ON Posts.pid = Likes.pid
     WHERE Posts.pid IN (SELECT * FROM (SELECT pid FROM Posts ORDER BY timestamp desc limit $page, $postsPerPage) as t)
     GROUP BY Posts.timestamp;";
} else {
    $query = "SELECT Posts.pid,
       COUNT(Comments.cid) as 'numComments',
       COUNT(Likes.pid) as 'numLikes'
     FROM Posts
       JOIN Users ON Posts.uid = Users.uid
       LEFT JOIN Comments ON Posts.pid = Comments.pid
       LEFT JOIN Likes ON Posts.pid = Likes.pid
     WHERE Posts.pid IN (SELECT * FROM (SELECT pid FROM Posts ORDER BY timestamp desc limit $page, $postsPerPage) as t)
     GROUP BY Posts.timestamp;";
}

$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $sRow["pid"] = $row["pid"];
    $sRow["numComments"] = $row["numComments"];
    $sRow["numLikes"] = $row["numLikes"];
    $sRow["userLiked"] = $row["userLiked"] ? true : false;
    $sResp["update"][] = $sRow;
}

echo json_encode($sResp);
mysqli_close($db);