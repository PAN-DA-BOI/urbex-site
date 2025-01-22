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
function addUser($username, $password, $color, $profilePicture, $permissions) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true);
    $users[] = [
        'username' => $username,
        'password' => $passwordHash,
        'color' => $color,
        'profilePicture' => $profilePicture,
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

// Function to write a blog post
function writeBlogPost($title, $content) {
    $blogDir = 'blog/';
    $fileName = strtolower(str_replace(' ', '_', $title)) . '.html';
    $fileContent = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>$title</title>\n    <link rel=\"stylesheet\" href=\"../styles.css\">\n</head>\n<body>\n    <h1>$title</h1>\n    <div>$content</div>\n</body>\n</html>";
    file_put_contents($blogDir . $fileName, $fileContent);
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
        $color = $_POST['color'];
        $profilePicture = $_FILES['profilePicture']['name'];
        $permissions = [
            'map' => isset($_POST['map']) ? true : false,
            'kits' => isset($_POST['kits']) ? true : false,
            'vote' => isset($_POST['vote']) ? true : false
        ];
        addUser($username, $password, $color, $profilePicture, $permissions);
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
    } elseif (isset($_POST['write_blog'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        writeBlogPost($title, $content);
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Developer Dashboard</h1>

    <h2>Add Location</h2>
    <form method="post" action="dev.php" class="dashboard-form">
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
        <button type="submit" name="add_location" class="dashboard-button">Add Location</button>
    </form>

    <h2>Add User</h2>
    <form method="post" action="dev.php" class="dashboard-form" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="color">Color:</label>
        <input type="color" id="color" name="color" required><br>
        <label for="profilePicture">Profile Picture:</label>
        <input type="file" id="profilePicture" name="profilePicture" required><br>
        <label for="map">Map Access:</label>
        <input type="checkbox" id="map" name="map"><br>
        <label for="kits">Kits Access:</label>
        <input type="checkbox" id="kits" name="kits"><br>
        <label for="vote">Vote Access:</label>
        <input type="checkbox" id="vote" name="vote"><br>
        <button type="submit" name="add_user" class="dashboard-button">Add User</button>
    </form>

    <h2>Update User Permissions</h2>
    <form method="post" action="dev.php" class="dashboard-form">
        <label for="update_username">Username:</label>
        <select id="update_username" name="username" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo htmlspecialchars($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
            <?php endforeach; ?>
        </select><br>
        <label for="update_map">Map Access:</label>
        <input type="checkbox" id="update_map" name="map"><br>
        <label for="update_kits">Kits Access:</label>
        <input type="checkbox" id="update_kits" name="kits"><br>
        <label for="update_vote">Vote Access:</label>
        <input type="checkbox" id="update_vote" name="vote"><br>
        <button type="submit" name="update_permissions" class="dashboard-button">Update Permissions</button>
    </form>

    <h2>Add PDF</h2>
    <form method="post" action="dev.php" enctype="multipart/form-data" class="dashboard-form">
        <label for="pdf">Choose PDF:</label>
        <input type="file" id="pdf" name="pdf" required><br>
        <button type="submit" name="add_pdf" class="dashboard-button">Add PDF</button>
    </form>

    <h2>Write Blog Post</h2>
    <form method="post" action="dev.php" class="dashboard-form">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br>
        <button type="submit" name="write_blog" class="dashboard-button">Write Blog Post</button>
    </form>
</body>
</html>
