<?php
session_start();
include_once("config.php");

if (isset($_SESSION["uid"])) {
    $uid = $_SESSION["uid"];
    $pid = $_POST["pid"];

    if (isset($_POST["add"])) {
        $sResp = array();
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }
        $timestamp = date("Y-m-d H:i:s");
        $query = "INSERT IGNORE INTO Likes (pid, uid, timestamp) VALUES ('$pid','$uid','$timestamp');";
        $result = mysqli_query($db, $query);
        if ($result) {
            $sRow["userLiked"] = true;
            $rResp[] = $sRow;
        }

        $query = "SELECT COUNT(1) as numLikes FROM Likes WHERE Likes.pid = '$pid';";
        $result = mysqli_query($db, $query);
        if ($result) {
            $sResp = array();
            while($row = mysqli_fetch_assoc($result)) {
                $sRow["numLikes"] = $row["numLikes"];
                $sResp[] = $sRow;
            }
            echo json_encode($sResp);
        }

        mysqli_close($db);
    } else if (isset($_POST["remove"])) {
        $sResp = array();
        $db = mysqli_connect(DB_HOST_NAME, DB_USER, DB_PASS, DB_NAME);
        if (!$db) {
            die ("Failed to connect to database: " . mysqli_connect_error());
        }
        $query = "DELETE FROM Likes WHERE pid = '$pid' AND uid = '$uid';";
        $result = mysqli_query($db, $query);
        if ($result) {
            $sRow["userLiked"] = false;
            $rResp[] = $sRow;
        }
        $query = "SELECT COUNT(1) as numLikes FROM Likes WHERE Likes.pid = '$pid';";
        $result = mysqli_query($db, $query);
        if ($result) {
            while($row = mysqli_fetch_assoc($result)) {
                $sRow["numLikes"] = $row["numLikes"];
                $sResp[] = $sRow;
            }
            echo json_encode($sResp);
        }

        mysqli_close($db);
    }
}