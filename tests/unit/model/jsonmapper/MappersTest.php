<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

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
            'block.json'                => [ 'block.json', \BrianHenryIE\MoneroExplorer\Model\Block::class ],
            'detailed_transaction.json' => [ 'detailed_transaction.json', \BrianHenryIE\MoneroExplorer\Model\DetailedTransaction::class ],
            'emission.json'             => [ 'emission.json', \BrianHenryIE\MoneroExplorer\Model\Emission::class ],
            'mempool.json'              => [ 'mempool.json', \BrianHenryIE\MoneroExplorer\Model\Mempool::class ],
            'outputs.json'              => [ 'outputs.json', \BrianHenryIE\MoneroExplorer\Model\Outputs::class ],
            'network_info.json'         => [ 'network_info.json', \BrianHenryIE\MoneroExplorer\Model\NetworkInfo::class ],
            'outputs_blocks.json'       => [ 'outputs_blocks.json', \BrianHenryIE\MoneroExplorer\Model\OutputsBlocks::class ],
            'raw_block.json'            => [ 'raw_block.json', \BrianHenryIE\MoneroExplorer\Model\RawBlock::class ],
            'raw_transaction.json'      => [ 'raw_transaction.json', \BrianHenryIE\MoneroExplorer\Model\RawTransaction::class ],
            'transaction.json'          => [ 'transaction.json', \BrianHenryIE\MoneroExplorer\Model\Transaction::class ],
            'transactions.json'         => [ 'transactions.json', \BrianHenryIE\MoneroExplorer\Model\Transactions::class ],
            'version.json'              => [ 'version.json', \BrianHenryIE\MoneroExplorer\Model\Version::class ],
        ];
    }

    /**
     * @template T of object
     * @param string $filename The test .json file.
     * @param class-string<T> $type The object type to cast/deserialize the response to.
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
