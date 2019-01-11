<?php

namespace App\Traits;

trait ProvinceHelper
{
    /**
     * 根据省份拼音获取汉字
     * @param $location
     * @return mixed
     */
    public function PinYinToProvince($location)
    {
        $arr = [
            'beijing' => '北京',
            'tianjin' => '天津',
            'hebei' => '河北',
            'shanxi' => [
                'taiyuan' => '山西',
                'datong' => '山西',
                'yangquan' => '山西',
                'changzhi' => '山西',
                'jincheng' => '山西',
                'suzhou' => '山西',
                'jinzhong' => '山西',
                'yuncheng' => '山西',
                'xinzhou' => '山西',
                'linfen' => '山西',
                'lvliang' => '山西',
                'gujiao' => '山西',
                'lucheng' => '山西',
                'gaoping' => '山西',
                'jiexiu' => '山西',
                'yongji' => '山西',
                'hejin' => '山西',
                'yuanping' => '山西',
                'houma' => '山西',
                'huozhou' => '山西',
                'xiaoyi' => '山西',
                'fenyang' => '山西',
                'xian' => '陕西',
                'tongchuan' => '陕西',
                'baoji' => '陕西',
                'xianyang' => '陕西',
                'weinan' => '陕西',
                'yanan' => '陕西',
                'hanzhong' => '陕西',
                'yulin' => '陕西',
                'ankang' => '陕西',
                'shangluo' => '陕西',
                'xingping' => '陕西',
                'hancheng' => '陕西',
                'huayin' => '陕西',
            ],
            'neimenggu' => '内蒙古',
            'liaoning' => '辽宁',
            'jilin' => '吉林',
            'heilongjiang' => '黑龙江',
            'shanghai' => '上海',
            'jiangsu' => '江苏',
            'zhejiang' => '浙江',
            'anhui' => '安徽',
            'fujian' => '福建',
            'jiangxi' => '江西',
            'shandong' => '山东',
            'henan' => '河南',
            'hubei' => '湖北',
            'guangdong' => '广东',
            'guangxi' => '广西',
            'hainan' => '海南',
            'chongqing' => '重庆',
            'sichuan' => '四川',
            'guizhou' => '贵州',
            'yunnan' => '云南',
            'xizang' => '西藏',
            'gansu' => '甘肃',
            'qinghai' => '青海',
            'ningxia' => '宁夏',
            'xinjiang' => '新疆',
            'taiwan' => '台湾',
            'xianggang' => '香港',
            'aomen' => '澳门',
        ];

        $locationArr = explode("-", $location);
        $province = $arr[0];
        if ($locationArr[0] === 'shanxi') {
            $province = $arr[0][1];
        }
        return $province;
    }
}