<?php

/*
 * This file is part of the Kilofox Ephemeris package.
 *
 * (c) 2021 Kilofox Studio
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kilofox\Ephemeris;

/**
 * 天文历基础类。
 *
 * @author Tinsh <kilofox2000@gmail.com>
 */
class Ephemeris
{
    /** @var float SYNODIC_MONTH 朔望月平均天数 */
    const SYNODIC_MONTH = 29.530589;

    /** @var array $asts 调整后的节气，用于暂存 */
    private static $asts = [];

    /**
     * 求∆t，单位为秒。
     *
     * @param int $year 公历年
     * @param int $month 公历月
     * @return float
     * @see https://eclipse.gsfc.nasa.gov/SEcat5/deltatpoly.html
     */
    public static function deltaT(int $year, int $month)
    {
        $y = $year + ($month - 0.5) / 12;

        switch (true) {
            case $y < -500:
                $u = ($y - 1820) / 100;
                $dt = -20 + 32 * $u * $u;
                break;
            case $y < 500:
                $u = $y / 100;
                $dt = 10583.6 - 1014.41 * $u + 33.78311 * $u * $u - 5.952053 * pow($u, 3) - 0.1798452 * pow($u, 4) + 0.022174192 * pow($u, 5) + 0.0090316521 * pow($u, 6);
                break;
            case $y < 1600:
                $u = ($y - 1000) / 100;
                $dt = 1574.2 - 556.01 * $u + 71.23472 * $u * $u + 0.319781 * pow($u, 3) - 0.8503463 * pow($u, 4) - 0.005050998 * pow($u, 5) + 0.0083572073 * pow($u, 6);
                break;
            case $y < 1700:
                $t = $y - 1600;
                $dt = 120 - 0.9808 * $t - 0.01532 * $t * $t + pow($t, 3) / 7129;
                break;
            case $y < 1800:
                $t = $y - 1700;
                $dt = 8.83 + 0.1603 * $t - 0.0059285 * $t * $t + 0.00013336 * pow($t, 3) - pow($t, 4) / 1174000;
                break;
            case $y < 1860:
                $t = $y - 1800;
                $dt = 13.72 - 0.332447 * $t + 0.0068612 * $t * $t + 0.0041116 * pow($t, 3) - 0.00037436 * pow($t, 4) + 1.21272E-5 * pow($t, 5) - 1.699E-7 * pow($t, 6) + 8.75E-10 * pow($t, 7);
                break;
            case $y < 1900:
                $t = $y - 1860;
                $dt = 7.62 + 0.5737 * $t - 0.251754 * $t * $t + 0.01680668 * pow($t, 3) - 0.0004473624 * pow($t, 4) + pow($t, 5) / 233174;
                break;
            case $y < 1920:
                $t = $y - 1900;
                $dt = -2.79 + 1.494119 * $t - 0.0598939 * $t * $t + 0.0061966 * pow($t, 3) - 0.000197 * pow($t, 4);
                break;
            case $y < 1941:
                $t = $y - 1920;
                $dt = 21.2 + 0.84493 * $t - 0.0761 * $t * $t + 0.0020936 * pow($t, 3);
                break;
            case $y < 1961:
                $t = $y - 1950;
                $dt = 29.07 + 0.407 * $t - $t * $t / 233 + pow($t, 3) / 2547;
                break;
            case $y < 1986:
                $t = $y - 1975;
                $dt = 45.45 + 1.067 * $t - $t * $t / 260 - pow($t, 3) / 718;
                break;
            case $y < 2005:
                $t = $y - 2000;
                $dt = 63.86 + 0.3345 * $t - 0.060374 * $t * $t + 0.0017275 * pow($t, 3) + 0.000651814 * pow($t, 4) + 2.373599E-5 * pow($t, 5);
                break;
            case $y < 2050:
                $t = $y - 2000;
                $dt = 62.92 + 0.32217 * $t + 0.005589 * $t * $t;
                break;
            case $y < 2150:
                $u = ($y - 1820) / 100;
                $dt = -20 + 32 * $u * $u - 0.5628 * (2150 - $y);
                break;
            default:
                $u = ($y - 1820) / 100;
                $dt = -20 + 32 * $u * $u;
        }

        return $dt;
    }

