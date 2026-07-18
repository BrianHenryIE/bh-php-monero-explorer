<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Block;
use BrianHenryIE\MoneroExplorer\Model\DetailedTransaction;
use BrianHenryIE\MoneroExplorer\Model\Emission;
use BrianHenryIE\MoneroExplorer\Model\Mempool;
use BrianHenryIE\MoneroExplorer\Model\NetworkInfo;
use BrianHenryIE\MoneroExplorer\Model\Outputs;
use BrianHenryIE\MoneroExplorer\Model\OutputsBlocks;
use BrianHenryIE\MoneroExplorer\Model\RawBlock;
use BrianHenryIE\MoneroExplorer\Model\RawTransaction;
use BrianHenryIE\MoneroExplorer\Model\Transaction;
use BrianHenryIE\MoneroExplorer\Model\Transactions;
use BrianHenryIE\MoneroExplorer\Model\Version;
use JsonMapper\JsonMapperFactory;
use PHPUnit\Framework\Attributes\DataProvider;

class MappersTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @return array<string, string[]>
     */
    public static function data(): array
    {
        return [
            'block.json'                => [ 'block.json', Block::class ],
            'detailed_transaction.json' => [ 'detailed_transaction.json', DetailedTransaction::class ],
            'emission.json'             => [ 'emission.json', Emission::class ],
            'mempool.json'              => [ 'mempool.json', Mempool::class ],
            'outputs.json'              => [ 'outputs.json', Outputs::class ],
            'network_info.json'         => [ 'network_info.json', NetworkInfo::class ],
            'outputs_blocks.json'       => [ 'outputs_blocks.json', OutputsBlocks::class ],
            'raw_block.json'            => [ 'raw_block.json', RawBlock::class ],
            'raw_transaction.json'      => [ 'raw_transaction.json', RawTransaction::class ],
            'transaction.json'          => [ 'transaction.json', Transaction::class ],
            'transactions.json'         => [ 'transactions.json', Transactions::class ],
            'version.json'              => [ 'version.json', Version::class ],
        ];
    }

    /**
     * @template T of object
     * @param string $filename The test .json file.
     * @param class-string<T> $type The object type to cast/deserialize the response to.
     *
     * @dataProvider data
     */
    #[DataProvider('data')]
    public function testMappers($filename, $type): void
    {
        try {
            $json = file_get_contents(__DIR__ . '/../../../_data/model/' . $filename);

            // The exact mapper configuration production uses.
            $mapper = \BrianHenryIE\MoneroExplorer\ExplorerApi::buildResponseMapper();

            $decoded_json = json_decode($json, false, 512, JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);
            if (is_array($decoded_json)) {
                $result = $mapper->mapToClassArray($decoded_json, $type);
            } else {
                $result = $mapper->mapToClass($decoded_json, $type);
            }
        } catch (\Throwable $throwable) {
            self::fail($filename . ' : ' . get_class($throwable) . ' - ' . $throwable->getMessage());
        }

        self::expectNotToPerformAssertions();
    }
}
