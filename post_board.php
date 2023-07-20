<?php
session_start();
require 'dbconnect.php';

// リクエストからユーザー名、掲示板タイトル、緯度、経度を取得
$username = $_POST['username'];
$boardTitle = $_POST['boardTitle'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// 掲示板タイトルをデータベースに保存
$query = "INSERT INTO boards (username, boardTitle, latitude, longitude) VALUES (:username, :boardTitle, :latitude, :longitude)";
$statement = $pdo->prepare($query);
$statement->execute([
    ':username' => $username,
    ':boardTitle' => $boardTitle,
    ':latitude' => $latitude,
    ':longitude' => $longitude
]);

// 最後に挿入された掲示板のIDを取得
$board_id = $pdo->lastInsertId();

echo $board_id;
