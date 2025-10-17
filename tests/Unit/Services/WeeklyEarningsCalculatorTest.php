<?php

namespace Tests\Unit\Services;

use App\Enums\Currency;
use App\Models\TimeEntry;
use App\Services\WeeklyEarningsCalculator;
use App\ValueObjects\Money;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Services\WeeklyEarningsCalculator::class)]
class WeeklyEarningsCalculatorTest extends TestCase
{
    #[Test]
    public function calculate_returns_empty_arrays_for_empty_collection(): void
    {
        $result = WeeklyEarningsCalculator::calculate(new Collection);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('weeklyEarnings', $result);
        $this->assertArrayHasKey('totalAmount', $result);
        $this->assertEmpty($result['weeklyEarnings']);
        $this->assertSame(0, $result['totalAmount']);
    }

    #[Test]
    public function calculate_handles_entries_with_no_earnings(): void
    {
        $entry = $this->createMock(TimeEntry::class);
        $entry->method('calculateEarnings')->willReturn(null);

        $entries = new Collection([$entry]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertEmpty($result['weeklyEarnings']);
        $this->assertSame(0, $result['totalAmount']);
    }

    #[Test]
    public function calculate_processes_single_currency_earnings(): void
    {
        $money = new Money(5000, Currency::USD); // $50.00

        $entry = $this->createMock(TimeEntry::class);
        $entry->method('calculateEarnings')->willReturn($money);

        $entries = new Collection([$entry]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(1, $result['weeklyEarnings']);
        $this->assertInstanceOf(Money::class, array_values($result['weeklyEarnings'])[0]);
        $this->assertSame(5000, array_values($result['weeklyEarnings'])[0]->amount);
        $this->assertSame(Currency::USD, array_values($result['weeklyEarnings'])[0]->currency);
        $this->assertSame(5000, $result['totalAmount']);
    }

    #[Test]
    public function calculate_groups_earnings_by_currency(): void
    {
        $usdEntry1 = $this->createMock(TimeEntry::class);
        $usdEntry1->method('calculateEarnings')->willReturn(new Money(3000, Currency::USD));

        $usdEntry2 = $this->createMock(TimeEntry::class);
        $usdEntry2->method('calculateEarnings')->willReturn(new Money(2000, Currency::USD));

        $eurEntry = $this->createMock(TimeEntry::class);
        $eurEntry->method('calculateEarnings')->willReturn(new Money(4000, Currency::EUR));

        $entries = new Collection([$usdEntry1, $usdEntry2, $eurEntry]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(2, $result['weeklyEarnings']);
        $this->assertSame(9000, $result['totalAmount']); // 3000 + 2000 + 4000

        // Check that USD earnings are grouped (should be 5000 total)
        $usdEarnings = collect($result['weeklyEarnings'])->first(fn ($money) => $money->currency === Currency::USD);
        $this->assertNotNull($usdEarnings);
        $this->assertSame(5000, $usdEarnings->amount);

        // Check EUR earnings
        $eurEarnings = collect($result['weeklyEarnings'])->first(fn ($money) => $money->currency === Currency::EUR);
        $this->assertNotNull($eurEarnings);
        $this->assertSame(4000, $eurEarnings->amount);
    }

    #[Test]
    public function calculate_sorts_earnings_by_amount_descending(): void
    {
        $entry1 = $this->createMock(TimeEntry::class);
        $entry1->method('calculateEarnings')->willReturn(new Money(1000, Currency::USD));

        $entry2 = $this->createMock(TimeEntry::class);
        $entry2->method('calculateEarnings')->willReturn(new Money(5000, Currency::EUR));

        $entry3 = $this->createMock(TimeEntry::class);
        $entry3->method('calculateEarnings')->willReturn(new Money(3000, Currency::GBP));

        $entries = new Collection([$entry1, $entry2, $entry3]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(3, $result['weeklyEarnings']);

        // Should be sorted by amount: EUR (5000), GBP (3000), USD (1000)
        $earnings = array_values($result['weeklyEarnings']);
        $this->assertSame(5000, $earnings[0]->amount);
        $this->assertSame(Currency::EUR, $earnings[0]->currency);

        $this->assertSame(3000, $earnings[1]->amount);
        $this->assertSame(Currency::GBP, $earnings[1]->currency);

        $this->assertSame(1000, $earnings[2]->amount);
        $this->assertSame(Currency::USD, $earnings[2]->currency);
    }

    #[Test]
    public function calculate_limits_to_top_five_currencies(): void
    {
        $currencies = [Currency::USD, Currency::EUR, Currency::GBP, Currency::JPY, Currency::CAD, Currency::AUD];
        $entries = new Collection;

        foreach ($currencies as $index => $currency) {
            $entry = $this->createMock(TimeEntry::class);
            // Create different amounts so we can test sorting
            $entry->method('calculateEarnings')->willReturn(new Money(($index + 1) * 1000, $currency));
            $entries->push($entry);
        }

        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(5, $result['weeklyEarnings']);
        $this->assertSame(21000, $result['totalAmount']); // 1000+2000+3000+4000+5000+6000

        // Check that it includes the top 5 (should exclude the first one with 1000)
        $amounts = collect($result['weeklyEarnings'])->pluck('amount')->toArray();
        $this->assertNotContains(1000, $amounts);
        $this->assertContains(6000, $amounts);
        $this->assertContains(5000, $amounts);
        $this->assertContains(4000, $amounts);
        $this->assertContains(3000, $amounts);
        $this->assertContains(2000, $amounts);
    }

    #[Test]
    public function calculate_handles_mixed_null_and_valid_earnings(): void
    {
        $validEntry = $this->createMock(TimeEntry::class);
        $validEntry->method('calculateEarnings')->willReturn(new Money(2500, Currency::USD));

        $nullEntry1 = $this->createMock(TimeEntry::class);
        $nullEntry1->method('calculateEarnings')->willReturn(null);

        $nullEntry2 = $this->createMock(TimeEntry::class);
        $nullEntry2->method('calculateEarnings')->willReturn(null);

        $entries = new Collection([$validEntry, $nullEntry1, $nullEntry2]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(1, $result['weeklyEarnings']);
        $this->assertSame(2500, array_values($result['weeklyEarnings'])[0]->amount);
        $this->assertSame(2500, $result['totalAmount']);
    }

    #[Test]
    public function calculate_method_is_static(): void
    {
        $reflection = new \ReflectionMethod(WeeklyEarningsCalculator::class, 'calculate');

        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    #[Test]
    public function calculate_returns_correct_array_structure(): void
    {
        $entry = $this->createMock(TimeEntry::class);
        $entry->method('calculateEarnings')->willReturn(new Money(1000, Currency::USD));

        $entries = new Collection([$entry]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('weeklyEarnings', $result);
        $this->assertArrayHasKey('totalAmount', $result);
        $this->assertIsArray($result['weeklyEarnings']);
        $this->assertIsInt($result['totalAmount']);
    }

    #[Test]
    public function calculate_handles_zero_amount_earnings(): void
    {
        $entry = $this->createMock(TimeEntry::class);
        $entry->method('calculateEarnings')->willReturn(new Money(0, Currency::USD));

        $entries = new Collection([$entry]);
        $result = WeeklyEarningsCalculator::calculate($entries);

        $this->assertCount(1, $result['weeklyEarnings']);
        $this->assertSame(0, array_values($result['weeklyEarnings'])[0]->amount);
        $this->assertSame(0, $result['totalAmount']);
    }
}
