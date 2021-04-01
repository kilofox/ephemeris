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
 * 八字排盘。
 *
 * @author Tinsh <kilofox2000@gmail.com>
 */
class Destiny
{
    /** @var array $zodiacAnimals 十二生肖 */
    protected $zodiacAnimals = [
        '鼠', '牛', '虎', '兔', '龙', '蛇',
        '马', '羊', '猴', '鸡', '狗', '猪'
    ];

    /** @var array $zodiacSigns 十二星座 */
    protected $zodiacSigns = [
        '摩羯', '宝瓶', '双魚', '白羊', '金牛', '双子',
        '巨蟹', '狮子', '室女', '天秤', '天蝎', '人马'
    ];

    /** @var array $cwx 五行 */
    protected $cwx = ['金', '水', '木', '火', '土'];

    /** @var array $genders 性别 */
    protected $genders = ['男', '女'];

    /** @var array $mz 命造 */
    protected $mz = ['乾', '坤'];

    /** @var array $cyy 阴阳 */
    protected $cyy = ['阳', '阴'];

    /** @var array $lx 命局类型 */
    protected $lx = ['命旺', '印重', '煞重', '财旺', '伤官'];

    /** @var array $wxtg 天干的五行属性，0、1、2、3、4 分別代表金、水、木、火、土 */
    protected $wxtg = [2, 2, 3, 3, 4, 4, 0, 0, 1, 1];

    /** @var array $wxdz 地支的五行属性，0、1、2、3、4 分別代表金、水、木、火、土 */
    protected $wxdz = [1, 4, 2, 2, 4, 3, 3, 4, 0, 0, 4, 1];

    /** @var array $ssq 十神全称 */
    protected $ssq = [
        '正印', '偏印', '比肩', '劫财', '伤官',
        '食神', '正财', '偏财', '正官', '偏官'
    ];

    /** @var array $sss 十神缩写 */
    protected $sss = ['印', '卩', '比', '劫', '伤', '食', '财', '才', '官', '杀'];

    /** @var array $dgs 日干关联其余各干对应十神 */
    protected $dgs = [
        [2, 3, 1, 0, 9, 8, 7, 6, 5, 4],
        [3, 2, 0, 1, 8, 9, 6, 7, 4, 5],
        [5, 4, 2, 3, 1, 0, 9, 8, 7, 6],
        [4, 5, 3, 2, 0, 1, 8, 9, 6, 7],
        [7, 6, 5, 4, 2, 3, 1, 0, 9, 8],
        [6, 7, 4, 5, 3, 2, 0, 1, 8, 9],
        [9, 8, 7, 6, 5, 4, 2, 3, 1, 0],
        [8, 9, 6, 7, 4, 5, 3, 2, 0, 1],
        [1, 0, 9, 8, 7, 6, 5, 4, 2, 3],
        [0, 1, 8, 9, 6, 7, 4, 5, 3, 2]
    ];

    /** @var array $tbs 日干关联各支对应十神 */
    protected $tbs = [
        [0, 1, 8, 9, 6, 7, 4, 5, 3, 2],
        [6, 7, 4, 5, 3, 2, 0, 1, 8, 9],
        [2, 3, 1, 0, 9, 8, 7, 6, 5, 4],
        [3, 2, 0, 1, 8, 9, 6, 7, 4, 5],
        [7, 6, 5, 4, 2, 3, 1, 0, 9, 8],
        [5, 4, 2, 3, 1, 0, 9, 8, 7, 6],
        [4, 5, 3, 2, 0, 1, 8, 9, 6, 7],
        [6, 7, 4, 5, 3, 2, 0, 1, 8, 9],
        [9, 8, 7, 6, 5, 4, 2, 3, 1, 0],
        [8, 9, 6, 7, 4, 5, 3, 2, 0, 1],
        [7, 6, 5, 4, 2, 3, 1, 0, 9, 8],
        [1, 0, 9, 8, 7, 6, 5, 4, 2, 3]
    ];

    /** @var array $zcg 地支藏干表，支藏干 */
    protected $zcg = [
        [9, -1, -1],
        [5, 9, 7],
        [0, 2, 4],
        [1, -1, -1],
        [4, 1, 9],
        [2, 4, 6],
        [3, 5, -1],
        [5, 1, 3],
        [6, 8, 4],
        [7, -1, -1],
        [4, 7, 3],
        [8, 0, -1]
    ];

