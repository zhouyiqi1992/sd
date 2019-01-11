<?php
namespace App\Traits;

use Carbon\Carbon;

trait TimeHelper
{
    /**
     * 获取指定日期当月的开始时间与结束时间，格式：Y-m-d
     * @param $date
     * @return array
     */
    public function getStartAndEndTime($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        $start = Carbon::createFromDate($year, $month)->startOfMonth()->toDateString();
        $end = Carbon::createFromDate($year, $month)->endOfMonth()->toDateString();
        $allDaysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        return [$start, $end, $allDaysInMonth];
    }
}