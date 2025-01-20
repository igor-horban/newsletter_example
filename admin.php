<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $recipients = isset($_POST['recipients']) ? $_POST['recipients'] : [];
    $attachments = isset($_FILES['attachments']) ? $_FILES['attachments'] : [];

    // Обробка вкладень
    $upload_dir = 'uploads/';
    $uploaded_files = [];
    foreach ($attachments['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($attachments['name'][$key]);
        $target_file = $upload_dir . $file_name;
        move_uploaded_file($tmp_name, $target_file);
        $uploaded_files[] = $target_file;
    }

    // Надсилання листів обраним отримувачам
    foreach ($recipients as $email) {
        $body = file_get_contents('templates/news_email.html');
        $body = str_replace('{{message}}', $message, $body);
        sendMail($email, $subject, $body, $uploaded_files);
    }

    $success = "Листи надіслано успішно!";
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін-панель</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/antd/dist/antd.min.css">
</head>
<body>
    <div class="container" style="margin: 50px auto; max-width: 800px;">
        <h1>Адмін-панель</h1>
        <?php if (isset($success)): ?>
            <div class="ant-alert ant-alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="ant-form ant-form-horizontal">
            <div class="ant-form-item">
                <label>Тема</label>
                <input type="text" name="subject" class="ant-input" required>
            </div>
            <div class="ant-form-item">
                <label>Повідомлення</label>
                <textarea name="message" class="ant-input" required></textarea>
            </div>
            <div class="ant-form-item">
                <label>Оберіть отримувачів</label>
                <?php
                    $stmt = $conn->query("SELECT id, name, email FROM users");
                    while ($row = $stmt->fetch_assoc()):
                ?>
                <div>
                    <input type="checkbox" name="recipients[]" value="<?= $row['email']; ?>">
                    <?= $row['name']; ?> (<?= $row['email']; ?>)
                </div>
                <?php endwhile; ?>
            </div>
            <div class="ant-form-item">
                <label>Вкладення</label>
                <input type="file" name="attachments[]" multiple class="ant-input">
            </div>
            <button type="submit" class="ant-btn ant-btn-primary">Надіслати</button>
        </form>
    </div>
</body>
</html>