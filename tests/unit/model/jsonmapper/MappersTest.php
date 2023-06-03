<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use JsonMapper\JsonMapperFactory;

class MappersTest extends \PHPUnit\Framework\TestCase
{
    public function data(): array
    {
        return [
            [ 'block.json', BlockMapper::class ],
//            [ 'detailed_transaction.json', EmissionMapper::class ],
            [ 'emission.json', EmissionMapper::class ],
            [ 'mempool.json', MempoolMapper::class ],
            [ 'outputs.json', OutputsMapper::class ],
            [ 'network_info.json', NetworkInfoMapper::class ],
//            [ 'outputs_blocks.json', OutputsBlocksMapper::class ],
            [ 'raw_block.json', RawBlockMapper::class ],
            [ 'raw_transaction.json', RawTransactionMapper::class ],
            [ 'transaction.json', TransactionMapper::class ],
            [ 'transactions.json', TransactionsMapper::class ],
            [ 'version.json', VersionMapper::class ],
        ];
    }

    /**
     * @dataProvider data
     */
    public function testMappers($filename, $type): void
    {
        try {
            $json = file_get_contents(__DIR__ . '/../../../_data/model/' . $filename);

            $mapper = (new JsonMapperFactory())->bestFit();

            $mapper->push(new \JsonMapper\Middleware\CaseConversion(
                \JsonMapper\Enums\TextNotation::UNDERSCORE(),
                \JsonMapper\Enums\TextNotation::CAMEL_CASE()
            ));

            $decoded_json = json_decode($json);
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
