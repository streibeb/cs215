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

$loggedIn = isset($_SESSION["uid"]);

$sResp = array("post" => array(), "comments" => array());

// Get new comments
$uid = $_SESSION["uid"];
$pid = $_GET["pid"];
$query = "SELECT Comments.*,
  Users.first_name,
  Users.last_name
FROM Comments
  JOIN Users ON Comments.uid = Users.uid
WHERE Comments.timestamp >= '$updateTime' AND Comments.pid = '$pid'
GROUP BY Comments.timestamp
ORDER BY Comments.timestamp DESC;";

$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $sRow["cid"] = $row["cid"];
    $sRow["timestamp"] = $row["timestamp"];
    $sRow["content"] = $row["content"];
    $sRow["image"] = !is_null($row["image"]) ? USER_UPLOAD_DIRECTORY.$row["image"] : null;
    $sRow["first_name"] = $row["first_name"];
    $sRow["last_name"] = $row["last_name"];
    $sResp["comments"][] = $sRow;
}

// Get likes for posts on page
$query = "SELECT Posts.pid,
   COUNT(Likes.pid) as 'numLikes',
   (SELECT 1 FROM Likes WHERE Likes.uid = '$uid' AND Posts.pid = Likes.pid) AS 'userLiked'
 FROM Posts
 LEFT JOIN Likes ON Posts.pid = Likes.pid
 WHERE Posts.pid = '$pid';";

$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $sRow["pid"] = $row["pid"];
    $sRow["numLikes"] = $row["numLikes"];
    $sRow["userLiked"] = $row["userLiked"] ? true : false;
    $sResp["update"][] = $sRow;
}
echo json_encode($sResp);
mysqli_close($db);