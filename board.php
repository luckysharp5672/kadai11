<?php
session_start();
require 'dbconnect.php';

// URLからidパラメータを取得
$id = $_GET['id'];

// メッセージを取得
$query = "SELECT * FROM messages WHERE board_id = :board_id ORDER BY created_at ASC";
$statement = $pdo->prepare($query);
$statement->execute([':board_id' => $id]);
$messages = $statement->fetchAll(PDO::FETCH_ASSOC);

// ログインしているかをチェックし、username を取得
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$_SESSION['board_id'] = $id;

// 掲示板のタイトルを取得
$query = "SELECT * FROM boards WHERE id = :id";
$statement = $pdo->prepare($query);
$statement->execute([':id' => $id]);
$board = $statement->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>掲示板</title>
    <style>
        #chatContainer {
            display: flex;
            flex-direction: column;
        }

        .chatBubble {
            max-width: 80%;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .chatBubble.right {
            background-color: #dcf8c6;
            text-align: right;
        }

        .chatBubble.left {
            background-color: #f0f0f0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div>
        <div style="display: flex;">
            <h1 style="margin-right: 200px;">掲示板タイトル：<?php echo htmlspecialchars($board['boardTitle'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <a href="logout.php">ログアウト</a>
        </div>
        <div>
            投稿者：<span id="username"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div>
            <form action="post_message.php" method="post">
                <input type="hidden" name="board_id" value="<?php echo $id; ?>">
                <textarea name="message" id="message" cols="30" rows="10"></textarea>
                <button id="send" type="submit">送信</button>
            </form>
        </div>
        <div id="chatContainer">
            <?php
            foreach ($messages as $message) {
                echo '<div class="chatBubble ' . ($message['user_id'] === $_SESSION['user_id'] ? 'right' : 'left') . '">';
                echo htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8');
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById('send').addEventListener('click', function(event) {
            event.preventDefault();

            var text = document.getElementById('message').value;
            var username = document.getElementById('username').textContent;
            document.getElementById('message').value = '';

            var formData = new FormData();
            formData.append('message', text);
            formData.append('board_id', <?php echo $id; ?>);

            fetch('post_message.php', {
                method: 'POST',
                body: formData
            }).then(function(response) {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error: ' + response.statusText);
                }
            });
        });
    </script>
</body>
</html>
