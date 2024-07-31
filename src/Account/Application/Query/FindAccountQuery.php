<?php

namespace App\Account\Application\Query;

final readonly class FindAccountQuery
{
    public function __construct(
        public int $id,
    ) {
    }
}