    /** @var array $czs 十二长生 */
    protected $czs = [
        '长生(强)', '沐浴(凶)', '冠带(吉)', '临官(大吉)', '帝旺(大吉)', '衰(弱)',
        '病(弱)', '死(凶)', '墓(吉)', '绝(凶)', '胎(平)', '养(平)'
    ];

    /** @var array $yearss */
    protected $yearss = ['异', '同'];

    /** @var array $sxss */
    protected $sxss = ['生我', '同我', '我生', '我克', '克我'];

    /** @var array $cfw 方位 */
    protected $cfw = [
        '　中　',
        '　北　', '北北东', '东北东',
        '　东　', '东南东', '南南东',
        '　南　', '南南西', '西南西',
        '　西　', '西北西', '北北西'
    ];

    /** @var array $csj 四季 */
    protected $csj = ['旺四季', '　春　', '　夏　', '　秋　', '　冬　'];

    /** @var array $fwtg 天干的方位属性 */
    protected $fwtg = [4, 4, 7, 7, 0, 0, 10, 10, 1, 1];

    /** @var array $fwdz 地支的方位属性 */
    protected $fwdz = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    /** @var array $sjtg 天干的四季属性 */
    protected $sjtg = [1, 1, 2, 2, 0, 0, 3, 3, 4, 4];

    /** @var array $sjdz 地支的四季属性 */
    protected $sjdz = [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 1];

    /**
     * 根据公历年月日精确计算星座下标。
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour 时间(0-23)
     * @param int $minute 分钟数(0-59)
     * @param int $second 秒数(0-59)
     * @return int|false $this->zodiacSigns[$zodiac]
     */
    public function zodiac(int $year, int $month, int $day, int $hour, int $minute = 0, int $second = 0)
    {
        if ($this->validDate($year, $month, $day) === false) {
            return false;
        }

        // 特殊JD
        $spcjd = Calendar::gd2jd($year, $month, $day, $hour, $minute, $second);
        if ($spcjd === false) {
            return false;
        }

        // 显示星座，根据公历的中气判断
        $zr = Calendar::ZQSinceWinterSolstice($year);

        if ($spcjd < $zr[0]) {
            $zr = Calendar::ZQSinceWinterSolstice($year - 1);
        }

        // 若小于雨水，则归上一年
        // 先找到指定时刻前后的中气月首，即指定时刻所在的节气月首JD值
        for ($i = 0; $i <= 13; $i++) {
            if ($spcjd < $zr[$i]) {
                $zodiac = ($i + 12 - 1) % 12;
                break;
            }
        }

        return $zodiac;
    }

