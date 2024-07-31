<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

interface QueryBusInterface // todo: I should use it in infrastructure to implement the query bus
{
    public function ask(QueryInterface $query): mixed;
}
