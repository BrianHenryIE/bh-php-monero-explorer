<?php

/**
 *
 * I suspect it's
 * @var array{
 *     output_pubkey:string,
 *     amount:int,
 *     block_no:int,
 *     in_mempool:int,
 *     output_idx:string,
 *     tx_hash:string,
 *     payment_id:string
 * }
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5953-L5959
 *
 * Which is partly common with:
 * @see \BrianHenryIE\MoneroExplorer\Model\OutputsOutput
 *
 * @package brianhenryie/bh-php-monero-explorer
 */
namespace BrianHenryIE\MoneroExplorer\Model;

interface OutputsBlocksOutput
{
}
