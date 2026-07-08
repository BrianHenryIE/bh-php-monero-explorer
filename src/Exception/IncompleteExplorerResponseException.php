<?php

/**
 * Thrown when an explorer API response cannot be hydrated to its typed model,
 * e.g. a required field is missing or has an unexpected type.
 *
 * The exception MESSAGE contains the endpoint, the target model class, and the
 * key names present in the response. The full raw response body is available
 * via {@see self::getResponseBody()} but is deliberately kept out of the
 * message: `outputs`/`outputsblocks` responses echo the caller's view key, and
 * exception messages routinely end up in logs.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Exception;

use Exception;
use Throwable;

class IncompleteExplorerResponseException extends Exception
{
    public function __construct(
        string $message,
        protected readonly string $responseBody,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * The raw JSON response body which failed to hydrate.
     *
     * NB: may contain the view key that was sent in the request's query string
     * (echoed back by the `outputs` and `outputsblocks` endpoints) — treat as
     * sensitive; do not log wholesale.
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}
