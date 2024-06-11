<pre
<form action="index.php" method="post" enctype="multipart/form-data">
    <label for="userfile">Upload CSV or JSON File:</label>
    <input type="file" name="userfile" accept=".csv,.json">
    <button type="submit">Upload</button>
</form>

<h1>Отправить данные с помощью GET</h1>

<form action="index.php" method="get">
    <label for="id">ID:</label>
    <input type="text" name="id" required><br>
    <label for="name">Name:</label>
    <input type="text" name="name" required><br>
    <label for="email">Email:</label>
    <input type="email" name="email" required><br>
    <label for="article">Article:</label>
    <input type="text" name="article"><br>
    <button type="submit">Submit</button>
</form>
    </pre>
<?php
function saveUser($user) {
    $dir = 'user';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = "{$dir}/{$user['id']}{$user['name']}.txt";
    $userData = json_encode($user, JSON_PRETTY_PRINT);

    file_put_contents($fileName, $userData);
}

function processUser($id, $name, $email, $article = null) {
    $dateCreated = date('Y-m-d H:i:s');
    $dateModified = $dateCreated;

    return [
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'article' => $article ?? "Default Article",
        'date_created' => $dateCreated,
        'date_modified' => $dateModified
    ];
}

// Обработка данных из файла
if ($_FILES && isset($_FILES['userfile'])) {
    $uploadedFile = $_FILES['userfile']['tmp_name'];
    $fileType = mime_content_type($uploadedFile);

    if ($fileType == 'application/json') {
        $data = json_decode(file_get_contents($uploadedFile), true);
    } else {
        $data = [];
        if (($handle = fopen($uploadedFile, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                $data[] = [
                    'id' => $row[0],
                    'name' => $row[1],
                    'email' => $row[2],
                    'article' => $row[3] ?? "Default Article",
                ];
            }
            fclose($handle);
        }
    }

    foreach ($data as $user) {
        $processedUser = processUser($user['id'], $user['name'], $user['email'], $user['article']);
        saveUser($processedUser);  //
    }
}

// Обработка данных из GET параметров
if ($_GET && isset($_GET['id'], $_GET['name'], $_GET['email'])) {
    $id = $_GET['id'];
    $name = $_GET['name'];
    $email = $_GET['email'];
    $article = $_GET['article'] ?? null;

    $user = processUser($id, $name, $email, $article);
    saveUser($user);  //
}


echo "Данные успешно сохранены.";