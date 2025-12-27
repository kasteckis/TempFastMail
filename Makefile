ssh:
	docker exec -it tempfastmail_php bash
deploy-prod:
	docker compose -f compose.prod.yaml build --pull --no-cache && docker compose down && docker compose -f compose.prod.yaml up --wait
