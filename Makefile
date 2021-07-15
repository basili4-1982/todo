env=dev

up:
		docker-compose  -f ./docker/docker-compose.yml up -d

up-prod:
	docker-compose  -f ./docker/prod-docker-compose.yml up -d

down:
	docker-compose  -f ./docker/docker-compose.yml down


build:
	docker-compose  -f ./docker/docker-compose.yml build


init:
	$(info  Разворачивание окружения: "$(env)")
	cp  ./environment/$(env)/.?*.* ./app

test:
	@echo $(env)