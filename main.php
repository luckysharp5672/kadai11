<?php
session_start();
require 'dbconnect.php';

// メッセージを取得
$query = "SELECT * FROM boards";
$statement = $pdo->prepare($query);
$statement->execute();
$boards = $statement->fetchAll(PDO::FETCH_ASSOC);

// ログインしているかをチェックし、username を取得
require_once('funcs.php');
loginCheck();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>さんぽデジタル掲示板</title>
    <style>
            .board {
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
            }
    </style>
    <script type='text/javascript' src='http://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=ApUslpO8ghJ6mpe35ApW427eo72OmGGHg9ETniAK37AnLv7g6GzjaiEkrMB1cowL' async defer></script>
    <script>
        // Initialize the boards variable in JavaScript
        var boards = <?php echo json_encode($boards); ?>;
    </script>
</head>
<body>
    <div style="display: flex;">
        <h1 style="margin-right: 200px;">さんぽデジタル掲示板</h1>
        <a href="logout.php">ログアウト</a>
    </div>
    <div>
        名前：<span id="currentUsername"><?php echo $username; ?></span>
    </div>
    <button id="getLocation">現在位置の取得</button>
    <div id="location">位置：未取得</div>
    <div id="myMap" style="position:relative;width:600px;height:400px;"></div>
    <form id="messageForm">
        <label for="boardTitle">新しい掲示板のタイトル:</label><br>
        <textarea id="boardTitle" name="boardTitle"></textarea><br>
        <button type="button" id="postButton">掲示板作成</button>
    </form>
    <br>
    <button id="searchButton">近くの掲示板を検索</button>

    <a href="select.php">掲示板管理</a>

    <div id="boards"><br>
    <?php if($_SESSION['kanri_flg'] === 1) { ?>
        <!-- 管理者専用の機能 -->
        <?php foreach ($boards as $board) { ?>
            <div class="board">
                <p>掲示板作成者: <?php echo $board['username']; ?></p>
                <p>掲示板タイトル: <span class="boardTitle"><?php echo $board['boardTitle']; ?></span></p>
                <p>緯度: <span class="latitude"><?php echo $board['latitude']; ?></span></p>
                <p>経度: <span class="longitude"><?php echo $board['longitude']; ?></span></p>
                <p>
                    <?php $board_id = $board['id']; ?>
                    <a href="board.php?id=<?= $board['id'] ?>">掲示板を開く</a>
                    <!-- 編集と削除ボタンを追加 -->
                    <a href="edit_form.php?id=<?php echo $board_id; ?>" target="_blank" class="editButton" data-id="<?php echo $board_id; ?>">編集</a>
                    <button class="deleteButton" data-id="<?php echo $board_id; ?>">削除</button>
                </p>
            </div>
        <?php } ?>
    <?php } else { ?>
        <!-- 管理者でないユーザーが利用可能な機能 -->
        <?php foreach ($boards as $board) { ?>
            <div class="board">
                <p>ユーザー名: <?php echo $board['username']; ?></p>
                <p>掲示板タイトル: <span class="boardTitle"><?php echo $board['boardTitle']; ?></span></p>
                <p>緯度: <span class="latitude"><?php echo $board['latitude']; ?></span></p>
                <p>経度: <span class="longitude"><?php echo $board['longitude']; ?></span></p>
            </div>
        <?php } ?>
    <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        let map, currentLatitude, currentLongitude;
        let username = document.getElementById("currentUsername").textContent || "user";

        // 修正後のコード
        if (username === "") {
        // ユーザー名が取得できない場合の処理
        console.log("ユーザー名が取得できませんでした。");
        } else {
        // ユーザー名が取得できた場合の処理
        console.log("ユーザー名:", username);
        }

        function loadMapScenario() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                function (position) {
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;

                    map = new Microsoft.Maps.Map(document.getElementById("myMap"), {
                    center: new Microsoft.Maps.Location(
                        currentLatitude,
                        currentLongitude
                    ),
                    zoom: 15,
                    });

                    var center = map.getCenter();

                    var pin = new Microsoft.Maps.Pushpin(center, {
                    color: "red",
                    });

                    map.entities.push(pin);

                    // Loop through the boards array and add a green pushpin for each board
                    for (var i = 0; i < boards.length; i++) {
                        var location = new Microsoft.Maps.Location(boards[i]['latitude'], boards[i]['longitude']);
                        var pin = new Microsoft.Maps.Pushpin(location, { color: 'green' });
                        
                        // Create an infobox for each pin
                        var infobox = new Microsoft.Maps.Infobox(location, { 
                            title: boards[i]['username'], 
                            description: boards[i]['boardTitle'], 
                            visible: false
                        });

                        // Add an event handler to the pushpin
                        addInfoboxEventHandlers(pin, infobox);

                        map.entities.push(pin);
                        map.entities.push(infobox);
                    }

                    // 現在の位置が取得できたら、サーバにリクエストを送信
                    fetch("get_nearby_messages.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body:
                        "latitude=" +
                        encodeURIComponent(currentLatitude) +
                        "&longitude=" +
                        encodeURIComponent(currentLongitude),
                    })
                    .then((response) => response.json())
                    .then((nearbyMessages) => {
                        // 近くのメッセージを処理するコードをここに書く
                    });
                },
                function (error) {
                    console.error("Error Code = " + error.code + " - " + error.message);
                }
                );
            } else {
                console.error("Your browser doesn't support the Geolocation API");
            }
        }

        document.getElementById("getLocation").addEventListener("click", function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
            function (position) {
                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;
                console.log(currentLatitude, currentLongitude);

                var location = new Microsoft.Maps.Location(
                currentLatitude,
                currentLongitude
                );
                var pushpin = new Microsoft.Maps.Pushpin(location, { color: "blue" });
                map.entities.push(pushpin);

                map.setView({ center: location, zoom: 15 });

                // 現在位置を表示
                document.getElementById("location").innerHTML =
                "位置：緯度 " + currentLatitude + " 経度 " + currentLongitude;
            },
            function (error) {
                console.error("Error occurred. Error code: " + error.code);
            }
            );
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
        });

        document.getElementById("postButton").addEventListener("click", function () {
            // メッセージを取得
            let boardTitle = document.getElementById("boardTitle").value;

            if (boardTitle == "") {
                alert("タイトルを入力してください。");
                return;
            }

            // ユーザー名、メッセージ、緯度、経度をPOSTデータとしてエンコード
            var postData = `username=${encodeURIComponent(username)
            }&boardTitle=${encodeURIComponent(boardTitle)
            }&latitude=${encodeURIComponent(currentLatitude)
            }&longitude=${encodeURIComponent(currentLongitude)}`;

            // post_board.phpエンドポイントにPOSTリクエストを送信
            fetch("post_board.php", {
                method: "POST",
                headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                },
                body: postData,
            })
                .then((response) => response.text())
                .then((result) => {
                console.log(result); // 新しいHTMLファイル名が出力される

            // 新しいHTMLファイルへのリンクを作成し、メッセージ一覧に追加する
            var link = document.createElement("a");
            link.href = "board.php?id=" + result;
            link.textContent = boardTitle;
            link.target = "_blank";

            var messageItem = document.createElement("div");
            messageItem.className = "message";
            messageItem.appendChild(link);

            var boards = document.getElementById("boards");
            boards.appendChild(messageItem);

            // 新しいHTMLに遷移する
            window.location.href = "board.php?id=" + result;
            });
        });

        function addInfoboxEventHandlers(pin, infobox) {
            Microsoft.Maps.Events.addHandler(pin, 'mouseover', makeInfoboxVisible(infobox));
            Microsoft.Maps.Events.addHandler(pin, 'mouseout', makeInfoboxInvisible(infobox));
        }

        function makeInfoboxVisible(infobox) {
            return function() {
                infobox.setOptions({ visible: true });
            }
        }

        function makeInfoboxInvisible(infobox) {
            return function() {
                infobox.setOptions({ visible: false });
            }
        }

        // 近くの掲示板を検索する
        document.getElementById('searchButton').addEventListener('click', function() {
            // 現在の位置情報を取得
            navigator.geolocation.getCurrentPosition(function(position) {
                var currentLatitude = position.coords.latitude;
                var currentLongitude = position.coords.longitude;

                // 位置情報をエンコード
                var postData = `latitude=${encodeURIComponent(currentLatitude)}&longitude=${encodeURIComponent(currentLongitude)}`;

                // get_nearby_boards.phpにPOSTリクエストを送信
                fetch("get_nearby_boards.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: postData,
                })
                .then((response) => response.json())
                .then((result) => {
                    var boards = document.getElementById('boards');
                    boards.innerHTML = '';  // 元の内容をクリア

                    // 結果のリストをループして、各掲示板のリンクを作成
                    result.forEach(function(board) {
                        var link = document.createElement('a');
                        link.href = "board.php?id=" + board.id;
                        link.textContent = board.boardTitle;

                        var boardItem = document.createElement('div');
                        boardItem.appendChild(link);
                        boards.appendChild(boardItem);
                    });
                });
            });
        });

    </script>
    
    <!-- 掲示板の編集、削除のために追加 -->
    <script>
    $(document).on("click", ".editButton", function () {
    console.log("Edit button clicked."); // この行を追加
    var messageId = $(this).data("id"); // メッセージIDを取得
    var boardTitle = $(this).closest(".board").find(".boardTitle").text(); // メッセージの内容を取得
    var newBoardTitle = prompt("新しいメッセージを入力してください", boardTitle); // 新しいメッセージをユーザに入力させる

    // ユーザが新しいメッセージを入力した場合、サーバに更新を依頼する
    if (newBoardTitle !== null) {
        $.ajax({
        url: "edit_form.php", // メッセージを編集するPHPスクリプトのURL
        type: "POST",
        data: {
            id: messageId,
            boardTitle: newBoardTitle,
        },
        success: function () {
            location.reload(); // ページをリロードして更新を反映する
        },
        });
    }
    });

    document.querySelectorAll(".deleteButton").forEach(function (deleteButton) {
    deleteButton.addEventListener("click", function () {
        var id = this.getAttribute("data-id");

        // ユーザに確認を取る
        if (!confirm("本当に削除しますか？")) {
        return;
        }

        // レコードのIDをサーバにPOSTする
        fetch("delete_message.php", {
        method: "POST",
        body: new URLSearchParams("id=" + id),
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        })
        .then(function (response) {
            if (!response.ok) {
            throw new Error("HTTP error " + response.status);
            }

            // レコードが削除されたら、ページをリロードする
            location.reload();
        })
        .catch(function (error) {
            console.error("Error:", error);
        });
    });
    });
    </script>
    <script>
    window.onload = function() {
        <?php if ($_SESSION['registration_success']): ?>
            alert("登録が完了しました。");
            <?php
            // メッセージが表示されたらフラグをリセット
            $_SESSION['registration_success'] = false;
            ?>
        <?php endif; ?>
    }
    </script>
</body>
</html>

