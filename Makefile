dev:
	@docker-compose down && \
		docker-compose build --pull --no-cache && \
		docker-compose \
			-f docker-compose.yml \
		up -d --remove-orphans