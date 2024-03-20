<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data</title>
    <link rel="stylesheet" type="text/css" href="../css/upload_style.css">
</head>
<body>
    <div class="container">
        <?php

        $dbhost = $_POST['host'];
        $dbname = $_POST['db_name'];
        $dbuser = $_POST['db_username'];
        $dbpass = $_POST['db_password'];
        $tableName = $_POST['table_name'];
        $port = $_POST['port'];

        $conn = pg_connect("host=$dbhost port=$port dbname=$dbname user=$dbuser password=$dbpass");

        if (!$conn) {
            echo "<div class='error'>Соединение разорвано: " . pg_last_error() . "</div>";
            die();
        }

        $login = $_POST['login'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE login='$login' AND password='$password'";
        $result = pg_query($conn, $query);

        if (!$result) {
            echo "<div class='error'>Ошибка при выполнении запроса</div>";
            die();
        }

        if (pg_num_rows($result) === 0) {
            echo "<div class='error'>Пользователь с указанным логином или паролем не найден</div>";
            die();
        }

        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file_data = file_get_contents($_FILES['file']['tmp_name']);
            $lines = explode("\n", $file_data);

            foreach ($lines as $line) {
                $fields = explode(",", $line);
                $date = $fields[0];
                $time = $fields[1];
                $value1 = $fields[2];
                $value2 = $fields[3];
                $value3 = $fields[4];
                $value4 = $fields[5];
                $volume = $fields[6];

                $query = "INSERT INTO $tableName (date, time, value1, value2, value3, value4, volume) 
                    VALUES ('$date', '$time', $value1, $value2, $value3, $value4, $volume)";
                $result = pg_query($conn, $query);

                if (!$result) {
                    echo "<div class='error'>Ошибка при вставке данных в базу данных</div>";
                    die();
                }
            }

            echo "<div class='success'>Данные успешно загружены в базу данных</div>";
        } else {
            echo "<div class='error'>Ошибка при загрузке файла</div>";
        }

        pg_close($conn);
        ?>
    </div>
</body>
</html>
