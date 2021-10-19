<?php

/**
 * Date: 13.04.2021
 * Time: 16:08
 */

namespace Koop\ScheduleReport\Listeners;

use Koop\ScheduleReport\Storages\ScheduleReportStorage;
use Illuminate\Console\Events\ScheduledTaskFinished;

class ScheduledTaskFinishedListener
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

    public function handle(ScheduledTaskFinished $event): void
    {
        $command = $event->task->command;

        $this->storage->onFinish($command);
    }
}
