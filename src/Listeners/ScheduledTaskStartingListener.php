<?php

/**
 * Date: 13.04.2021
 * Time: 16:08
 */

namespace Koop\ScheduleReport\Listeners;

use Koop\ScheduleReport\Storages\ScheduleReportStorage;
use Illuminate\Console\Events\ScheduledTaskStarting;

class ScheduledTaskStartingListener
{
    private ScheduleReportStorage $storage;

    /**
     * ScheduleTaskStartingListener constructor.
     *
     * @param ScheduleReportStorage $storage
     */
    public function __construct(ScheduleReportStorage $storage)
    {
        $this->storage = $storage;
    }

    public function handle(ScheduledTaskStarting $event): void
    {
        $command = $event->task->command;

        $this->storage->onStart($command);
    }
}
