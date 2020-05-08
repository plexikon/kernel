<?php

use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorOption;

return [

    'message' => [

        'alias' => \Plexikon\Chronicle\Chronicler\Reporting\ChronicleMessageAlias::class,
        'serializer' => \Plexikon\Chronicle\Chronicler\Reporting\ChronicleMessageSerializer::class,
        'payload_serializer' => \Plexikon\Chronicle\Chronicler\Reporting\ChroniclePayloadSerializer::class,

        'decorators' => [
            \Plexikon\Reporter\Message\Decorator\EventIdMessageDecorator::class,
            \Plexikon\Reporter\Message\Decorator\EventTypeMessageDecorator::class,
            \Plexikon\Reporter\Message\Decorator\TimeOfRecordingMessageDecorator::class,
            \Plexikon\Chronicle\Chronicler\Reporting\AggregateIdTypeMessageDecorator::class,
        ],

        'publisher' => \Plexikon\Chronicle\Chronicler\Reporting\DomainEventPublisher::class,
    ],

    'models' => [
        'event_stream' => \Plexikon\Chronicle\Model\EventStream::class,
        'projection' => \Plexikon\Chronicle\Model\Projection::class,
    ],

    'tracker' => [
        'use' => 'transactional',
        'event' => \Plexikon\Chronicle\Chronicler\Tracker\EventTracker::class,
        'transactional' => \Plexikon\Chronicle\Chronicler\Tracker\TransactionEventTracker::class,
    ],

    'chronicle' => [
        'store' => [

            'default' => 'pgsql',

            'pgsql' => [
                'persistence_strategy' => \Plexikon\Chronicle\Chronicler\Strategy\Persistence\PgsqlSingleStream::class,
                'disable_transaction' => false,
                'use_write_lock' => true,
                'use_event_decorator' => true,
                'use_event_transaction' => true,
            ],
        ],

        'subscribers' => [
            \Plexikon\Chronicle\Chronicler\Reporting\MessagePublisherSubscriber::class
        ]
    ],

    'repositories' => [
        \Plexikon\Kernel\Projection\Stream::ACCOUNT => [
                  'aggregate_class_name' => \Plexikon\Kernel\Model\Account\Account::class,
                  'cache' => true,
                  'snapshot_store_id' => \Plexikon\Chronicle\Support\Contracts\Snapshot\SnapshotStore::class
        ]
    ],

    'snapshots' => [
        'store' => [
            'default' => 'pgsql',

            'pgsql' => [
                'table_name' => 'snapshots',
                'mapping_tables' => [],
                'serializer' => \Plexikon\Chronicle\Snapshot\DefaultSnapshotSerializer::class,
                'disabled_transaction' => false,
            ],

            'in_memory' => [
                'disabled_transaction' => false,
            ]
        ]
    ],

    'projectors' => [

        'use' => 'default',

        'projector' => [
            'default' => [
                'filter' => \Plexikon\Chronicle\Support\QueryFilter\FromIncludedPositionFilter::class,
                'options' => 'lazy'
            ]
        ],

        'options' => [

            'lazy' => [
                ProjectorOption::OPTION_PCNTL_DISPATCH => true,
                ProjectorOption::OPTION_LOCK_TIMEOUT_MS => 20000,
                ProjectorOption::OPTION_SLEEP => 10000,
                ProjectorOption::OPTION_UPDATE_LOCK_THRESHOLD => 15000,
                ProjectorOption::OPTION_PERSIST_BLOCK_SIZE => 1000,
            ],

            'intensive' => [
                ProjectorOption::OPTION_PCNTL_DISPATCH => true,
                ProjectorOption::OPTION_LOCK_TIMEOUT_MS => 1000,
                ProjectorOption::OPTION_SLEEP => 1000,
                ProjectorOption::OPTION_UPDATE_LOCK_THRESHOLD => 0,
                ProjectorOption::OPTION_PERSIST_BLOCK_SIZE => 1, //
            ]
        ]
    ],

    'console' => [
        'commands' => [
            \Plexikon\Chronicle\Support\Console\CreateEventStreamCommand::class,
            \Plexikon\Chronicle\Support\Console\ProjectorOperationCommand::class,
            \Plexikon\Chronicle\Support\Console\ReadProjectorCommand::class,
        ],

        'up_migrations' => true
    ],
];
