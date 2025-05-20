<?php

namespace Rest\Services;

use Rest\Transformers\BondTransformer;

class BondService extends BaseService
{
    public function getBonds(): array
    {
        return iterator_to_array($this->client->bonds()->getAllBonds());
    }

    public function getBondByFigi(string $figi): object
    {
        return $this->client->bonds()->getBondByFigi($figi);
    }

    public function getFormattedBonds(): array
    {
        return array_map([BondTransformer::class, 'transform'], $this->getBonds());
    }

    public function getFormattedBondByFigi(string $figi): array
    {
        return BondTransformer::transform($this->getBondByFigi($figi));
    }
}
