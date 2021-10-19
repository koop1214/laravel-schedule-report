<?php

/**
 * Date: 13.04.2021
 * Time: 16:23
 */

namespace Koop\ScheduleReport\Storages;

use Carbon\Carbon;

class ScheduleReportStorage
{
    private const START_KEY  = 'schedule_start:';
    private const FINISH_KEY = 'schedule_finish:';
    private const TTL        = 3600 * 24 * 31;

    private \Redis $redisPersistent;

    /**
     * ScheduleReportStorage constructor.
     *
     * @param \Redis $redisPersistent
     */
    public function __construct(\Redis $redisPersistent)
    {
        $this->redisPersistent = $redisPersistent;
    }

    public function onStart(string $command): void
    {
        $date = Carbon::now();
        $key  = $this->getStartKey($date);

        $this->redisPersistent->hSet($key, $command, $date->timestamp);
        $this->redisPersistent->expire($key, self::TTL);
    }

    public function onFinish(string $command): void
    {
        $date = Carbon::now();
        $key  = $this->getFinishKey($date);

        $this->redisPersistent->hSet($key, $command, $date->timestamp);
        $this->redisPersistent->expire($key, self::TTL);
    }

    public function listStarted(Carbon $date): array
    {
        $key = $this->getStartKey($date);

        return $this->redisPersistent->hGetAll($key);
    }

    public function listFinished(Carbon $date): array
    {
        $key = $this->getFinishKey($date);

        return $this->redisPersistent->hGetAll($key);
    }

    private function getStartKey(Carbon $date): string
    {
        return self::START_KEY . $date->format('Ymd');
    }

    private function getFinishKey(Carbon $date): string
    {
        return self::FINISH_KEY . $date->format('Ymd');
    }
}
