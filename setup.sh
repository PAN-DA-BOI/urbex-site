#!/bin/bash

# Update and upgrade the package list
sudo apt-get update
sudo apt-get upgrade -y

# Install Apache2
sudo apt-get install apache2 -y

# Install Node.js and npm
sudo apt-get install nodejs npm -y

# Install Git
sudo apt-get install git -y

# Clone the GitHub repository
git clone https://github.com/PAN-DA-BOI/urbex-site.git

# Navigate to the project directory
cd urbex-site

# Install any necessary npm packages (if you have a package.json file)
npm install

# Set up the WebSocket server
echo "Setting up the WebSocket server..."
node_modules/.bin/nodemon server.js &

# Copy the project files to the Apache web root
sudo cp -r * /var/www/html/

# Ensure the correct permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Create a password file for basic authentication
sudo htpasswd -c /etc/apache2/.htpasswd pandaboi
sudo htpasswd /etc/apache2/.htpasswd kitty
sudo htpasswd /etc/apache2/.htpasswd InTheWoods
sudo htpasswd /etc/apache2/.htpasswd terraferma
sudo htpasswd /etc/apache2/.htpasswd user

# Configure Apache for basic authentication
sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf" <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/map>
        AuthType Basic
        AuthName "Restricted Content"
        AuthUserFile /etc/apache2/.htpasswd
        Require valid-user
    </Directory>

    <Directory /var/www/html/vote>
        AuthType Basic
        AuthName "Restricted Content"
        AuthUserFile /etc/apache2/.htpasswd
        Require valid-user
    </Directory>

    <Directory /var/www/html/kits>
        AuthType Basic
        AuthName "Restricted Content"
        AuthUserFile /etc/apache2/.htpasswd
        Require valid-user
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Restart Apache to apply changes
sudo systemctl restart apache2

echo "Setup complete. Your project is now available at http://localhost"
