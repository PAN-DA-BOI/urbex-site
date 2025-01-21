<?php
session_start();

// Check if the user is logged in and is PAN-DA-BOI
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'PAN-DA-BOI') {
    header('Location: index.html');
    exit();
}

// Function to add a new location
function addLocation($name, $lat, $lng, $notes, $folder) {
    // Update map.html
    $mapHtmlFile = 'map/map.html';
    $mapHtmlContent = file_get_contents($mapHtmlFile);
    $newLocationButton = '<button class="location-button" data-lat="' . $lat . '" data-lng="' . $lng . '" data-notes="' . $notes . '" data-folder="' . $folder . '">' . $name . '</button>';
    $mapHtmlContent = substr_replace($mapHtmlContent, $newLocationButton . "\n", strpos($mapHtmlContent, '<div class="main-part">') + 26, 0);
    file_put_contents($mapHtmlFile, $mapHtmlContent);

    // Update map.js
    $mapJsFile = 'map/map.js';
    $mapJsContent = file_get_contents($mapJsFile);
    $newLocationEntry = "{ lat: $lat, lng: $lng, notes: \"$notes\", name: \"$name\", folder: '$folder' }";
    $mapJsContent = substr_replace($mapJsContent, $newLocationEntry . ",\n", strpos($mapJsContent, '];') - 1, 0);
    file_put_contents($mapJsFile, $mapJsContent);
}

// Function to add a new user
function addUser($username, $password) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true);
    $users[] = [
        'username' => $username,
        'password' => $passwordHash
    ];
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Function to start a new vote
function startVote() {
    // Reset vote counts
    $votesFile = 'vote/votes.json';
    file_put_contents($votesFile, json_encode(['option1' => 0, 'option2' => 0, 'option3' => 0], JSON_PRETTY_PRINT));

    // Reset vote logs
    $voteLogsFile = 'vote/vote_logs.json';
    file_put_contents($voteLogsFile, json_encode([], JSON_PRETTY_PRINT));

    // Start the countdown timer (this would typically be handled by the server-side script)
    // For demonstration purposes, we'll just log the action
    error_log('Vote started');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_location'])) {
        $name = $_POST['name'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $notes = $_POST['notes'];
        $folder = $_POST['folder'];
        addLocation($name, $lat, $lng, $notes, $folder);
        header('Location: dev.php');
        exit();
    } elseif (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        addUser($username, $password);
        header('Location: dev.php');
        exit();
    } elseif (isset($_POST['start_vote'])) {
        startVote();
        header('Location: dev.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Developer Dashboard</h1>

    <h2>Add Location</h2>
    <form method="post" action="dev.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="lat">Latitude:</label>
        <input type="text" id="lat" name="lat" required><br>
        <label for="lng">Longitude:</label>
        <input type="text" id="lng" name="lng" required><br>
        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes" required><br>
        <label for="folder">Folder:</label>
        <input type="text" id="folder" name="folder" required><br>
        <button type="submit" name="add_location">Add Location</button>
    </form>

    <h2>Add User</h2>
    <form method="post" action="dev.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit" name="add_user">Add User</button>
    </form>

    <h2>Start Vote</h2>
    <form method="post" action="dev.php">
        <button type="submit" name="start_vote">Start Vote</button>
    </form>
</body>
</html>
