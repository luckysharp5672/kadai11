<?php
session_start();
require 'dbconnect.php';

// メッセージを取得
$query = "SELECT * FROM boards";
$statement = $pdo->prepare($query);
$statement->execute();
$boards = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
            .board {
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
            }
    </style>
</head>
<body>
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