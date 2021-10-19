<?php

/**
 * Date: 13.04.2021
 * Time: 16:40
 */

namespace Koop\ScheduleReport\Commands;

use Koop\ScheduleReport\Storages\ScheduleReportStorage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleReportCommand extends Command
{
    public const SIGNATURE = 'schedule:report';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SIGNATURE . ' {--date= : дата в формате Y-m-d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выводит отчет о выполненных по расписанию задачах';

    public function handle(ScheduleReportStorage $storage): void
    {
        $date = Carbon::parse($this->option('date'));

        $started  = $storage->listStarted($date);
        $finished = $storage->listFinished($date);

        asort($started);

        $rows = [];

        foreach ($started as $command => $timestamp) {
            $startedAt = Carbon::createFromTimestamp($timestamp);

            if (isset($finished[$command]) && $finished[$command] >= $timestamp) {
                $finishedAt = Carbon::createFromTimestamp($finished[$command]);
                $duration   = $this->secondsToTime($finished[$command] - $timestamp);
            } else {
                $finishedAt = null;
                $duration   = '-';
            }

            $rows[] = [
                $command,
                $startedAt->toIso8601String(),
                $finishedAt ? $finishedAt->toIso8601String() : '-',
                $duration,
            ];
        }

        $this->table(['command', 'started', 'finished', 'duration'], $rows);
    }

    private function secondsToTime(int $seconds): string
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@{$seconds}");

        return $dtF->diff($dtT)->format('%H:%I:%S');
    }
}
