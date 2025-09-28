cd api
composer install
cp env-example.php env.php
cd ../front-end
npm install
npm run build
cd ..
cp docker-compose-template.yml docker-compose.yml
docker compose -f 'docker-compose.yml' up -d --build
sleep 1
./init-db.sh

echo "Setup complete. Access the application at http://localhost:9000/login"