    /**
     * 地球在绕日运行时因受到其他天体的影响而产生的摄动。
     *
     * @param float $jd 儒略日期
     * @return float 某时刻的摄动偏移量
     */
    public static function perturbation(float $jd)
    {
        $ptsa = [
            485, 203, 199, 182, 156, 136, 77, 74, 70, 58,
            52, 50, 45, 44, 29, 18, 17, 16, 14, 12,
            12, 12, 9, 8
        ];
        $ptsb = [
            324.96, 337.23, 342.08, 27.85, 73.14,
            171.52, 222.54, 296.72, 243.58, 119.81,
            297.17, 21.02, 247.54, 325.15, 60.93,
            155.12, 288.79, 198.04, 199.76, 95.39,
            287.11, 320.81, 227.73, 15.45
        ];
        $ptsc = [
            1934.136, 32964.467, 20.186, 445267.112, 45036.886,
            22518.443, 65928.934, 3034.906, 9037.513, 33718.147,
            150.678, 2281.226, 29929.562, 31555.956, 4443.417,
            67555.328, 4562.452, 62894.029, 31436.921, 14577.848,
            31931.756, 34777.259, 1222.114, 16859.074
        ];
        $t = ($jd - 2451545) / 36525;
        $s = 0;

        for ($k = 0; $k <= 23; $k++) {
            $s = $s + $ptsa[$k] * cos($ptsb[$k] * M_PI / 180 + $ptsc[$k] * M_PI / 180 * $t);
        }

        $w = 35999.373 * $t - 2.47;
        $l = 1 + 0.0334 * cos($w * M_PI / 180) + 0.0007 * cos(2 * $w * M_PI / 180);
        $ptb = 0.00001 * $s / $l;

        return $ptb;
    }

    /**
     * 根据指定的儒略日期，求出公元2000年后的均值新月个数。
     *
     * @param float $jd
     * @return int
     */
    public static function newMoonNumber(float $jd)
    {
        // 从2000年后的第一个均值新月（2000年1月6日14时20分36秒）起至指定时间的阴历月数
        return floor(($jd - 2451550.09765) / self::SYNODIC_MONTH);
    }

