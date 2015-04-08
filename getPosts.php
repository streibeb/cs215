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
    WHERE Posts.timestamp >= '$updateTime'
    ORDER BY Posts.pid";
} else {
    $query = "SELECT Posts.*,
      Users.first_name,
      Users.last_name,
      (SELECT COUNT(Comments.cid) FROM Comments WHERE Posts.pid = Comments.pid) AS 'numComments',
      (SELECT COUNT(1) FROM Likes WHERE Posts.pid = Likes.pid) AS 'numLikes'
    FROM Posts
    JOIN Users ON Posts.uid = Users.uid
    WHERE Posts.timestamp >= '$updateTime'
    ORDER BY Posts.pid";
}
$result = mysqli_query($db, $query);
$sResp = array();
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
    $sResp[] = $sRow;
}
echo json_encode($sResp);
mysqli_close($db);