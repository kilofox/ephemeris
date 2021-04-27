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
 * 中国农历。
 *
 * @author Tinsh <kilofox2000@gmail.com>
 */
class Calendar
{
    /** @var bool $springBegins 干支是否以立春作为岁首 */
    public $springBegins = true;

    /** @var bool $devideZiHour 干支纪时是否区分夜子时和早子时 */
    public $devideZiHour = false;

    /** @var array $cstbs 六十甲子 */
    protected $cstbs = [
        '甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉',
        '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未',
        '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳',
        '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯',
        '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑',
        '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'
    ];

    /** @var array $months 农历月名 */
    protected $months = [
        '正月', '二月', '三月', '四月', '五月', '六月',
        '七月', '八月', '九月', '十月', '冬月', '腊月'
    ];

    /** @var array $days 农历日名 */
    protected $days = [
        '初一', '初二', '初三', '初四', '初五', '初六', '初七', '初八', '初九', '初十',
        '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '廿十',
        '廿一', '廿二', '廿三', '廿四', '廿五', '廿六', '廿七', '廿八', '廿九', '三十'
    ];

    /** @var array $weeks 星期名 */
    protected $weeks = ['日', '一', '二', '三', '四', '五', '六'];

    /** @var array $numbers 数字 */
    protected $numbers = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];

    /** @var array $css 十天干名 */
    protected static $css = [
        '甲', '乙', '丙', '丁', '戊',
        '己', '庚', '辛', '壬', '癸'
    ];

    /** @var array $tbs 十二地支名 */
    protected static $tbs = [
        '子', '丑', '寅', '卯', '辰', '巳',
        '午', '未', '申', '酉', '戌', '亥'
    ];

    /** @var array $terms 二十四节气名 */
    public $solarTerms = [
        '春分', '清明', '谷雨', '立夏', '小满', '芒种', '夏至', '小暑',
        '大暑', '立秋', '处暑', '白露', '秋分', '寒露', '霜降', '立冬',
        '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '惊蛰'
    ];

    /**
     * 将格里高利历日期转换为儒略日期。
     *
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @param int $hour 时
     * @param int $minute 分
     * @param int $second 秒
     * @return float
     * @throws InvalidArgumentException
     */
    public static function gd2jd(int $year, int $month, int $day, int $hour, int $minute = 0, int $second = 0)
    {
        // 超出计算能力
        if ($year < -7000 || $year > 7000) {
            throw new \InvalidArgumentException('超出计算能力');
        }

        $yp = $year + floor(($month - 3) / 10);

        if ($year > 1582 || $year === 1582 && $month > 10 || $year === 1582 && $month === 10 && $day >= 15) {
            $init = 1721119.5;
            $jdY = floor($yp * 365.25) - floor($yp / 100) + floor($yp / 400);
        } else {
            if ($year < 1582 || $year === 1582 && $month < 10 || $year === 1582 && $month === 10 && $day <= 4) {
                $init = 1721117.5;
                $jdY = floor($yp * 365.25);
            } else {
                throw new \InvalidArgumentException('不存在的时间');
            }
        }

        $mp = floor($month + 9) % 12;
        $jdM = $mp * 30 + floor(($mp + 1) * 34 / 57);
        $jdD = $day - 1;
        $hour += ($second / 60 + $minute) / 60;
        $jdH = $hour / 24;
        $jd = $jdY + $jdM + $jdD + $jdH + $init;

        return $jd;
    }

    /**
     * 将儒略日期转换为格里高利历日期。
     *
     * @param float $jd 儒略日期
     * @return array
     */
    public static function jd2gd(float $jd)
    {
        if ($jd >= 2299160.5) {
            $y4h = 146097;
            $init = 1721119.5;
        } else {
            $y4h = 146100;
            $init = 1721117.5;
        }

        $years = floor($jd - $init);
        $yh = $y4h / 4;
        $cen = floor(($years + 0.75) / $yh);
        $dd = floor($years + 0.75 - $cen * $yh);
        $ywl = 1461 / 4;
        $jy = floor(($dd + 0.75) / $ywl);
        $dd = floor($dd + 0.75 - $ywl * $jy + 1);
        $ml = 153 / 5;
        $mp = floor(($dd - 0.5) / $ml);
        $dd = floor(($dd - 0.5) - 30.6 * $mp + 1);
        $dy = 100 * $cen + $jy;
        $dm = ($mp + 2) % 12 + 1;

        if ($dm < 3) {
            $dy++;
        }

        $seconds = floor(($jd + 0.5 - floor($jd + 0.5)) * 86400 + 0.00005);
        $minutes = floor($seconds / 60);
        $th = floor($minutes / 60);
        $tm = $minutes % 60;
        $ts = $seconds % 60;

        return [$dy, $dm, $dd, $th, $tm, $ts];
    }

    /**
     * 验证公历日期是否有效。
     *
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @return true
     * @throws InvalidArgumentException
     */
    public function validDate(int $year, int $month, int $day)
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('不存在的月份');
        }

        if ($month === 2) {
            if ($year > 1582 && $year % 100 === 0) {
                $leapYear = $year % 400 === 0;
            } else {
                $leapYear = $year % 4 === 0;
            }
            $days = $leapYear ? 29 : 28;
        } else {
            $days = (abs($month - 7.5) + 0.5) % 2 === 1 ? 31 : 30;
        }

        if ($day < 1 || $day > $days) {
            throw new \InvalidArgumentException('不存在的日期');
        }

        if ($year === 1582 && $month === 10 && $day > 4 && $day < 15) {
            throw new \InvalidArgumentException('不存在的日期');
        }

        return true;
    }

    /**
     * 公历某年月的天数。
     *
     * @param int $year 公历年
     * @param int $month 公历月
     * @return int
     * @throws InvalidArgumentException
     */
    public function solarDays(int $year, int $month)
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('不存在的月份');
        }

        // 1582年10月只有21天
        if ($year === 1582 && $month === 10) {
            return 21;
        }

        if ($month === 2) {
            if ($year > 1582 && $year % 100 === 0) {
                $leapYear = $year % 400 === 0;
            } else {
                $leapYear = $year % 4 === 0;
            }
            $days = $leapYear ? 29 : 28;
        } else {
            $days = (abs($month - 7.5) + 0.5) % 2 === 1 ? 31 : 30;
        }

        return $days;
    }

    /**
     * 由于未知原因，个别年份计算出来的闰月与
     *  https://github.com/ytliu0/ChineseCalendar 提供的结果不一致，在此修正。
     *
     * @param int $year 公历年
     * @param array $months 阴历月列表
     * @return array
     */
    private static function fixLeapMonth(int $year, array $months)
    {
        if (in_array($year, [1425])) {
            foreach ($months as $k => $month) {
                if ($month > floor($month)) {
                    $months[$k] = floor($month) + 1;
                    $months[$k + 1] += 0.5;
                    break;
                }
            }
        }

        return $months;
    }

    /**
     * 某年从冬月开始的15个阴历月份（含闰月）代号。
     *
     * @param int $year 公历年
     * @return array
     */
    public static function lunarMonths(int $year)
    {
        // 以上一年冬至为起点的连续16个中气
        $mcs = Ephemeris::midClimates($year);

        // 以含冬至中气为阴历11月开始的连续16个朔望月的新月点
        $nms = Ephemeris::newMoons($year);

        $lmks[0] = 0;

        // 是否已遇到闰月？
        $im = false;

        // 若第13个中气大于等于第14个新月，则表示两个冬至之间有12个朔望月
        if (floor($mcs[12] + 0.5) >= floor($nms[13] + 0.5)) {
            // 至少有一个朔望月不含中气，第一个不含中气的月即为闰月
            for ($i = 1; $i <= 14; $i++) {
                // 若腊月起始日大于冬至中气日，且正月起始日小于等于大寒中气日，则此月为闰月，其余同理
                if (!$im && floor(($nms[$i] + 0.5) > floor($mcs[$i - 1] + 0.5) && floor($nms[$i + 1] + 0.5) <= floor($mcs[$i] + 0.5))) {
                    $lmks[$i] = $i - 0.5;
                    // 遇到闰月
                    $im = true;
                } else {
                    // 遇到闰月以后，每个月代号要减1
                    $lmks[$i] = $im ? $i - 1 : $i;
                }
            }
        }
        // 否则表示两个连续冬至之间只有11个整月，故12个月无闰月
        else {
            // 直接对12个月赋予月代号
            for ($i = 1; $i <= 12; $i++) {
                $lmks[$i] = $i;
            }

            // 处理次一置月年的11月与12月，亦有可能含闰月
            for ($i = 13; $i <= 14; $i++) {
                // 若次一腊月起始日大于附近的冬至中气日，且正月起始日小于等于大寒中气日，则此月为闰月，次一正月同理
                if (!$im && floor(($nms[$i] + 0.5) > floor($mcs[$i - 1] + 0.5) && floor($nms[$i + 1] + 0.5) <= floor($mcs[$i] + 0.5))) {
                    $lmks[$i] = $i - 0.5;
                    // 遇到闰月
                    $im = true;
                } else {
                    // 遇到闰月以后，每个月代号要减1
                    $lmks[$i] = $im ? $i - 1 : $i;
                }
            }
        }

        // 算法外修正
        $lmks = self::fixLeapMonth($year, $lmks);

        return $lmks;
    }

    /**
     * 农历某年月的天数。
     *
     * @param int $year 公历年
     * @param int $month 公历月
     * @param bool $isLeapMonth 是否闰月
     * @return int
     * @throws InvalidArgumentException
     */
    public function lunarDays(int $year, int $month, bool $isLeapMonth = false)
    {
        if ($year < -7000 || $year > 7000) {
            throw new \InvalidArgumentException('超出计算能力');
        }

        if ($year < -1000 || $year > 3000) {
            throw new \InvalidArgumentException('适用于西元前1000年至西元3000年，超出此范围误差较大');
        }

        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('无效的农历月');
        }

        // 求出以含冬至中气为阴历11月开始的连续16个朔望月的新月点
        $sjd = Ephemeris::newMoons($year);
        $lmks = self::lunarMonths($year);

        // 0 代表无闰月
        $leapMonth = 0;

        // 确认指定年上一年11月开始各月是否闰月
        for ($j = 1; $j <= 14; $j++) {
            if ($lmks[$j] > floor($lmks[$j])) {
                // 0 对应阴历11月，1 对应阴历12月，2 对应阴历隔年1月，依此类推。
                $leapMonth = (int) floor($lmks[$j] + 0.5);
                break;
            }
        }

        // 11月对应到 1，12月对应到 2，1月对应到 3 ，2月对应到 4，依此类推。
        $mx = $month + 2;

        // 求算阴历各月之大小，大月30天，小月29天
        $nofd = [];
        for ($i = 0; $i <= 14; $i++) {
            // 每月天数，加 0.5 是因JD以正午起算
            $nofd[$i] = (int) (floor($sjd[$i + 1] + 0.5) - floor($sjd[$i] + 0.5));
        }

        // 若为闰月
        if ($isLeapMonth) {
            // 而旗标非闰月或非本年闰月，则表示此年不含闰月
            if ($leapMonth < 3) {
                // $leapMonth = 0 代表无闰月，1 代表闰月为上一年的11月，2 代表闰月为上一年的12月
                throw new \InvalidArgumentException('此年非闰年');
            }
            // 若本年內有闰月
            else {
                // 但不为输入的月份
                if ($leapMonth !== $mx) {
                    // 则此输入的月份非闰月
                    throw new \InvalidArgumentException('此月非闰月');
                }
                // 若输入的月份即为闰月
                else {
                    // 当月的天数
                    $days = $nofd[$mx];
                }
            }
        } else {
            // 若旗标非闰月，则表示此年不含闰月（包括上一年的11月起之月份）
            if ($leapMonth == 0) {
                // 当月的天数
                $days = $nofd[$mx - 1];
            }
            // 若旗标为本年有闰月（包括上一年的11月起之月份）
            else {
                // 当月的天数
                $days = $mx > $leapMonth ? $nofd[$mx] : $nofd[$mx - 1];
            }
        }

        return $days;
    }

    /**
     * 获取农历某年的闰月，0 为无闰月。
     *
     * @param int $year 公历年
     * @return int
     */
    public function leapMonth(int $year)
    {
        $lmks = self::lunarMonths($year);

        // 0 代表无闰月
        $leapMonth = 0;

        // 确认指定年上一年11月开始各月是否闰月
        for ($j = 1; $j <= 14; $j++) {
            if ($lmks[$j] > floor($lmks[$j])) {
                // 0 对应阴历11月，1 对应阴历12月，2 对应阴历隔年1月，依此类推。
                $leapMonth = (int) floor($lmks[$j] + 0.5);
                break;
            }
        }

        return max(0, $leapMonth - 2);
    }

    /**
     * 求出指定公历年从立春开始的二十四节气的儒略日期。
     *
     * @param int $year 公历年
     * @return array
     */
    public function solarTerms(int $year)
    {
        // 求出指定年春分前的节气JD值，以上一年的年值代入
        $sts = Ephemeris::adjustedSolarTerms($year - 1, 21, 3);

        // 立春
        $pts[0] = $sts[22];

        // 雨水
        $pts[1] = $sts[23];

        // 惊蛰
        $pts[2] = $sts[24];

        // 求出指定年从春分到大寒的节气JD值
        $sts = Ephemeris::adjustedSolarTerms($year, 0, 21);

        for ($i = 1; $i <= 21; $i++) {
            $pts[$i + 2] = $sts[$i];
        }

        return $pts;
    }

    /**
     * 根据公历日期时间计算干支。
     *
     * @param int $year 公历年
     * @param int $month 公历月
     * @param int $day 公历日
     * @param int $hour 时
     * @param int $minute 分
     * @param int $second 秒
     * @return array|false
     */
    public function sexagenaryCycle(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0)
    {
        if (!$this->validDate($year, $month, $day)) {
            return false;
        }

        $jd = self::gd2jd($year, $month, $day, $hour, $minute, $second);

        if ($jd === false) {
            return false;
        }

        // 取得自立春开始的不含中气的12个节气
        $pcs = Ephemeris::preClimatesSinceSpring($year);

        // 以立春作为岁首，若小于 $pcs[0] ，则属于上一个回归年
        if ($this->springBegins && $jd < $pcs[0]) {
            $year--;

            // 取得自立春开始的不含中气的12个节气
            $pcs = Ephemeris::preClimatesSinceSpring($year);
        }

        $stems = [];
        $branches = [];
        $cstb = (($year + 4712 + 24) % 60 + 60) % 60;

        // 年干支
        $stems[0] = $cstb % 10;
        $branches[0] = $cstb % 12;

        // 比较求算节气月，求出月干支
        $tm = 0;
        for ($j = 0; $j <= 13; $j++) {
            if ($pcs[$j] > $jd) {
                // 已超过指定时刻，故应取上一个节气
                $tm = $j - 1;
                break;
            }
        }

        $tmm = (($year + 4712) * 12 + $tm + 60) % 60;
        $cstb = ($tmm + 50) % 60;

        // 月干支
        $stems[1] = $cstb % 10;
        $branches[1] = $cstb % 12;

        // 将起始点从正午改为从0点开始
        $jd += 0.5;

        // 将jd的小数部份化为秒，并加上起始点前移的一小时，取其整数值
        $seconds = (($jd - floor($jd)) * 86400) + 3600;

        // 将秒数化为日数，加回到 $jd 的整数部份
        $dayjd = floor($jd) + $seconds / 86400;
        $cstb = (floor($dayjd + 49) % 60 + 60) % 60;

        // 日干支
        $stems[2] = $cstb % 10;
        $branches[2] = $cstb % 12;

        // 若区分夜子时和早子时，则子正0时分日
        if ($this->devideZiHour && $hour >= 23) {
            $stems[2] = ($stems[2] + 9) % 10;
            $branches[2] = ($branches[2] + 11) % 12;
        }

        // 计算时干支
        $dh = $dayjd * 12;
        $cstb = (floor($dh + 48) % 60 + 60) % 60;
        $stems[3] = $cstb % 10;
        $branches[3] = $cstb % 12;

        return [$stems, $branches];
    }

    /**
     * 根据年干支计算所有合法的月干支。
     *
     * @param int $ygz 年干支代码
     * @return array 月干支代码列表
     */
    public function sexagenaryMonths($ygz)
    {
        $mgz = [];
        $nv = 2 + 12 * ($ygz % 10);

        for ($i = 0; $i <= 11; $i++) {
            $pv = ($i + $nv) % 60;
            $mgz[$pv] = $this->cstbs[$pv];
        }

        return $mgz;
    }

    /**
     * 根据日干支计算所有合法的时干支。
     *
     * @param int $dgz 日干支代码
     * @return array 时干支代码列表
     */
    public function sexagenaryHours($dgz)
    {
        $hgz = [];
        $nv = 12 * ($dgz % 10);

        for ($i = 0; $i <= 11; $i++) {
            $pv = ($i + $nv) % 60;
            $hgz[$pv] = $this->cstbs[$pv];
        }

        return $hgz;
    }

    /**
     * 根据四柱干支查找对应的公历日期。
     *
     * !! 这里没有考虑夜子时和早子时。
     *
     * @param int $ygz 年干支
     * @param int $mgz 月干支
     * @param int $dgz 日干支
     * @param int $hgz 时干支
     * @param int $yeai 起始年
     * @param int $mx 查找多少个甲子
     * @throws InvalidArgumentException
     */
    public function gz2gl(int $ygz, int $mgz, int $dgz, int $hgz, int $yeai, int $mx)
    {
        if ($ygz < 0 || $ygz >= 60) {
            throw new \InvalidArgumentException('干支非六十甲子');
        }

        if ($mgz < 0 || $mgz >= 60) {
            throw new \InvalidArgumentException('干支非六十甲子');
        }

        if ($dgz < 0 || $dgz >= 60) {
            throw new \InvalidArgumentException('干支非六十甲子');
        }

        if ($hgz < 0 || $hgz >= 60) {
            throw new \InvalidArgumentException('干支非六十甲子');
        }

        if (!key_exists($mgz, $this->sexagenaryMonths($ygz))) {
            throw new \InvalidArgumentException('对应的干支不存在');
        }

        if (!key_exists($hgz, $this->sexagenaryHours($dgz))) {
            throw new \InvalidArgumentException('对应的干支不存在');
        }

        $yeaf = $yeai + $mx * 60;

        if ($yeai < -1000 || $yeaf > 3000) {
            throw new \InvalidArgumentException('适用于西元前1000年至西元3000年，超出此范围误差较大');
        }

        // initial-final 返回一个含起止时间的数组
        $ifs = [];

        for ($m = 0; $m <= $mx - 1; $m++) {
            $yea = $yeai + $m * 60;

            // 将年月干支对应到指定年的节气月起始时刻
            // 已知公元0年为庚申年，庚申的六十甲子代码为56，这里求得 $yea 的六十甲子代码 $syc
            $syc = ($yea + 56) % 60;

            // 年干支代码相对 $yea 干支代码偏移了多少
            $asyc = ($ygz + 60 - $syc) % 60;

            // 加上偏移即得一个 $ygz 年
            $iy = $yea + $asyc;

            // 该年的立春开始的节气
            $pts = Ephemeris::preClimatesSinceSpring($iy);

            // 已知干支代码，要求干支名，只需将干支代码除以10，所得的余数即为天干的代码；将干支代码除以12，所得的余数即为地支的代码。这里求得mgz在第几个月
            $mgzo = ($mgz + 60 - 2) % 12;

            // 节气月头JD
            $initialJD = $pts[$mgzo];

            // 节气月尾JD
            $finalJD = $pts[$mgzo + 1];

            // 节气月头的日干支代码，儒略日历时间0日为癸丑日，六十甲子代码为49
            $sdc = (floor($initialJD) + 49) % 60;

            // 生日相对于节气月头的日数
            $asdc = ($dgz + 60 - $sdc) % 60;

            // 生日JD值（未加上时辰）
            $idd = floor($initialJD + $asdc);

            // 时辰代码
            $ihh = $hgz % 12;
            $id = $idd + ($ihh * 2 - 13) / 24;
            $fd = $idd + ($ihh * 2 - 11) / 24;

            // 此四柱在此60年中不存在
            if ($fd >= $initialJD && $id <= $finalJD) {
                // 没有跨节
                if ($id > $initialJD && $fd < $finalJD) {
                    $ids = $id;
                    $fds = $fd;
                }

                // 同一个时辰跨越了节：在节气月头，只包含时辰后段
                if ($id < $initialJD && $fd > $initialJD) {
                    $ids = $initialJD;
                    $fds = $fd;
                }

                // 同一个时辰跨越了节：在节气月尾，只包含时辰前段
                if ($id < $finalJD && $fd > $finalJD) {
                    $ids = $id;
                    $fds = $finalJD;
                }

                // 儒略日历时间转成公历时间
                $ifs[] = [self::jd2gd($ids), self::jd2gd($fds)];
            }
        }

        return $ifs;
    }

    /**
     * 将数字年转换为汉字年。
     *
     * @param int $year 数字年
     * @return string
     */
    public function toChineseYear(int $year)
    {
        $year = (string) $year;
        $chars = [];

        for ($i = 0, $l = strlen($year); $i < $l; $i++) {
            $chars[] = $this->numbers[$year[$i]];
        }

        return implode('', $chars);
    }

    /**
     * 将农历月转换为农历月名称。
     *
     * @param int $month 农历月
     * @return string
     * @throws InvalidArgumentException
     */
    public function toChineseMonth(int $month)
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('无效的农历月');
        }

        return $this->months[$month - 1];
    }

    /**
     * 将农历日转换为农历日名称。
     *
     * @param int $day 农历日
     * @return string
     * @throws InvalidArgumentException
     */
    public function toChineseDay(int $day)
    {
        if ($day < 1 || $day > 30) {
            throw new \InvalidArgumentException('无效的农历日');
        }

        return $this->days[$day - 1];
    }

    /**
     * 根据一柱天干地支码返回该柱的六十甲子。
     *
     * @param int $stem 天干码
     * @param int $branch 地支码
     * @return string
     * @throws InvalidArgumentException
     */
    public function toCstb(int $stem, int $branch)
    {
        if ($stem < 0 || $stem > 59) {
            throw new \InvalidArgumentException('无效的天干');
        }

        if ($branch < 0 || $branch > 59) {
            throw new \InvalidArgumentException('无效的地支');
        }

        // 奇对奇，偶对偶，才能组成一柱
        if ($stem % 2 !== $branch % 2) {
            throw new \InvalidArgumentException('干支非六十甲子');
        }

        return self::$css[$stem] . self::$tbs[$branch];
    }

    /**
     * 将公历时间转换成农历时间。
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @return false/array(年,月,日,是否闰月)
     * @throws InvalidArgumentException
     */
    public function solar2lunar(int $year, int $month, int $day, int $hour = null)
    {
        $flag = 0;

        if ($year < -7000 || $year > 7000) {
            throw new \InvalidArgumentException('超出计算能力');
        }

        if ($year < -1000 || $year > 3000) {
            throw new \InvalidArgumentException('适用于西元前1000年至西元3000年，超出此范围误差较大');
        }

        if (!$this->validDate($year, $month, $day)) {
            return false;
        }

        // 求出以含冬至中气为阴历11月开始的连续16个朔望月的新月点
        $sjd = Ephemeris::newMoons($year);
        $lmks = self::lunarMonths($year);

        // 求出指定年月日之JD值
        $jdx = self::gd2jd($year, $month, $day, 12);

        if (floor($jdx) < floor($sjd[0] + 0.5)) {
            $flag = 1;
            // 求出以含冬至中气为阴历11月开始的连续16个朔望月的新月点
            $sjd = Ephemeris::newMoons($year - 1);
            $lmks = self::lunarMonths($year - 1);
        }

        for ($i = 0; $i <= 14; $i++) {
            // 下面的指令中加 0.5 是为了改为从0时算起，而不是从正午算起
            if (floor($jdx) >= floor($sjd[$i] + 0.5) && floor($jdx) < floor($sjd[$i + 1] + 0.5)) {
                $mi = $i;
                break;
            }
        }

        // 每月初一从1开始，而非从0开始
        $lunarDay = floor($jdx) - floor($sjd[$mi] + 0.5) + 1;

        if ($lmks[$mi] < 2 || $flag == 1) {
            $lunarYear = $year - 1;
        } else {
            $lunarYear = $year;
        }

        // $lmks[$mi] 为 0 对应上一年的阴历11月，为 1 对应上一年的阴历12月，为 2 对应本年的阴历1月，依此类推。
        if (($lmks[$mi] - floor($lmks[$mi])) * 2 + 1 === 1) {
            $isLeapMonth = false;
        } else {
            $isLeapMonth = true;
        }

        // 对应到月份
        $lunarMonth = (floor($lmks[$mi] + 10) % 12) + 1;

        // 干支纪年
        list($stem, $branch) = $this->sexagenaryCycle($year, $month, $day);

        return [
            'lunar_year' => $lunarYear,
            'lunar_month' => $lunarMonth,
            'lunar_day' => (int) $lunarDay,
            'lunar_year_chinese' => $this->toChineseYear($lunarYear),
            'lunar_month_chinese' => $this->toChineseMonth($lunarMonth),
            'lunar_day_chinese' => $this->toChineseDay($lunarDay),
            'cstb_year' => $this->toCstb($stem[0], $branch[0]),
            'cstb_month' => $this->toCstb($stem[1], $branch[1]),
            'cstb_day' => $this->toCstb($stem[2], $branch[2]),
            'is_leap' => $isLeapMonth,
        ];
    }

    /**
     * 将农历时间转换成公历时间。
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param bool $isLeapMonth 是否闰月
     * @return array
     * @throws InvalidArgumentException
     */
    public function lunar2solar(int $year, int $month, int $day, bool $isLeapMonth = false)
    {
        if ($year < -7000 || $year > 7000) {
            throw new \InvalidArgumentException('超出计算能力');
        }

        if ($year < -1000 || $year > 3000) {
            throw new \InvalidArgumentException('适用于西元前1000年至西元3000年，超出此范围误差较大');
        }

        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('无效的农历月');
        }

        if ($day < 1 || $day > 30) {
            throw new \InvalidArgumentException('无效的农历日');
        }

        // 求出以含冬至中气为阴历11月开始的连续16个朔望月的新月点
        $sjd = Ephemeris::newMoons($year);
        $lmks = self::lunarMonths($year);

        // 若闰月旗标为0代表无闰月
        $leapMonth = 0;

        // 确认指定年上一年11月开始各月是否闰月
        for ($j = 1; $j <= 14; $j++) {
            if ($lmks[$j] > floor($lmks[$j])) {
                // 0 对应阴历11月，1 对应阴历12月，2 对应阴历隔年1月，依此类推。
                $leapMonth = floor($lmks[$j] + 0.5);
                break;
            }
        }

        // 11月对应到1，12月对应到2，1月对应到3，2月对应到4，依此类推。
        $mx = $month + 2;

        // 求算阴历各月之大小，大月30天，小月29天
        for ($i = 0; $i <= 14; $i++) {
            // 每月天数，加 0.5 是因JD以正午起算
            $nofd[$i] = floor($sjd[$i + 1] + 0.5) - floor($sjd[$i] + 0.5);
        }

        // 若有勾选闰月
        if ($isLeapMonth === true) {
            // 而旗标非闰月或非本年闰月，则表示此年不含闰月
            if ($leapMonth < 3) {
                // $leapMonth = 0 代表无闰月，1 代表闰月为上一年的11月，2 代表闰月为上一年的12月
                throw new \InvalidArgumentException('此年非闰年');
            }
            // 若本年內有闰月
            else {
                // 但不为输入的月份
                if ($leapMonth != $mx) {
                    throw new \InvalidArgumentException('此月非闰月');
                }
                // 若输入的月份即为闰月
                else {
                    // 若输入的日期不大于当月的天数
                    if ($day <= $nofd[$mx]) {
                        // 则将当月之前的JD值加上日期之前的天数
                        $jdx = $sjd[$mx] + $day - 1;
                    } else {
                        throw new \InvalidArgumentException('日期超出范围');
                    }
                }
            }
        }
        // 若没有勾选闰月
        else {
            // 若旗标非闰月，则表示此年不含闰月(包括上一年的11月起之月份)
            if ($leapMonth === 0) {
                // 若输入的日期不大于当月的天数，则将当月之前的JD值加上日期之前的天数
                if ($day <= $nofd[$mx - 1]) {
                    $jdx = $sjd[$mx - 1] + $day - 1;
                } else {
                    throw new \InvalidArgumentException('日期超出范围');
                }
            }
            // 若旗标为本年有闰月(包括上一年的11月起之月份)
            else {
                // 公式 $nofd[$mx - ($mx > $leapMonth) - 1] 的用意为：若指定月大于闰月，则键名用 $mx ，否则键名用 $mx - 1
                // 若输入的日期不大于当月的天数，则将当月之前的JD值加上日期之前的天数
                if ($day <= $nofd[$mx + ($mx > $leapMonth) - 1]) {
                    $jdx = $sjd[$mx + ($mx > $leapMonth) - 1] + $day - 1;
                } else {
                    throw new \InvalidArgumentException('日期超出范围');
                }
            }
        }

        $date = self::jd2gd($jdx);

        return [
            'solar_year' => (int) $date[0],
            'solar_month' => (int) $date[1],
            'solar_day' => (int) $date[2],
            'solar_hour' => (int) $date[3],
            'solar_minute' => (int) $date[4],
            'solar_second' => (int) $date[5],
        ];
    }

}
