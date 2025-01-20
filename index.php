<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Валідація електронної пошти
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Невірний формат електронної пошти!";
    } else {
        // Перевірка, чи електронна пошта вже зареєстрована
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Ця електронна пошта вже зареєстрована.";
        } else {
            // Додавання користувача в базу
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $phone);
            if ($stmt->execute()) {
                // Надсилання листа-привітання
                $body = file_get_contents('templates/welcome_email.html');
                $body = str_replace('{{name}}', htmlspecialchars($name), $body);
                sendMail($email, 'Ласкаво просимо!', $body);
                $success = "Реєстрація успішна! Лист надіслано.";
            } else {
                $error = "Помилка реєстрації.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/antd/dist/antd.min.css">
</head>
<body>
    <div class="container" style="margin: 50px auto; max-width: 1000px;">
        <h1 align="center"><b class="keywords">ПІДПИШІТЬСЯ НА РОЗСИЛКУ</b> та дізнавайтеся про новини першими!</h1>
        <?php if (isset($success)): ?>
            <div class="ant-alert ant-alert-success"><?= $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="ant-alert ant-alert-error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="ant-form ant-form-horizontal">
                <input type="text" name="name" placeholder="Ім'я" class="ant-input" style="margin-right: 22px; max-width: 225px;" required>
                <input type="email" name="email" placeholder="Електронна пошта" class="ant-input" style="margin-right: 22px; max-width: 225px;" required>
                <input type="text" name="phone" placeholder="Телефон" class="ant-input" style="margin-right: 22px; max-width: 225px;">
                <button type="submit" class="ant-btn ant-btn-primary" style="margin-right: 22px; width:225px;">Підписатися</button>
        </form>
    </div>
</body>
</html>