    /**
     * 求出实际新月点。
     * 以2000年的第一个均值新月点为0点求出的均值新月点和其朔望月之序数 $k 代入此方程式来求算实际新月点。
     *
     * @param int $k
     * @return float
     */
    public static function trueNewMoon(int $k)
    {
        $jdt = 2451550.09765 + $k * self::SYNODIC_MONTH;

        // 2451545 为2000年1月1日正午12时的JD
        $t = ($jdt - 2451545) / 36525;

        // 频繁使用的平方
        $t2 = $t * $t;

        // 频繁使用的立方
        $t3 = $t2 * $t;

        // $t 的四次方
        $t4 = $t3 * $t;

        // mean time of phase
        $pt = $jdt + 0.0001337 * $t2 - 1.5E-7 * $t3 + 7.3E-10 * $t4;

        // 地球绕太阳运行均值近点角（从太阳观察）
        $m = 2.5534 + 29.10535669 * $k - 2.18E-5 * $t2 - 1.1E-7 * $t3;

        // 月球绕地球运行均值近点角（从地球观察）
        $mprime = 201.5643 + 385.81693528 * $k + 0.0107438 * $t2 + 1.239E-5 * $t3 - 5.8E-8 * $t4;

        // 月球的纬度参数
        $f = 160.7108 + 390.67050274 * $k - 0.0016341 * $t2 - 2.27E-6 * $t3 + 1.1E-8 * $t4;

        // 月球绕日运行轨道升交点经度
        $omega = 124.7746 - 1.5637558 * $k + 0.0020691 * $t2 + 2.15E-6 * $t3;

        // 乘式因子
        $es = 1 - 0.002516 * $t - 0.0000074 * $t2;

        // 因摄动造成的偏移
        $apt1 = -0.4072 * sin((M_PI / 180) * $mprime);
        $apt1 += 0.17241 * $es * sin((M_PI / 180) * $m);
        $apt1 += 0.01608 * sin((M_PI / 180) * 2 * $mprime);
        $apt1 += 0.01039 * sin((M_PI / 180) * 2 * $f);
        $apt1 += 0.00739 * $es * sin((M_PI / 180) * ($mprime - $m));
        $apt1 -= 0.00514 * $es * sin((M_PI / 180) * ($mprime + $m));
        $apt1 += 0.00208 * $es * $es * sin((M_PI / 180) * (2 * $m));
        $apt1 -= 0.00111 * sin((M_PI / 180) * ($mprime - 2 * $f));
        $apt1 -= 0.00057 * sin((M_PI / 180) * ($mprime + 2 * $f));
        $apt1 += 0.00056 * $es * sin((M_PI / 180) * (2 * $mprime + $m));
        $apt1 -= 0.00042 * sin((M_PI / 180) * 3 * $mprime);
        $apt1 += 0.00042 * $es * sin((M_PI / 180) * ($m + 2 * $f));
        $apt1 += 0.00038 * $es * sin((M_PI / 180) * ($m - 2 * $f));
        $apt1 -= 0.00024 * $es * sin((M_PI / 180) * (2 * $mprime - $m));
        $apt1 -= 0.00017 * sin((M_PI / 180) * $omega);
        $apt1 -= 0.00007 * sin((M_PI / 180) * ($mprime + 2 * $m));
        $apt1 += 0.00004 * sin((M_PI / 180) * (2 * $mprime - 2 * $f));
        $apt1 += 0.00004 * sin((M_PI / 180) * (3 * $m));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($mprime + $m - 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * (2 * $mprime + 2 * $f));
        $apt1 -= 0.00003 * sin((M_PI / 180) * ($mprime + $m + 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($mprime - $m + 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * ($mprime - $m - 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * (3 * $mprime + $m));
        $apt1 += 0.00002 * sin((M_PI / 180) * (4 * $mprime));

        $apt2 = 0.000325 * sin((M_PI / 180) * (299.77 + 0.107408 * $k - 0.009173 * $t2));
        $apt2 += 0.000165 * sin((M_PI / 180) * (251.88 + 0.016321 * $k));
        $apt2 += 0.000164 * sin((M_PI / 180) * (251.83 + 26.651886 * $k));
        $apt2 += 0.000126 * sin((M_PI / 180) * (349.42 + 36.412478 * $k));
        $apt2 += 0.00011 * sin((M_PI / 180) * (84.66 + 18.206239 * $k));
        $apt2 += 0.000062 * sin((M_PI / 180) * (141.74 + 53.303771 * $k));
        $apt2 += 0.00006 * sin((M_PI / 180) * (207.14 + 2.453732 * $k));
        $apt2 += 0.000056 * sin((M_PI / 180) * (154.84 + 7.30686 * $k));
        $apt2 += 0.000047 * sin((M_PI / 180) * (34.52 + 27.261239 * $k));
        $apt2 += 0.000042 * sin((M_PI / 180) * (207.19 + 0.121824 * $k));
        $apt2 += 0.00004 * sin((M_PI / 180) * (291.34 + 1.844379 * $k));
        $apt2 += 0.000037 * sin((M_PI / 180) * (161.72 + 24.198154 * $k));
        $apt2 += 0.000035 * sin((M_PI / 180) * (239.56 + 25.513099 * $k));
        $apt2 += 0.000023 * sin((M_PI / 180) * (331.55 + 3.592518 * $k));

        $tnm = $pt + $apt1 + $apt2;

        return $tnm;
    }

    /**
     * 计算指定年（公历）的春分点理论值。
     * 因地球在绕日运行时会因受到其他星球的影响而产生摄动，必须将此现象产生的偏移量加入。
     *
     * @param int $year 公历年
     * @return float 儒略日数
     * @throws InvalidArgumentException
     */
    public static function vernalEquinox(int $year)
    {
        if ($year >= 1000 && $year <= 8001) {
            $m = ($year - 2000) / 1000;
            $jd = 2451623.80984 + 365242.37404 * $m + 0.05169 * $m * $m - 0.00411 * pow($m, 3) - 0.00057 * pow($m, 4);
        } elseif ($year >= -8000 && $year < 1000) {
            $m = $year / 1000;
            $jd = 1721139.29189 + 365242.1374 * $m + 0.06134 * $m * $m + 0.00111 * pow($m, 3) - 0.00071 * pow($m, 4);
        } else {
            throw new \InvalidArgumentException('超出计算能力');
        }

        return $jd;
    }

    /**
     * 获取指定公历年的春分开始的二十四节气理论值。
     * 大致原理：把公转轨道进行24等分，每一等分为一个节气，此为理论值，再用摄动值和 deltaT 做调整得到实际值。
     *
     * @param int $year 公历年
     * @param int $init 从 0 开始
     * @param int $num 1 至 24，若超过则有几秒的误差
     * @return array 下标从 1 开始的数组
     */
    public static function solarTerms(int $year, int $init, int $num)
    {
        // 春分点
        $ve = self::vernalEquinox($year);

        // 回归年长度
        $tropicalYear = self::vernalEquinox($year + 1) - $ve;

        $tx = ($ve - 2451545) / 365250;
        $e = 0.0167086342 - 0.0004203654 * $tx - 1.26734E-5 * $tx * $tx + 1.444E-7 * pow($tx, 3) - 2.0E-10 * pow($tx, 4) + 3.0E-10 * pow($tx, 5);

        // 求春分点与近日点的夹角
        $tt = $year / 1000;
        $vp = 111.25586939 - 17.0119934518333 * $tt - 0.044091890166673 * $tt * $tt - 0.00043735616666135 * pow($tt, 3) + 8.1671666660239E-6 * pow($tt, 4);

        $rvp = $vp * M_PI / 180;
        $ath = M_PI / 12;
        $peri = [];

        for ($i = 1; $i <= $init + $num; $i++) {
            $flag = 0;
            $th = $ath * ($i - 1) + $rvp;

            if ($th > M_PI && $th <= 3 * M_PI) {
                $th = 2 * M_PI - $th;
                $flag = 1;
            }

            if ($th > 3 * M_PI) {
                $th = 4 * M_PI - $th;
                $flag = 2;
            }

            $f1 = 2 * atan((sqrt((1 - $e) / (1 + $e)) * tan($th / 2)));
            $f2 = ($e * sqrt(1 - $e * $e) * sin($th)) / (1 + $e * cos($th));
            $f = ($f1 - $f2) * $tropicalYear / 2 / M_PI;

            if ($flag == 1) {
                $f = $tropicalYear - $f;
            }

            if ($flag == 2) {
                $f = 2 * $tropicalYear - $f;
            }

            $peri[$i] = $f;
        }

        $jdez = [];

        for ($i = max(1, $init); $i <= $init + $num; $i++) {
            $jdez[$i] = $ve + $peri[$i] - $peri[1];
        }

        return $jdez;
    }

    /**
     * 获取指定公历年对摄动作调整后的自春分点开始的二十四节气。
     *
     * @param int $year 公历年
     * @param int $init 0-23
     * @param int $num 1-24 取的个数
     * @return array
     */
    public static function adjustedSolarTerms(int $year, int $init, int $num)
    {
        if (!isset(self::$asts[$year])) {
            // 输入指定年，求该回归年各节气点
            $jdez = self::solarTerms($year, 0, 26);

            for ($i = 1; $i <= 26; $i++) {
                // 受摄动影响所需的微调
                $ptb = self::perturbation($jdez[$i]);

                // 力学时转换为世界时
                $dt = self::deltaT($year, floor($i / 2) + 3);

                // 加上摄动调整值 $ptb ，减去对应的 Delta T 值（分钟转换为日）
                $jdez[$i] = $jdez[$i] + $ptb - $dt / 86400;

                // 中国时间比格林威治时间先行8小时
                $jdez[$i] = $jdez[$i] + 1 / 3;
            }

            self::$asts[$year] = $jdez;
        }

        $jdst = [];

        for ($i = $init + 1; $i <= $init + $num; $i++) {
            $jdst[$i] = self::$asts[$year][$i];
        }

        return $jdst;
    }

    /**
     * 求出某年从冬至开始的连续16个（多取四个以备用）中气JD值。
     *
     * @param int $year 公历年
     * @return array
     */
    public static function midClimates(int $year)
    {
        // 求出上一年以冬至为起点的中气JD值
        $sts = self::adjustedSolarTerms($year - 1, 18, 5);

        // 冬至
        $mts[0] = $sts[19];

        // 大寒
        $mts[1] = $sts[21];

        // 雨水
        $mts[2] = $sts[23];

        // 求出指定年的中气JD值
        $sts = self::adjustedSolarTerms($year, 0, 26);

        for ($i = 1; $i <= 13; $i++) {
            $mts[$i + 2] = $sts[2 * $i - 1];
        }

        return $mts;
    }

    /**
     * 求算以含冬至中气为阴历11月开始的连续16个新月JD值。
     *
     * @param int $year 公历年
     * @return array
     */
    public static function newMoons(int $year)
    {
        // 求出上一年的冬至JD值
        $sts = self::adjustedSolarTerms($year - 1, 18, 1);

        // 冬至
        $jdws = $sts[19];

        // 求年初前两个月附近的新月点（即上一年的11月初）
        $spcjd = Calendar::gd2jd($year - 1, 11, 0, 0);

        // 自2000年1月起的新月个数
        $kn = self::newMoonNumber($spcjd);

        // 求出连续20个新月
        for ($i = 0; $i <= 19; $i++) {
            $k = $kn + $i;
            $mjd = $thejd + self::SYNODIC_MONTH * $i;

            // 以 $k 值代入求瞬时朔望日，中国时间比格林威治时间先行8小时
            $tjd[$i] = self::trueNewMoon($k) + 1 / 3;

            // 力学时转换为世界时，1 为1月，0 为上一年12月，-1 为上一年11月
            $tjd[$i] = $tjd[$i] - self::deltaT($year, $i - 1) / 86400;
        }

        for ($j = 0; $j <= 18; $j++) {
            // 已超过冬至中气（比较日期法）
            if (floor($tjd[$j] + 0.5) > floor($jdws + 0.5)) {
                break;
            }
        }

        $jdnm = [];

        for ($k = 0; $k <= 15; $k++) {
            // 重排键名，使含冬至新月的键名为0
            $jdnm[$k] = $tjd[$j - 1 + $k];
        }

        return $jdnm;
    }

    /**
     * 求出某年从立春开始的不含中气的十二节气JD值。
     *
     * @param int $year 公历年
     * @return array
     */
    public static function preClimatesSinceSpring(int $year)
    {
        // 求出指定年春分前的节气JD值，以上一年的年值代入
        $sts = self::adjustedSolarTerms($year - 1, 21, 3);

        // 立春
        $pts[0] = $sts[22];

        // 惊蛰
        $pts[1] = $sts[24];

        // 求出指定年节气JD值，从惊蛰开始，到雨水
        $sts = self::adjustedSolarTerms($year, 0, 26);

        // 清明至小寒
        for ($i = 1; $i <= 13; $i++) {
            $pts[$i + 1] = $sts[2 * $i];
        }

        return $pts;
    }

}
