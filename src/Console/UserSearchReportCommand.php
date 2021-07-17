<?php

namespace Workable\RequestLogging\Console;

use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Workable\RequestLogging\Models\UserSearchReport;

class UserSearchReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-search:report
    {--date=daily : range time needs to report [today, yesterday, week, month, range]}
    {--start= : range time needs to report [YYYY-MM-DD]}
    {--end= : range time needs to report [YYYY-MM-DD]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load report search daily to database';

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
        $data_search = [];
        foreach ($dates as $date) {
            $file = storage_path('request_logs/search_daily-' . $date . '.log');

            if (!file_exists($file)) {
                $this->warn($file . ' no data ');
                continue;
            }
            $this->info($file . ' importing ');

            $this->processFile($file,$data_search);
        }

        if (empty($data_search))
            return;


        foreach ($data_search as $log) {
            UserSearchReport::insert($log);
        }
    }

    public function processFile($file, &$data_search)
    {
        $handle = fopen($file, "r");
        while (!feof($handle)) {
            $line = fgets($handle);
            if (strlen($line)) {
                $line = trim($line);

                $request_time = explode(']', $line)[0];
                $request_time = str_replace('[', '', $request_time);

                $search_info = explode('EMERGENCY: ', $line)[1];
                $search_info = explode(' ', $search_info);

                $url = array_shift($search_info);
                $ip = array_pop($search_info);

                $data_search[] = [
                    'url' => $url,
                    'agent' => implode(" ", $search_info),
                    'ip' => $ip,
                    'request_time' => $request_time,
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