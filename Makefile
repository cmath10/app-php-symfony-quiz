TARGET_HEADER=@echo '===== ' $@
PHP_CONSOLE=docker-compose run --rm php php bin/console

.PHONY: db-migrations-list
db-migrations-list: ## Shortcut for doctrine:migrations:list
	$(PHP_CONSOLE) doctrine:migrations:list

.PHONY: db-migrations-diff
db-migrations-diff: ## Shortcut for doctrine:migrations:diff
	$(PHP_CONSOLE) doctrine:migrations:diff

.PHONY: db-migrate
db-migrate: ## Shortcut for doctrine:migrations:migrate
	$(PHP_CONSOLE) doctrine:migrations:migrate

.PHONY: db-migrate-prev
db-migrate-prev: ## Shortcut for doctrine:migrations:migrate prev
	$(PHP_CONSOLE) doctrine:migrations:migrate prev

.PHONY: db-fixtures-load
db-fixtures-load: ## Shortcut for doctrine:fixtures:load
	$(PHP_CONSOLE) doctrine:fixtures:load

.PHONY: help
help: ## Lists recipes
	@echo "Recipes:"
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk '\
	    BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#-- colors
$(call computable,CC_BLACK,$(shell tput -Txterm setaf 0 2>/dev/null))
$(call computable,CC_RED,$(shell tput -Txterm setaf 1 2>/dev/null))
$(call computable,CC_GREEN,$(shell tput -Txterm setaf 2 2>/dev/null))
$(call computable,CC_YELLOW,$(shell tput -Txterm setaf 3 2>/dev/null))
$(call computable,CC_BLUE,$(shell tput -Txterm setaf 4 2>/dev/null))
$(call computable,CC_MAGENTA,$(shell tput -Txterm setaf 5 2>/dev/null))
$(call computable,CC_CYAN,$(shell tput -Txterm setaf 6 2>/dev/null))
$(call computable,CC_WHITE,$(shell tput -Txterm setaf 7 2>/dev/null))
$(call computable,CC_END,$(shell tput -Txterm sgr0 2>/dev/null))