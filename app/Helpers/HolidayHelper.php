<?php
// app/Helpers/HolidayHelper.php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HolidayHelper
{
    /**
     * Get all fixed holidays (same date every year)
     */
    public static function getFixedHolidays($year = null)
    {
        $year = $year ?? now()->year;

        return [
            [
                'name' => 'New Year\'s Day',
                'date' => Carbon::create($year, 1, 1),
                'is_optional' => false
            ],
            [
                'name' => 'Republic Day',
                'date' => Carbon::create($year, 1, 26),
                'is_optional' => false
            ],
            [
                'name' => 'International Women\'s Day',
                'date' => Carbon::create($year, 3, 8),
                'is_optional' => true
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($year, 8, 15),
                'is_optional' => false
            ],
            [
                'name' => 'Gandhi Jayanti',
                'date' => Carbon::create($year, 10, 2),
                'is_optional' => false
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($year, 12, 25),
                'is_optional' => false
            ],
        ];
    }

    /**
     * Get dynamic holidays (based on calculations)
     */
    public static function getDynamicHolidays($year = null)
    {
        $year = $year ?? now()->year;
        $holidays = [];

        // Easter (First Sunday after first full moon after March 21)
        $easter = Carbon::create($year, 3, 21)->addDays(easter_days($year));
        $holidays[] = [
            'name' => 'Easter Sunday',
            'date' => $easter,
            'is_optional' => true
        ];

        // Good Friday (2 days before Easter)
        $holidays[] = [
            'name' => 'Good Friday',
            'date' => $easter->copy()->subDays(2),
            'is_optional' => true
        ];

        // Diwali (approximate - usually October/November)
        $diwali = self::getDiwaliDate($year);
        if ($diwali) {
            $holidays[] = [
                'name' => 'Diwali',
                'date' => $diwali,
                'is_optional' => false
            ];
        }

        // Eid al-Fitr (approximate)
        $eid = self::getEidDate($year);
        if ($eid) {
            $holidays[] = [
                'name' => 'Eid al-Fitr',
                'date' => $eid,
                'is_optional' => true
            ];
        }

        // Thanksgiving (4th Thursday of November)
        $thanksgiving = Carbon::create($year, 11, 1)->next('Thursday')->addWeeks(3);
        $holidays[] = [
            'name' => 'Thanksgiving Day',
            'date' => $thanksgiving,
            'is_optional' => true
        ];

        return $holidays;
    }

    /**
     * Get Diwali date (approximate calculation)
     */
    private static function getDiwaliDate($year)
    {
        // Diwali usually falls between October 15 and November 15
        // This is a simplified calculation - for production, use proper Hindu calendar API
        $baseDate = Carbon::create($year, 10, 15);
        $dayOffset = ($year * 7) % 30;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Eid al-Fitr date (approximate)
     */
    private static function getEidDate($year)
    {
        // Simplified Eid calculation
        $baseDate = Carbon::create($year, 5, 1);
        $dayOffset = ($year * 3) % 28;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get month-specific holidays
     */
    public static function getMonthSpecificHolidays($year = null)
    {
        $year = $year ?? now()->year;

        return [
            // January
            [
                'name' => 'Makar Sankranti',
                'date' => Carbon::create($year, 1, 14),
                'is_optional' => true
            ],
            // February
            [
                'name' => 'Mahashivratri',
                'date' => self::getMahashivratriDate($year),
                'is_optional' => true
            ],
            // March
            [
                'name' => 'Holi',
                'date' => self::getHoliDate($year),
                'is_optional' => false
            ],
            // April
            [
                'name' => 'Ram Navami',
                'date' => self::getRamNavamiDate($year),
                'is_optional' => true
            ],
            // August
            [
                'name' => 'Raksha Bandhan',
                'date' => self::getRakshaBandhanDate($year),
                'is_optional' => true
            ],
            // September
            [
                'name' => 'Ganesh Chaturthi',
                'date' => self::getGaneshChaturthiDate($year),
                'is_optional' => true
            ],
            // October
            [
                'name' => 'Dussehra',
                'date' => self::getDussehraDate($year),
                'is_optional' => false
            ],
            // December
            [
                'name' => 'New Year\'s Eve',
                'date' => Carbon::create($year, 12, 31),
                'is_optional' => true
            ],
        ];
    }

    /**
     * Get Holi date (March full moon)
     */
    private static function getHoliDate($year)
    {
        // Holi is on full moon of Phalguna (February/March)
        $marchFullMoon = Carbon::create($year, 3, 1);
        while ($marchFullMoon->dayOfWeek !== Carbon::SUNDAY) {
            $marchFullMoon->addDay();
        }
        return $marchFullMoon;
    }

    /**
     * Get Mahashivratri date (February/March)
     */
    private static function getMahashivratriDate($year)
    {
        return Carbon::create($year, 2, 20)->addDays(($year * 3) % 28);
    }

    /**
     * Get Ram Navami date (March/April)
     */
    private static function getRamNavamiDate($year)
    {
        return Carbon::create($year, 3, 25)->addDays(($year * 2) % 25);
    }

    /**
     * Get Raksha Bandhan date (August)
     */
    private static function getRakshaBandhanDate($year)
    {
        return Carbon::create($year, 8, 10)->addDays(($year * 2) % 20);
    }

    /**
     * Get Ganesh Chaturthi date (August/September)
     */
    private static function getGaneshChaturthiDate($year)
    {
        return Carbon::create($year, 8, 25)->addDays(($year * 3) % 25);
    }

    /**
     * Get Dussehra date (October)
     */
    private static function getDussehraDate($year)
    {
        return Carbon::create($year, 10, 5)->addDays(($year * 2) % 25);
    }

    /**
     * Get all holidays for a specific year
     */
    public static function getAllHolidays($year = null, $includeOptional = true)
    {
        $year = $year ?? now()->year;
        $holidays = array_merge(
            self::getFixedHolidays($year),
            self::getDynamicHolidays($year),
            self::getMonthSpecificHolidays($year)
        );

        // Filter optional holidays if needed
        if (!$includeOptional) {
            $holidays = array_filter($holidays, function($holiday) {
                return !$holiday['is_optional'];
            });
        }

        // Remove duplicates (same date different names)
        $uniqueHolidays = [];
        foreach ($holidays as $holiday) {
            $dateKey = $holiday['date']->format('Y-m-d');
            if (!isset($uniqueHolidays[$dateKey])) {
                $uniqueHolidays[$dateKey] = $holiday;
            }
        }

        // Sort by date
        usort($uniqueHolidays, function($a, $b) {
            return $a['date']->timestamp - $b['date']->timestamp;
        });

        return $uniqueHolidays;
    }

    /**
     * Get upcoming holidays (from today onwards)
     */
    public static function getUpcomingHolidays($limit = 10, $includeOptional = true)
    {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        $holidays = array_merge(
            self::getAllHolidays($currentYear, $includeOptional),
            self::getAllHolidays($nextYear, $includeOptional)
        );

        // Filter only upcoming holidays
        $upcoming = array_filter($holidays, function($holiday) {
            return $holiday['date']->isFuture() || $holiday['date']->isToday();
        });

        // Limit results
        $upcoming = array_slice($upcoming, 0, $limit);

        // Format for display
        return array_map(function($holiday) {
            return [
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'days_until' => now()->startOfDay()->diffInDays($holiday['date']),
                'is_optional' => $holiday['is_optional']
            ];
        }, $upcoming);
    }

    /**
     * Check if a specific date is a holiday
     */
    public static function isHoliday($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $holidays = self::getAllHolidays($date->year, true);

        foreach ($holidays as $holiday) {
            if ($holiday['date']->format('Y-m-d') === $date->format('Y-m-d')) {
                return $holiday;
            }
        }

        return false;
    }
}
