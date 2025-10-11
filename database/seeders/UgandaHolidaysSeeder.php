<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UgandaHolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;
        
        // Uganda Public Holidays for current year and next year
        $years = [$currentYear, $nextYear];
        
        foreach ($years as $year) {
            $holidays = $this->getUgandaHolidays($year);
            
            foreach ($holidays as $holiday) {
                try {
                    // Check if holiday already exists for this date
                    $holidayDate = Carbon::parse($holiday['date'])->startOfDay();
                    $existing = Holiday::whereDate('date', $holidayDate)->first();
                    
                    if ($existing) {
                        // Update existing holiday
                        $existing->update([
                            'name' => $holiday['name'],
                            'description' => $holiday['description'],
                        ]);
                        $this->command->info("Updated: {$holiday['name']} on {$holidayDate->format('Y-m-d')}");
                    } else {
                        // Create new holiday
                        Holiday::create([
                            'id' => Str::uuid()->toString(),
                            'name' => $holiday['name'],
                            'description' => $holiday['description'],
                            'date' => $holidayDate,
                        ]);
                        $this->command->info("Created: {$holiday['name']} on {$holidayDate->format('Y-m-d')}");
                    }
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // If unique constraint violation, try to update the existing record
                    $existing = Holiday::whereDate('date', $holidayDate)->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $holiday['name'],
                            'description' => $holiday['description'],
                        ]);
                        $this->command->info("Found and updated: {$holiday['name']} on {$holidayDate->format('Y-m-d')}");
                    }
                }
            }
        }
        
        $this->command->info('Uganda public holidays seeded successfully!');
    }
    
    /**
     * Get Uganda public holidays for a given year
     *
     * @param int $year
     * @return array
     */
    private function getUgandaHolidays(int $year): array
    {
        return [
            // Fixed date holidays
            [
                'name' => 'New Year\'s Day',
                'date' => Carbon::create($year, 1, 1)->format('Y-m-d'),
                'description' => 'First day of the year',
            ],
            [
                'name' => 'NRM Liberation Day',
                'date' => Carbon::create($year, 1, 26)->format('Y-m-d'),
                'description' => 'Commemorates the National Resistance Movement\'s ascension to power in 1986',
            ],
            [
                'name' => 'International Women\'s Day',
                'date' => Carbon::create($year, 3, 8)->format('Y-m-d'),
                'description' => 'Celebrates women\'s social, economic, cultural, and political achievements',
            ],
            [
                'name' => 'Labour Day',
                'date' => Carbon::create($year, 5, 1)->format('Y-m-d'),
                'description' => 'International Workers\' Day',
            ],
            [
                'name' => 'Martyrs\' Day',
                'date' => Carbon::create($year, 6, 3)->format('Y-m-d'),
                'description' => 'Honors the Uganda Martyrs who were executed for their Christian faith',
            ],
            [
                'name' => 'National Heroes\' Day',
                'date' => Carbon::create($year, 6, 9)->format('Y-m-d'),
                'description' => 'Honors Ugandans who contributed to the nation\'s development',
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($year, 10, 9)->format('Y-m-d'),
                'description' => 'Celebrates Uganda\'s independence from British colonial rule in 1962',
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($year, 12, 25)->format('Y-m-d'),
                'description' => 'Celebrates the birth of Jesus Christ',
            ],
            [
                'name' => 'Boxing Day',
                'date' => Carbon::create($year, 12, 26)->format('Y-m-d'),
                'description' => 'Day after Christmas',
            ],
            
            // Islamic holidays (dates vary by year, these are approximate for 2025-2026)
            // You may need to update these dates based on the Islamic calendar
            ...$this->getIslamicHolidays($year),
            
            // Christian holidays (dates vary by year)
            ...$this->getChristianHolidays($year),
        ];
    }
    
    /**
     * Get Islamic holidays for a given year
     * Note: These dates are approximate and should be updated based on moon sighting
     *
     * @param int $year
     * @return array
     */
    private function getIslamicHolidays(int $year): array
    {
        // Approximate dates for Eid al-Fitr and Eid al-Adha
        // These need to be confirmed each year based on the Islamic calendar
        
        if ($year == 2025) {
            return [
                [
                    'name' => 'Eid al-Fitr',
                    'date' => '2025-03-31',
                    'description' => 'Marks the end of Ramadan, the Islamic holy month of fasting',
                ],
                [
                    'name' => 'Eid al-Adha',
                    'date' => '2025-06-07',
                    'description' => 'Festival of Sacrifice, commemorates Ibrahim\'s willingness to sacrifice his son',
                ],
            ];
        } elseif ($year == 2026) {
            return [
                [
                    'name' => 'Eid al-Fitr',
                    'date' => '2026-03-21',
                    'description' => 'Marks the end of Ramadan, the Islamic holy month of fasting',
                ],
                [
                    'name' => 'Eid al-Adha',
                    'date' => '2026-05-28',
                    'description' => 'Festival of Sacrifice, commemorates Ibrahim\'s willingness to sacrifice his son',
                ],
            ];
        }
        
        return [];
    }
    
    /**
     * Get Christian movable holidays for a given year
     *
     * @param int $year
     * @return array
     */
    private function getChristianHolidays(int $year): array
    {
        // Calculate Easter Sunday using Computus algorithm
        $easterDate = $this->calculateEaster($year);
        
        // Good Friday is 2 days before Easter
        $goodFriday = $easterDate->copy()->subDays(2);
        
        // Easter Monday is 1 day after Easter
        $easterMonday = $easterDate->copy()->addDay();
        
        return [
            [
                'name' => 'Good Friday',
                'date' => $goodFriday->format('Y-m-d'),
                'description' => 'Commemorates the crucifixion of Jesus Christ',
            ],
            [
                'name' => 'Easter Monday',
                'date' => $easterMonday->format('Y-m-d'),
                'description' => 'Day after Easter Sunday',
            ],
        ];
    }
    
    /**
     * Calculate Easter Sunday for a given year using Computus algorithm
     *
     * @param int $year
     * @return Carbon
     */
    private function calculateEaster(int $year): Carbon
    {
        $a = $year % 19;
        $b = intval($year / 100);
        $c = $year % 100;
        $d = intval($b / 4);
        $e = $b % 4;
        $f = intval(($b + 8) / 25);
        $g = intval(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intval($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intval(($a + 11 * $h + 22 * $l) / 451);
        $month = intval(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        
        return Carbon::create($year, $month, $day);
    }
}
