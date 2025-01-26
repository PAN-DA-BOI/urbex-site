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
function addUser($username, $password, $permissions) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true);
    $users[] = [
        'username' => $username,
        'password' => $passwordHash,
        'permissions' => $permissions
    ];
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Function to update user permissions
function updatePermissions($username, $permissions) {
    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['permissions'] = $permissions;
        }
    }
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Function to add a new PDF
function addPdf($file) {
    $targetDir = 'vote/pdf/';
    $targetFile = $targetDir . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $targetFile);
}

// Function to update the text file
function updateTextFile($content) {
    $filePath = 'path/to/your/textfile.txt';
    file_put_contents($filePath, $content);
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
        $permissions = [
            'map' => isset($_POST['map']) ? true : false,
            'kits' => isset($_POST['kits']) ? true : false,
            'vote' => isset($_POST['vote']) ? true : false
        ];
        addUser($username, $password, $permissions);
        header('Location: dev.php');
        exit();
    } elseif (isset($_POST['update_permissions'])) {
        $username = $_POST['username'];
        $permissions = [
            'map' => isset($_POST['map']) ? true : false,
            'kits' => isset($_POST['kits']) ? true : false,
            'vote' => isset($_POST['vote']) ? true : false
        ];
        updatePermissions($username, $permissions);
        header('Location: dev.php');
        exit();
    } elseif (isset($_POST['add_pdf'])) {
        addPdf($_FILES['pdf']);
        header('Location: dev.php');
        exit();
    } elseif (isset($_POST['update_text'])) {
        $content = $_POST['content'];
        updateTextFile($content);
        header('Location: dev.php');
        exit();
    }
}

// Load users for permission update form
$usersFile = 'users.json';
$users = json_decode(file_get_contents($usersFile), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
    <div class="header">
        DEVELOPMENT
    </div>
    <div class="container">
        <div class="top">
            <div class="sect1">
                <div class="box">
                    <h1>CPU</h1>
                    <p>CPU Temp: </p>
                    <p>Usage: </p>
                </div>
                <div class="box">
                    <h1>RAM</h1>
                    <p>Usage: </p>
                </div>
                <div class="box">
                    <h1>NETWORK</h1>
                    <p>Network Info: </p>
                </div>
            </div>
            <div class="sect2">
                <div class="title-container">
                    <h1>Vote</h1>
                    <button class="deploy-button" onclick="document.getElementById('voteForm').submit()">Deploy</button>
                </div>
                <div class="box-container">
                    <form id="voteForm" method="post" enctype="multipart/form-data">
                        <div class="box">
                            <p>Option 1</p>
                            <input type="file" name="file1">
                            <button type="submit" name="add_pdf">Remove</button>
                        </div>
                        <div class="box">
                            <p>Option 2</p>
                            <input type="file" name="file2">
                            <button type="submit" name="add_pdf">Remove</button>
                        </div>
                        <div class="box">
                            <p>Option 3</p>
                            <input type="file" name="file3">
                            <button type="submit" name="add_pdf">Remove</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="bottom">
            <div class="sect3">
                <div class="grid-container">
                    <div class="grid-header">USERNAME</div>
                    <div class="grid-header">MAP</div>
                    <div class="grid-header">KIT</div>
                    <div class="grid-header">VOTE</div>
                    <div class="grid-header">DEV</div>
                    <div class="grid-header">ONLINE</div>
                    <div class="grid-header">BAN</div>
                    <?php foreach ($users as $user): ?>
                        <div class="grid-item"><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="grid-item"><label class="switch"><input type="checkbox" <?php echo $user['permissions']['map'] ? 'checked' : ''; ?> name="map[<?php echo htmlspecialchars($user['username']); ?>]"><span class="slider"></span></label></div>
                        <div class="grid-item"><label class="switch"><input type="checkbox" <?php echo $user['permissions']['kits'] ? 'checked' : ''; ?> name="kits[<?php echo htmlspecialchars($user['username']); ?>]"><span class="slider"></span></label></div>
                        <div class="grid-item"><label class="switch"><input type="checkbox" <?php echo $user['permissions']['vote'] ? 'checked' : ''; ?> name="vote[<?php echo htmlspecialchars($user['username']); ?>]"><span class="slider"></span></label></div>
                        <div class="grid-item"><label class="switch"><input type="checkbox" name="dev[<?php echo htmlspecialchars($user['username']); ?>]"><span class="slider"></span></label></div>
                        <div class="grid-item"><?php echo $user['online'] ? 'Online' : 'Offline'; ?></div>
                        <div class="grid-item"><button type="submit" name="ban[<?php echo htmlspecialchars($user['username']); ?>]">Ban</button></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sect4">
                <div class="top-container">
                    <div class="upload-container">
                        <label for="imageUpload" class="custom-upload-button">Upload Image</label>
                        <input type="file" id="imageUpload" class="image-upload" style="display:none;" name="imageUpload">
                    </div>
                    <div class="deploy-container">
                        <button class="deploy-button-sect4" onclick="document.getElementById('textForm').submit()">Deploy</button>
                    </div>
                </div>
                <div class="text-edit-container">
                    <form id="textForm" method="post">
                        <textarea class="text-edit" name="content" placeholder="Enter text here..."></textarea>
                        <button type="submit" name="update_text">Update Text</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
