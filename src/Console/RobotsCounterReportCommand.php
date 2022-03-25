<?php

namespace Workable\RequestLogging\Console;

use Workable\RequestLogging\Models\RobotsCounterReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DateTime;

class RobotsCounterReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'robot:report
    {--date=daily : range time needs to report [today, yesterday, week, month, range]}
    {--start= : range time needs to report [YYYY-MM-DD]}
    {--end= : range time needs to report [YYYY-MM-DD]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count robots report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->option('date');
        list($start, $end) = $this->parseDate($date);
        $dates  = $this->getDatesFromRange($start, $end);
        $robots = [];
        foreach ($dates as $date) {
            $file = storage_path('request_logs/robots-' . $date . '.log');

            if (!file_exists($file)) {
                $this->warn($file . ' no data ');
                continue;
            }
            $this->info($file . ' importing ');

            $this->processFile($file, $robots);
        }

        if (empty($robots))
            return;


        foreach ($robots as $log) {
            RobotsCounterReport::insert($log);
        }
    }

    public function processFile($file, &$robots)
    {
        $handle = fopen($file, "r");
        while (!feof($handle)) {
            $line = fgets($handle);
            if (strlen($line)) {
                $line = trim($line);

                $bot_request_time = explode(']', $line)[0];
                $bot_request_time = str_replace('[', '', $bot_request_time);

                $bot_info = explode('EMERGENCY: ', $line)[1];
                preg_match('/\(.*\)/', $bot_info, $bot_agent);
                $bot_info = explode(' ', $bot_info);

                $countParam = count($bot_info);

                $robots[] = [
                    'name'         => $bot_info[0],
                    'agent'        => $bot_agent[0],
                    'url'          => $bot_info[$countParam - 3],
                    'ip'           => $bot_info[$countParam - 2],
                    'time_request' => $bot_request_time,
                    'time_exec'    => $bot_info[$countParam - 1],
                ];
            }
        }
        unlink($file);
    }

    /**
     * @param $date
     *
     * @return array [start, end]
     */
    private function parseDate($date)
    {
        $now = new Carbon();
        switch ($date) {
            case "today":// chi count cho hom nay
                $end   = $now->format('Y-m-d');
                $start = $now->format('Y-m-d');
                break;
            case "yesterday":// count cho hom qua
                $now   = new Carbon();
                $end   = $now->format('Y-m-d');
                $start = $now->sub(new \DateInterval('P1D'))->format('Y-m-d');
                break;
            case "week":// count cho tuan nay
                $now   = new Carbon();
                $end   = $now->format('Y-m-d');
                $start = $now->sub(new \DateInterval('P1W'))->format('Y-m-d');
                break;
            case "month":// count cho thang nay
                $now   = new Carbon();
                $end   = $now->format('Y-m-d');
                $start = $now->sub(new \DateInterval('P1M'))->format('Y-m-d');
                break;
            case "range":// count lai tat ca
                $start = new Carbon($this->option('start'));
                $start = $start->format('Y-m-d');
                $end   = new Carbon($this->option('end'));
                $end   = $end->format('Y-m-d');
                break;
            case "daily":// count cho hom nay va hom qua
                $now   = new Carbon();
                $end   = $now->format('Y-m-d');
                $start = $now->sub(new \DateInterval('P1D'))->format('Y-m-d');
                break;
            default:
                $now   = new Carbon($date);
                $end   = $now->format('Y-m-d');
                $start = $now->format('Y-m-d');
                break;
        }
        return [$start, $end];
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array    = array();
        $interval = new \DateInterval('P1D');
        $realEnd  = new DateTime($end);
        $realEnd->add($interval);
        $period = new \DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }
}
