# Integration test environment.
# See MODERNIZATION_AND_INTEGRATION_TEST_PLAN.md and docker-compose.yml.

# Start monerod, monero-wallet-rpc, and the xmrblocks explorer; wait until healthy.
# The FIRST run compiles xmrblocks from source (~20 minutes, ~3 GB build space).
integration-up:
	docker compose up -d --wait

# Build the deterministic regtest chain and write tests/_data/integration/manifest.json.
integration-seed:
	php tests/integration/seed-monero-explorer-chain.php

# Destroy the containers AND all chain/wallet state.
integration-down:
	docker compose down -v

integration-logs:
	docker compose logs

# Full cycle: clean slate, start, seed, run integration suites, tear down.
integration:
	$(MAKE) integration-down
	$(MAKE) integration-up
	$(MAKE) integration-seed
	MONERO_INTEGRATION_TESTS=1 composer test-integration
	$(MAKE) integration-down
