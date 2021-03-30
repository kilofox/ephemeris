# Kilofox Ephemeris

:date: 千狐天文历是一款利用天文算法实现中国农历与公历无表查询的日历产品，可用于西元前1000年至西元3000年期间的日期转换。

# Installing

```shell
$ composer require kilofox/ephemeris
```

# Usage

```php
use Kilofox\Ephemeris\Calendar;

$calendar = new Calendar;

// 阳历转阴历
$result = $calendar->solar2lunar(2021, 3, 19);

// 阴历转阳历
$result = $calendar->lunar2solar(2021, 2, 7);

```

结果：

```php
array (
  'lunar_year' => 2021,
  'lunar_month' => 2,
  'lunar_day' => 7,
  'lunar_year_chinese' => '二零二一',
  'lunar_month_chinese' => '二月',
  'lunar_day_chinese' => '初七',
  'cstb_year' => '辛丑',
  'cstb_month' => '辛卯',
  'cstb_day' => '丙寅',
  'is_leap' => true,
)

array (
  'solar_year' => 2021,
  'solar_month' => 3,
  'solar_day' => 19,
  'solar_hour' => 18,
  'solar_minute' => 21,
  'solar_second' => 7,
)
```
更多 API 见源码。

# Reference

- [基本算法参考](http://www.bieyu.com/)
- [中国历法 - 维基百科](https://zh.wikipedia.org/wiki/Category:%E4%B8%AD%E5%9B%BD%E5%8E%86%E6%B3%95)
- [农历 - 维基百科](https://zh.wikipedia.org/wiki/%E8%BE%B2%E6%9B%86)
- [阴历 - 维基百科](https://zh.wikipedia.org/wiki/%E9%98%B4%E5%8E%86)
- [阳历 - 维基百科](https://zh.wikipedia.org/wiki/%E9%98%B3%E5%8E%86)
- [干支 - 维基百科](https://zh.wikipedia.org/wiki/%E5%B9%B2%E6%94%AF)
- [星座 - 维基百科](https://zh.wikipedia.org/wiki/%E6%98%9F%E5%BA%A7)
- [生肖 - 维基百科](https://zh.wikipedia.org/wiki/%E7%94%9F%E8%82%96)
