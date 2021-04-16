<?php

/*
 * This file is part of the Kilofox Ephemeris package.
 *
 * (c) 2021 Kilofox Studio
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kilofox\Ephemeris\Tests;

use Kilofox\Ephemeris\Calendar;
use PHPUnit\Framework\TestCase;

/**
 * 日历测试。
 *
 * @author Tinsh <kilofox2000@gmail.com>
 */
class CalendarTest extends TestCase
{
    /**
     * Provides test data for testSolarDays()
     *
     * @return array
     */
    public function solarDaysProvider()
    {
        return [
            [1900, 2, 28],
            [2000, 2, 29],
            [2000, 6, 30],
            [2000, 7, 31],
        ];
    }

    /**
     * Tests Calendar::solarDays()
     *
     * @dataProvider solarDaysProvider
     * @param int $year
     * @param int $month
     * @param int  $expected
     */
    public function testSolarDays($year, $month, $expected)
    {
        $calendar = new Calendar;
        $actual = $calendar->solarDays($year, $month);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides test data for testLunarMonths()
     *
     * @return array
     */
    public function lunarMonthsProvider()
    {
        return [
            [1651, 2],
            [1909, 3],
            [2033, 12],
            [3358, 13],
        ];
    }

    /**
     * Tests Calendar::lunarMonths()
     *
     * @dataProvider lunarMonthsProvider
     * @param int $year
     * @param int  $expected
     */
    public function testLunarMonths($year, $expected)
    {
        $calendar = new Calendar;
        $months = $calendar->lunarMonths($year);

        $this->assertEquals($expected, floor($months[$expected + 1]));
    }

    /**
     * Provides test data for testSexagenaryCycle()
     *
     * @return array
     */
    public function sexagenaryCycleProvider()
    {
        return [
            [1900, 2, 28],
            [2000, 2, 29],
            [2000, 6, 30],
            [2000, 7, 31],
        ];
    }

    /**
     * Tests Calendar::sexagenaryCycle()
     *
     * @dataProvider sexagenaryCycleProvider
     * @param int $year
     * @param int $month
     * @param int  $expected
     */
    public function testSexagenaryCycle($year, $month, $day, $hour, $minute, $second, $expected)
    {
        $calendar = new Calendar;
        $actual = $calendar->solarDays($year, $month);

        $this->assertEquals($expected, $actual);
    }

}
