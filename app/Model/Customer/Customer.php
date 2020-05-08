<?php
declare(strict_types=1);

namespace App\Model\Customer;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateRoot;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRoot;

final class Customer implements AggregateRoot
{
    use HasAggregateRoot;
}
