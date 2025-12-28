ssh:
	docker exec -it tempfastmail_php bash
deploy-prod:
	docker compose -f compose.prod.yaml build --pull --no-cache && docker compose stop php && docker compose -f compose.prod.yaml up --wait
