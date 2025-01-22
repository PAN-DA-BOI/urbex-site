<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user'];
    $color = $_POST['color'];
    $profilePicture = $_FILES['profilePicture']['name'];

    // Update user settings
    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['color'] = $color;
            if ($profilePicture) {
                $targetDir = 'profile_pictures/';
                $targetFile = $targetDir . basename($profilePicture);
                move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile);
                $user['profilePicture'] = $targetFile;
            }
        }
    }
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

    echo json_encode(['message' => 'Settings updated successfully']);
}
?>
