<?php

namespace Rest\Transformers;

class AccountTransformer
{
    public static function transform($account): array
    {
        return [
            'id' => $account->id,
            'name' => $account->name,
            'type' => $account->type->description(),
            'status' => $account->status->description(),
            'openedDate' => $account->openedDate?->format(\DateTimeInterface::ATOM),
        ];
    }
}
