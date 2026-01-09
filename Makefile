start:
	docker compose up -d --build
ssh:
	docker exec -it tempfastmail_php bash
deploy-prod:
	docker compose -f docker-compose.demo.yaml build --pull --no-cache && docker compose stop php && docker compose -f docker-compose.demo.yaml up --wait
