var map = L.map('map').setView([40.88067708994584, -111.87888418137246], 20);

// Add a tile layer to the map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Define the files at the top
var files = [
    { path: 'food-world/food.html', folder: 'food-world' },
    { path: 'food-world/image (1).jpg', folder: 'food-world' },
    { path: 'food-world/image (2).jpg', folder: 'food-world' },
    { path: 'food-world/image (3).jpg', folder: 'food-world' },
    { path: 'food-world/image (4).jpg', folder: 'food-world' },
    { path: 'food-world/image (5).jpg', folder: 'food-world' },
    { path: 'food-world/image (6).jpg', folder: 'food-world' },
    { path: 'food-world/image (7).jpg', folder: 'food-world' },
    { path: 'food-world/image (8).jpg', folder: 'food-world' },
    { path: 'food-world/image (9).jpg', folder: 'food-world' },
    { path: 'food-world/image (10).jpg', folder: 'food-world' },
    { path: 'food-world/image (11).jpg', folder: 'food-world' },
	{ path: 'training/train.html', folder: 'training' },
	{ path: 'training/image (1).JPEG', folder: 'training' },
	{ path: 'training/image (2).JPEG', folder: 'training' },
	{ path: 'training/image (3).JPEG', folder: 'training' },
	{ path: 'training/image (4).JPEG', folder: 'training' },
	{ path: 'training/image (5).JPEG', folder: 'training' },
	{ path: 'training/image (6).JPEG', folder: 'training' },
	{ path: 'training/image (7).JPEG', folder: 'training' },
	{ path: 'training/image (8).JPEG', folder: 'training' },
	{ path: 'training/image (9).JPEG', folder: 'training' },
	{ path: 'training/image (10).JPEG', folder: 'training' },
	{ path: 'training/image (11).JPEG', folder: 'training' },
	{ path: 'training/image (12).JPEG', folder: 'training' },
	{ path: 'training/image (13).JPEG', folder: 'training' },
	{ path: 'training/image (14).JPEG', folder: 'training' },
	{ path: 'training/image (15).JPEG', folder: 'training' },
	{ path: 'training/image (16).JPEG', folder: 'training' },
	{ path: 'training/video (1).mp4', folder: 'training' },
	{ path: 'training/video (2).mp4', folder: 'training' },
	{ path: 'training/video (3).mp4', folder: 'training' },
	{ path: 'training/video (4).mp4', folder: 'training' },
	{ path: 'training/video (5).mp4', folder: 'training' },
	{ path: 'training/video (6).mp4', folder: 'training' },
	{ path: 'training/video (7).mp4', folder: 'training' },
	{ path: 'training/video (8).mp4', folder: 'training' },

    // Add other files here
];

// Create a mapping from folder names to file paths
var folderFiles = files.reduce((acc, file) => {
    if (!acc[file.folder]) {
        acc[file.folder] = [];
    }
    acc[file.folder].push(file.path);
    return acc;
}, {});

// Array of locations with predefined file lists
var locations = [
    //{ lat: 40.88712, lng: -111.8952, notes: "Note: Old Cinemark", name: "Movie Theater", folder: 'movie-theater' },
    { lat: 40.742931, lng: -111.929172, notes: "Note: abandoned food world building", name: "Food World", folder: 'food-world' },
    //{ lat: 40.753712, lng: -111.900832, notes: "Note: multiple buildings in area", name: "Construction", folder: 'construction' },
    //{ lat: 41.225611, lng: -111.991836, notes: "Note: abandoned hospital", name: "Hospital", folder: 'hospital' },
    //{ lat: 40.901375, lng: -111.885932, notes: "Note: Anson Call house", name: "Call House", folder: 'house' }, 	
	//{ lat: 40.752839, lng: -111.902072, notes: "Note: abandoned pickle building", name: "utah pickle co", folder: 'pickle' },
	//{ lat: 40.753135, lng: -111.902191, notes: "Note: Warehouse", name: "Warehouse", folder: 'Warehouse' }
	{ lat: 40.8741744, lng: -111.918811, notes: "Note: police training house", name: "Training House", folder: 'training' }
];

// Add markers to the map
locations.forEach(location => {
    L.marker([location.lat, location.lng]).addTo(map)
        .bindPopup(`<b>${location.name}</b><br>${location.notes}`);
});

// Add event listeners to the location buttons
document.querySelectorAll('.location-button').forEach(button => {
    button.addEventListener('click', function() {
        var lat = this.getAttribute('data-lat');
        var lng = this.getAttribute('data-lng');
        var folder = this.getAttribute('data-folder');
        map.setView([lat, lng], 20);

        // Debugging statement
        console.log(`Button clicked: ${this.textContent}, Folder: ${folder}`);

        // Find the corresponding location and display its files
        var location = locations.find(loc => loc.name === this.textContent);
        if (location && folderFiles[folder]) {
            var fileList = document.getElementById('file-list');
            fileList.innerHTML = ''; // Clear previous files
            folderFiles[folder].forEach(filePath => {
                var listItem = document.createElement('li');
                var link = document.createElement('a');
                link.href = `${filePath}`; // Assuming files are in a 'files' directory
                link.target = '_blank';
                link.textContent = filePath.split('/').pop(); // Display only the file name
                listItem.appendChild(link);
                fileList.appendChild(listItem);
            });
            document.getElementById('sidebar').style.display = 'block';
        } else {
            console.error(`Folder not found: ${folder}`);
        }
    });

    button.addEventListener('mouseover', function() {
        var notes = this.getAttribute('data-notes');
        document.getElementById('notes').innerText = notes;
    });

    button.addEventListener('mouseout', function() {
        document.getElementById('notes').innerText = '';
    });
});
