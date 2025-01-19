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
    { path: 'theater/theater.html', folder: 'movie-theater' },
    { path: 'theater/image (1).jpg', folder: 'movie-theater' },
    { path: 'theater/image (2).jpg', folder: 'movie-theater' },
    { path: 'theater/image (3).jpg', folder: 'movie-theater' },
    { path: 'theater/image (4).jpg', folder: 'movie-theater' },
    { path: 'theater/image (5).jpg', folder: 'movie-theater' },
    { path: 'construction/construction.html', folder: 'construction' },
    { path: 'construction/image (1).jpg', folder: 'construction' },
    { path: 'hospital/hospital.html', folder: 'hospital' },
    { path: 'house/house.html', folder: 'house' },
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
    { lat: 40.88712, lng: -111.8952, notes: "Note: Old Cinemark", name: "Movie Theater", folder: 'movie-theater' },
    { lat: 40.742931, lng: -111.929172, notes: "Note: abandoned food world building", name: "Food World", folder: 'food-world' },
    { lat: 40.753712, lng: -111.900832, notes: "Note: multiple buildings in area", name: "Construction", folder: 'construction' },
    { lat: 41.225611, lng: -111.991836, notes: "Note: abandoned hospital", name: "Hospital", folder: 'hospital' },
    { lat: 40.901375, lng: -111.885932, notes: "Note: abandoned house", name: "House", folder: 'house' }
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
