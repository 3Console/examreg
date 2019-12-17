ifndef u
u:=sotatek
endif

ifndef env
env:=dev
endif

OS:=$(shell uname)

docker-start:
	cp laravel-echo-server.json.example laravel-echo-server.json
	@if [ $(OS) = "Linux" ]; then\
		sed -i -e "s/localhost:8000/web:8000/g" laravel-echo-server.json; \
		sed -i -e "s/\"redis\": {}/\"redis\": {\"host\": \"redis\"}/g" laravel-echo-server.json; \
	else\
		sed -i '' -e "s/localhost:8000/web:8000/g" laravel-echo-server.json; \
		sed -i '' -e "s/\"redis\": {}/\"redis\": {\"host\": \"redis\"}/g" laravel-echo-server.json; \
	fi
	docker-compose up -d

docker-stop:
	docker-compose stop

docker-restart:
	docker-compose down
	make docker-start
	make docker-init-db-full
	make docker-link-storage

docker-connect: 
	docker exec -it examreg ash

init-app:
	cp .env.example .env
	composer install
	php artisan key:generate
	php artisan passport:keys
	php artisan migrate
	php artisan db:seed
	php artisan storage:link

docker-init:
	docker exec -it examreg make init-app
	rm -rf node_modules
	npm install

init-db-full:
	make autoload
	php artisan migrate:fresh
	php artisan db:seed

docker-init-db-full:
	docker exec -it examreg make init-db-full

docker-link-storage:
	docker exec -it examreg php artisan storage:link

init-db:
	make autoload
	php artisan migrate:fresh

start:
	php artisan serve

log:
	tail -f storage/logs/laravel.log

test-js:
	npm test

test-php:
	vendor/bin/phpcs --standard=phpcs.xml && vendor/bin/phpmd app text phpmd.xml

build:
	npm run dev

watch:
	npm run watch

docker-watch:
	docker exec -it examreg make watch

autoload:
	composer dump-autoload

cache:
	php artisan cache:clear && php artisan view:clear

docker-cache:
	docker exec examreg make cache

route:
	php artisan route:list

create-table:
	# Ex: make create-alter n=create_users_table t=users
	docker exec -it examreg php artisan make:migration $(n) --create=$(t)

model:
	php artisan make:model Models/$(n) -m

create-model:
	# Ex: make create-model n=Test
	# Result: app/Models/Test.php
	#         database/migrations/2018_01_05_102531_create_tests_table.php
	docker exec -it examreg php artisan make:model Models/$(n) -m

create-alter:
	# Ex: make create-alter n=add_votes_to_users_table t=users
	docker exec -it examreg php artisan make:migration $(n) --table=$(t)

deploy:
	ssh $(u)@$(h) "mkdir -p $(dir)"
	rsync -avhzL --delete \
				--no-perms --no-owner --no-group \
				--exclude .git \
				--exclude .idea \
				--exclude .env \
				--exclude laravel-echo-server.json \
				--exclude storage/*.key \
				--exclude node_modules \
				--exclude /vendor \
				--exclude bootstrap/cache \
				--exclude storage/logs \
				--exclude storage/framework \
				--exclude storage/app \
				--exclude public/storage \
				--exclude .env.example \
				. $(u)@$(h):$(dir)/

deploy-dev:
	make deploy h=192.168.1.208 dir=/var/www/examreg
	ssh $(u)@192.168.1.208 "cd /var/www/examreg"
