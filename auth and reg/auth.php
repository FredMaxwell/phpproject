<?php
    session_start();

    function hashPassword($password, $salt) {
        return hash('sha512', $password . $salt);
    }

// Функция для загрузки пользователей из файла
    function loadUsers($file) {
        $users = [];
        if (file_exists($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                list($id, $username, $hashed_password) = explode('|', trim($line));
                $users[$username] = [
                    'id' => $id,
                    'username' => $username,
                    'hashed_password' => $hashed_password,
                ];
            }
        }
        return $users;
    }

// Функция для сохранения пользователя в файл
    function saveUser($file, $user) {
        $line = implode('|', $user) . "\n";
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    $usersFile = __DIR__ . '/users.txt';
    $salt = 'SOL';
    $users = loadUsers($usersFile);

// Регистрация
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (array_key_exists($username, $users)) {
            echo "Пользователь уже существует!";
        } else {
            $userId = count($users) + 1;
            $hashedPassword = hashPassword($password, $salt);
            $user = [
                'id' => $userId,
                'username' => $username,
                'hashed_password' => $hashedPassword,
            ];
            saveUser($usersFile, $user);
            echo "Регистрация прошла успешно!";
        }
    }

// Авторизация
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (array_key_exists($username, $users) && $users[$username]['hashed_password'] === hashPassword($password, $salt)) {
            $_SESSION['auth'] = true;
            $_SESSION['username'] = $username;
            echo "Авторизация успешна!";
        } else {
            echo "Неверное имя пользователя или пароль!";
        }
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        echo "Вы вышли из системы!";
    }

    $auth = !empty($_SESSION) && array_key_exists('auth', $_SESSION) && (bool)$_SESSION['auth'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация и Авторизация</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            position: relative;
        }
        form {
            margin-bottom: 20px;
        }
        input {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px auto;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
            opacity: 1;
            animation: fadeOut 5s forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>
<div class="container">
    <?php if ($logoutMessage): ?>
        <div class="message"><?php echo $logoutMessage; ?></div>
    <?php endif; ?>

    <?php if ($auth): ?>
        <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <form method="post">
            <button type="submit" name="logout">Выйти</button>
        </form>
    <?php else: ?>
        <h2>Регистрация</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="register">Зарегистрироваться</button>
        </form>

        <h2>Авторизация</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="login">Войти</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>