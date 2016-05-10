lint:
	mkdir -p _artifacts/lint && chmod -R 777 _artifacts/lint
	docker run --rm -v "${PWD}:/project" jolicode/phaudit php-cs-fixer fix --format=txt -v --dry-run src || export ERROR=1; \
	docker run --rm -v "${PWD}:/project" jolicode/phaudit phpmetrics --report-html=_artifacts/lint/metrics.html src/ || ERROR=1; \
	docker run --rm -v "${PWD}:/project" jolicode/phaudit phpmd src html cleancode,codesize,controversial,design,unusedcode,tests/phpmd/naming.xml > _artifacts/lint/mess.html || ERROR=1; \
	exit ${ERROR}