    /**
     * 根据公历年月日计算命盘信息，fate-命运，map-图示。
     *
     * @param int $gender 性别，0-男，1-女
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour 时间(0-23)
     * @param int $minute 分钟数(0-59)，在跨节的时辰上会需要，有的排盘忽略了跨节，不需要考虑跨节则请把时间置为对应时辰的初始值
     * @param int $second 秒数(0-59)，在跨节的时辰上会需要，有的排盘忽略了跨节
     * @return false/array
     * @throws InvalidArgumentException
     */
    public function fatenmap(int $gender, int $year, int $month, int $day, int $hour, int $minute = 0, int $second = 0)
    {
        if (!in_array($gender, [0, 1])) {
            throw new \InvalidArgumentException('无效的性别');
        }

        if ($year < -1000 || $year > 3000) {
            throw new \InvalidArgumentException('适用于西元前1000年至西元3000年，超出此范围误差较大');
        }

        // 特殊JD
        $spcjd = Calendar::gd2jd($year, $month, $day, $hour, $minute, $second);
        if ($spcjd === false) {
            return false;
        }

        // 假设 $hour 传了大于 24 的数字，此处修正
        list($year, $month, $day, $hour, $minute, $second) = Calendar::jd2gd($spcjd);

        // 一个回归年的天数
        $ta = 365.24244475;

        // 要返回的数组
        $rt = [];

        // 五行数量，这里不计算藏干里的
        $nwx = [0, 0, 0, 0, 0];

        // 阴阳数量，这里不计算藏干里的
        $nyy = [0, 0];

        // 日干对地支为“子”者所对应的运程代码
        $szs = [1, 6, 10, 9, 10, 9, 7, 0, 4, 3];

        $ty = $year;

        //取得自立春开始的非中气之二十四节气
        $jr = Calendar::pureTermsSinceSpring($ty);

        // jr[0]为立春，约在2月5日前后，
        if ($spcjd < $jr[0]) {
            // 若小于 jr[0]，则属于前一个节气年
            $ty = $year - 1;

            // 取得自立春开始的非中气之12节气
            $jr = Calendar::pureTermsSinceSpring($ty);
        }

        list($cs, $tb) = $this->cstb($year, $month, $day, $hour, $minute, $second);

        // 计算年月日时辰等四柱干支的阴阳属性和个数及五行属性和个数
        // 阴阳天干
        $yeartg = [];

        // 阴阳地支
        $yeardz = [];

        // 各天干对应的五行
        $ewxtg = [];

        // 各地支对应的五行
        $ewxdz = [];

        // yytg 为八字各柱天干之阴阳属性，yydz 为八字各柱地支之阴阳属性，nyy[0] 为阳之总数，nyy[1] 为阴之总数
        for ($k = 0; $k <= 3; $k++) {
            $yeartg[$k] = $cs[$k] % 2;

            // 求天干的阴阳并计算阴阳总数
            $nyy[$yeartg[$k]] = $nyy[$yeartg[$k]] + 1;

            $yeardz[$k] = $tb[$k] % 2;

            // 求地支的阴阳并计算阴阳总数
            $nyy[$yeardz[$k]] = $nyy[$yeardz[$k]] + 1;

            $ewxtg[$k] = $this->wxtg[$cs[$k]];

            // wxtg为天干之五行属性
            $nwx[$ewxtg[$k]] = $nwx[$ewxtg[$k]] + 1;

            $ewxdz[$k] = $this->wxdz[$tb[$k]];

            // wxdz为地支之五行属性
            $nwx[$ewxdz[$k]] = $nwx[$ewxdz[$k]] + 1;
        }

        // 阴阳数量
        $rt['nyy'] = $nyy;

        // 五行数量
        $rt['nwx'] = $nwx;

        // 各天干对应的阴阳
        $rt['yytg'] = $yeartg;

        // 各地支对应的阴阳
        $rt['yydz'] = $yeardz;

        // 各天干对应的五行
        $rt['ewxtg'] = $ewxtg;

        // 各地支对应的五行
        $rt['ewxdz'] = $ewxdz;

        // 日主与地支藏干决定十神
        // 各地支的藏干
        $bzcg = [];

        // 各地支的藏干对应的五行
        $wxcg = [];

        // 各地支的藏干对应的阴阳
        $yearcg = [];

        // 各地支的藏干对应的文字
        $bctg = [];

        // 0, 1, 2, 3 四个
        for ($i = 0; $i <= 3; $i++) {
            $wxcg[$i] = [];
            $yearcg[$i] = [];
            // 0, 1, 2 三个
            for ($j = 0; $j <= 2; $j++) {
                // 取得藏干表中的藏干代码，zcg 为一个 4 X 3 的数组
                $nzcg = $this->zcg[$tb[$i]][$j];

                // 若存在则取出（若为 -1 ，则代表空白）
                if ($nzcg >= 0) {
                    // 暂存其干支文字
                    $bctg[3 * $i + $j] = $this->ctg[$nzcg];

                    // 暂存其所对应之十神文字
                    $bzcg[3 * $i + $j] = $this->sss[$this->dgs[$nzcg][$cs[2]]];

                    // 其五行属性
                    $wxcg[$i][$j] = $this->wxtg[$nzcg];

                    // 其阴阳属性
                    $yearcg[$i][$j] = $nzcg % 2;
                } else {
                    // 若 nzcg 为 -1 ，则代表空白，设定藏干文字变量为空白
                    $bctg[3 * $i + $j] = '';

                    // 若 nzcg 为- 1 ，则代表空白，设定十神文字变量为空白
                    $bzcg[3 * $i + $j] = '';
                }
            }
        }

        $rt['bctg'] = $bctg;
        $rt['bzcg'] = $bzcg;
        $rt['wxcg'] = $wxcg;
        $rt['yycg'] = $yearcg;

        // 求算起运时刻
        // 先找到指定时刻前后的节气月首
        for ($i = 0; $i <= 14; $i++) {
            if ($jr[$i] > $spcjd) {
                // ord 即为指定时刻所在的节气月首JD值
                $ord = $i - 1;
                break;
            }
        }

        // xf 代表节气月的前段长，单位为日，以指定时刻为分界点
        $xf = $spcjd - $jr[$ord];

        // yf 代表节气月的后段长
        $yf = $jr[$ord + 1] - $spcjd;

        if ($gender === 0 && $yeartg[0] === 0 || $gender === 1 && $yeartg[0] === 1) {
            // zf 为指定日开始到起运日之间的总日数（精确法）
            $zf = $ta * 10 * ($yf / ($yf + $xf));

            // zf 为指定日开始到起运日之间的总日数（粗略法）三天折合一年，一天折合四个月，一个时辰折合十天，一个小时折合五天，反推得到一年按360天算，一个月按30天算
            // $zf = 360 * 10 * ($yf / 30);
            // 阳年男或阴年女，其大运是顺推的
            $forward = 0;
        } else {
            // 阴年男或阳年女，其大运是逆推的
            $zf = $ta * 10 * ($xf / ($yf + $xf));

            // 粗略法
            // $zf = 360 * 10 * ($xf / 30);
            $forward = 1;
        }

        // 起运时刻为指定时刻加上推算出的10年內比例值zf
        $qyt = $spcjd + $zf;

        // 将起运时刻的JD值转换为年月日时分秒
        $jt = Calendar::jd2gd($qyt);

        // 起运年（公历）
        $qyy = $jt[0];

        // 起运年
        $rt['qyy'] = $qyy;

        // 一年按 $ta 天算，一个月按 $ta / 12 天算
        $rt['qyy_desc'] = '出生后' . intval($zf / $ta) . '年' . intval($zf % $ta / ($ta / 12)) . '个月' . intval($zf % $ta % ($ta / 12)) . '天起运';

        // 求算起运年（指节气年，农历）
        // 取得自立春开始的非中气之12节气
        $qjr = Calendar::pureTermsSinceSpring($qyy);

        // qjr[0]为立春，约在2月5日前后
        if ($qyt >= $qjr[0]) {
            $jqyy = $qyy;
        } else {
            // 若小于 jr[0] ，则属于前一个节气年
            $jqyy = $qyy - 1;
        }

        // 求算起运年及其后第五年的年干支及起运岁
        $jtd = (($jqyy + 4712 + 24) % 10 + 10) % 10;
        $jtd = $this->ctg[(($jqyy + 4712 + 24) % 10 + 10) % 10] . ' ' . $this->ctg[(($jqyy + 4712 + 24 + 5) % 10 + 10) % 10];

        // 显示每十年为一阶段之起运时刻，分两个五年以年天干和阳历日期表示
        $rt['qyy_desc2'] = '每逢 ' . $jtd . ' 年' . $jt[1] . '月' . $jt[2] . '日交大运';

        // 起运年减去出生年再加一即为起运之岁数，从怀胎算起，出生即算一岁
        $qage = $jqyy - $ty;

        // 大运
        $rt['dy'] = [];

        // 下面的回圈计算起讫岁，大运干支（及其对应的十神），衰旺吉凶
        // 起始岁数
        $zqage = [];

        // 末端岁数
        $zboz = [];

        // 大运月干代码
        $zfman = [];

        // 大运月支代码
        $zfmbn = [];

        // 大运月干文字
        $zfma = [];

        // 大运月支文字
        $zfmb = [];

        // 大运对应的十二长生
        $nzs = [];

        // 这里是根据天干地支代码计算月柱的六十甲子代码
        $mgz = ((10 + $cs[1] - $tb[1]) % 10) / 2 * 12 + $tb[1];

        // 求各阶段的起讫岁数及该阶段的大运
        for ($k = 0; $k <= 8; $k++) {
            if (!is_array($rt['dy'][$k])) {
                $rt['dy'][$k] = [];
            }

            // 求各阶段的起始岁数
            $rt['dy'][$k]['zqage'] = $zqage[$k] = $qage + 1 + $k * 10;

            // 求各阶段的末端岁数
            $rt['dy'][$k]['zboz'] = $zboz[$k] = $qage + 1 + $k * 10 + 9;

            // 排大运
            // 求大运的数值表示值,以出生月份的次月干支开始顺排或以出生月份的前一个月干支开始逆排
            // 大运月干。加60是为保证在取模之前为正数
            $rt['dy'][$k]['zfman'] = $zfman[$k] = ($mgz + 60 + pow(-1, $forward) * ($k + 1)) % 10;

            // 大运月支。加60是为保证在取模之前为正数
            $rt['dy'][$k]['zfmbn'] = $zfmbn[$k] = ($mgz + 60 + pow(-1, $forward) * ($k + 1)) % 12;

            $rt['dy'][$k]['zfma'] = $zfma[$k] = $this->ctg[$zfman[$k]];
            $rt['dy'][$k]['zfmb'] = $zfmb[$k] = $this->cdz[$zfmbn[$k]];

            // 算衰旺吉凶ncs
            // szs(tg(2))为日干对大运地支为“子”者所对应之运程代码
            // g(2)为生日天干(以整数0~11表示)之代码
            // (-1)^tg(2)表示若日干为阳则取加号，若日干为阴则取减号
            // 第一个大运之地支数值为zfmbn(0)
            // 下式中 szs(tg(2)) + (-1) ^ tg(2) * (zfmbn(0))为决定起始运势，(-1) ^ forward * (-1) ^ tg(2) 为决定顺推或逆推，可合并简化为次一式。
            // 加24是为了使总值在取模之前不为负值
            $rt['dy'][$k]['nzs'] = $nzs[$k] = (24 + $szs[$cs[2]] + pow(-1, $cs[2]) * ($zfmbn[0] + pow(-1, $forward) * $k)) % 12;
            $rt['dy'][$k]['nzsc'] = $this->czs[$nzs[$k]];
        }

        // 求流年的数值表示值及对应的文字
        // 流年天干
        $lyean = [];

        // 流年地支
        $lyebn = [];

        // 流年所对应的干支文字
        $lye = [];

        for ($j = 0; $j <= 89; $j++) {
            // 大运
            $k = intval($j / 10);

            // 流年
            $i = $j % 10;

            // 大运对应的流年
            if (!is_array($rt['dy'][$k]['ly'])) {
                $rt['dy'][$k]['ly'] = [];
            }

            if (!is_array($rt['dy'][$k]['ly'][$i])) {
                $rt['dy'][$k]['ly'][$i] = [];
            }

            // $lyean[j] = ($ygz + $j + $qage) % 10;
            // 年龄（虚岁）
            $rt['dy'][$k]['ly'][$i]['age'] = $j + $qage + 1;

            // 流年（农历）
            $rt['dy'][$k]['ly'][$i]['year'] = $j + $qage + $jqyy;

            // 流年天干
            $rt['dy'][$k]['ly'][$i]['lyean'] = $lyean[$j] = ($cs[0] + $j + $qage) % 10;

            // 流年地支
            $rt['dy'][$k]['ly'][$i]['lyebn'] = $lyebn[$j] = ($tb[0] + $j + $qage) % 12;

            // 取流年所对应的干支文字
            $rt['dy'][$k]['ly'][$i]['lye'] = $lye[$j] = $this->ctg[$lyean[$j]] . $this->cdz[$lyebn[$j]];
        }

        // 显示星座，根据公历的中气判断
        $zr = Calendar::ZQSinceWinterSolstice($year);

        if ($spcjd < $zr[0]) {
            $zr = Calendar::ZQSinceWinterSolstice($year - 1);
        }

        // 若小于雨水，则归上一年
        // 先找到指定时刻前后的中气月首，即指定时刻所在的节气月首JD值。
        for ($i = 0; $i <= 13; $i++) {
            if ($spcjd < $zr[$i]) {
                $zodiac = ($i + 12 - 1) % 12;
                break;
            }
        }

        // 命造乾坤
        $rt['mz'] = $this->mz[$gender];

        // 性别，0-男，1-女
        $rt['gender'] = $this->genders[$gender];

        // 公历生日
        $rt['gl'] = [$year, $month, $day];

        // 农历生日
        $rt['nl'] = $this->solar2lunar($year, $month, $day);

        // 八字天干数组
        $rt['tg'] = $cs;

        // 八字地支数组
        $rt['dz'] = $tb;

        // 四柱字符
        $rt['sz'] = [];

        // 天干字符
        $rt['ctg'] = [];

        // 地支字符
        $rt['cdz'] = [];

        for ($i = 0; $i <= 3; $i++) {
            $rt['sz'][$i] = $this->ctg[$cs[$i]] . $this->cdz[$tb[$i]];
            $rt['ctg'][$i] = $this->ctg[$cs[$i]];
            $rt['cdz'][$i] = $this->cdz[$tb[$i]];
        }

        // 生肖与年地支对应
        $rt['chinese_zodiac'] = $this->zodiacAnimals[$tb[0]];

        // 星座
        $rt['zodiac'] = $this->zodiacSigns[$zodiac];

        // 日干阴阳
        $rt['cyy'] = $this->cyy[$yeartg[2]];

        return $rt;
    }

}
