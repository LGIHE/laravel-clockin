# Uganda Public Holidays

This document describes the Uganda public holidays that are automatically seeded into the system.

## Automatic Seeding

The system automatically includes Uganda public holidays for the current year and the next year. The holidays are seeded when you run:

```bash
php artisan db:seed
```

Or specifically:

```bash
php artisan db:seed --class=UgandaHolidaysSeeder
```

## 2025 Uganda Public Holidays

The following 13 public holidays are recognized in Uganda for 2025:

| Date | Day | Holiday | Description |
|------|-----|---------|-------------|
| Jan 01, 2025 | Wednesday | New Year's Day | First day of the year |
| Jan 26, 2025 | Sunday | NRM Liberation Day | Commemorates the National Resistance Movement's ascension to power in 1986 |
| Mar 08, 2025 | Saturday | International Women's Day | Celebrates women's social, economic, cultural, and political achievements |
| Mar 31, 2025 | Monday | Eid al-Fitr | Marks the end of Ramadan, the Islamic holy month of fasting |
| Apr 18, 2025 | Friday | Good Friday | Commemorates the crucifixion of Jesus Christ |
| Apr 21, 2025 | Monday | Easter Monday | Day after Easter Sunday |
| May 01, 2025 | Thursday | Labour Day | International Workers' Day |
| Jun 03, 2025 | Tuesday | Martyrs' Day | Honors the Uganda Martyrs who were executed for their Christian faith |
| Jun 07, 2025 | Saturday | Eid al-Adha | Festival of Sacrifice, commemorates Ibrahim's willingness to sacrifice his son |
| Jun 09, 2025 | Monday | National Heroes' Day | Honors Ugandans who contributed to the nation's development |
| Oct 09, 2025 | Thursday | Independence Day | Celebrates Uganda's independence from British colonial rule in 1962 |
| Dec 25, 2025 | Thursday | Christmas Day | Celebrates the birth of Jesus Christ |
| Dec 26, 2025 | Friday | Boxing Day | Day after Christmas |

## Holiday Types

### Fixed Date Holidays
These holidays occur on the same date every year:
- New Year's Day (January 1)
- NRM Liberation Day (January 26)
- International Women's Day (March 8)
- Labour Day (May 1)
- Martyrs' Day (June 3)
- National Heroes' Day (June 9)
- Independence Day (October 9)
- Christmas Day (December 25)
- Boxing Day (December 26)

### Islamic Holidays (Variable Dates)
These holidays are based on the Islamic lunar calendar and vary each year:
- Eid al-Fitr (end of Ramadan)
- Eid al-Adha (Festival of Sacrifice)

**Note:** The dates for Islamic holidays are approximate and should be confirmed each year based on moon sighting.

### Christian Movable Holidays
These holidays are calculated based on Easter, which varies each year:
- Good Friday (Friday before Easter)
- Easter Monday (Monday after Easter)

## Updating Islamic Holiday Dates

The Islamic holiday dates in the seeder are approximate and need to be updated annually based on the Islamic calendar. To update them:

1. Open `database/seeders/UgandaHolidaysSeeder.php`
2. Find the `getIslamicHolidays()` method
3. Add a new condition for the upcoming year with confirmed dates

Example:
```php
if ($year == 2027) {
    return [
        [
            'name' => 'Eid al-Fitr',
            'date' => '2027-03-10', // Update with confirmed date
            'description' => 'Marks the end of Ramadan...',
        ],
        // ...
    ];
}
```

## Adding Holidays Manually

Administrators can also add holidays manually through the web interface:

1. Navigate to the Holidays page
2. Click "Create Holiday"
3. Enter the holiday name, date, and optional description
4. Click "Create Holiday"

## Technical Details

- Holidays are stored in the `holidays` table
- Each holiday has: `id`, `name`, `description`, `date`, `created_at`, `updated_at`, `deleted_at`
- The `date` column has a unique constraint to prevent duplicate dates
- Soft deletes are enabled (holidays can be restored if accidentally deleted)
- Easter is calculated using the Computus algorithm
- The seeder updates existing holidays instead of creating duplicates

## Running the Seeder

To add or update holidays for the current and next year:

```bash
php artisan db:seed --class=UgandaHolidaysSeeder
```

This command is idempotent - you can run it multiple times safely. It will:
- Update existing holidays with the latest names and descriptions
- Create new holidays that don't exist
- Skip holidays that would cause duplicates
