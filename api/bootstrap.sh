#!/bin/bash

# Create project directory structure
mkdir -p www

# Create nginx configuration
echo 'server {
    listen 80;
    root /var/www/html/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
    
    location ~ \.php$ {
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}' > nginx.conf

# Build and start containers
docker compose up -d

# Initialize a new Symfony project (adjust for your needs)
echo "Starting project initialization..."
echo "Please choose your project type:"
echo "1) Symfony (latest)"
echo "2) Symfony 7.2 (your target version)"
echo "3) Laravel (latest)"
echo "4) Empty project with Composer"
read -p "Enter your choice (1-4): " choice

case $choice in
  1)
    docker compose exec php-fpm composer create-project symfony/skeleton .
    ;;
  2)
    docker compose exec php-fpm composer create-project symfony/skeleton:"7.2.*" .
    ;;
  3)
    docker compose exec php-fpm composer create-project laravel/laravel .
    ;;
  4)
    docker compose exec php-fpm composer init
    ;;
  *)
    echo "Invalid choice"
    exit 1
    ;;
esac

# Install additional dependencies for Symfony
if [[ $choice -eq 1 || $choice -eq 2 ]]; then
  docker compose exec php-fpm composer require symfony/webapp-pack
fi

echo "Project initialization complete! Your environment is ready."
echo "Access your website at: http://localhost"
echo "Access PHPMyAdmin at: http://localhost:8080"