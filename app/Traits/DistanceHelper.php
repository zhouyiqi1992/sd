<?php

namespace App\Traits;

trait DistanceHelper
{
    /**
     * 计算两点地理坐标之间的距离
     * @param  Decimal $longitude1 起点经度
     * @param  Decimal $latitude1 起点纬度
     * @param  Decimal $longitude2 终点经度
     * @param  Decimal $latitude2 终点纬度
     * @param  Int $unit 单位 1:米 2:公里
     * @param  Int $decimal 精度 保留小数位数
     * @return Decimal
     */
    function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 3)
    {
        $longitude1 = doubleval($longitude1);
        $latitude1 = doubleval($latitude1);
        $longitude2 = doubleval($longitude2);
        $latitude2 = doubleval($latitude2);
        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926;

        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;

        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI / 180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if ($unit == 2) {
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);

    }

    /**
     * 腾讯地图转百度地图坐标
     * GCJ02->中国正常GCJ02坐标,腾讯地图用的也是GCJ02坐标
     * BD09->百度地图BD09坐标
     *
     * @param $lat
     * @param $lng
     * @return array
     */
    function Convert_GCJ02_To_BD09($lat, $lng)
    {
        $lat = doubleval($lat);
        $lng = doubleval($lng);
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;
        return [$lng, $lat];
    }

    /**
     * 百度地图转腾讯地图坐标
     * GCJ02->中国正常GCJ02坐标,腾讯地图用的也是GCJ02坐标
     * BD09->百度地图BD09坐标
     *
     * @param $lat
     * @param $lng
     * @return array
     */
    function Convert_BD09_To_GCJ02($lat, $lng)
    {
        $lat = doubleval($lat);
        $lng = doubleval($lng);
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        return [$lng, $lat];
    }
}
