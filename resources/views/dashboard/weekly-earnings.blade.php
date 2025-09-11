<div id="weekly-earnings" class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200">
    <div class="text-sm text-gray-500 mb-3">{{ __('Weekly Earnings') }}</div>

    @if(count($weeklyEarnings) > 0)
        <div class="flex flex-wrap gap-2 sm:gap-3">
            @foreach($weeklyEarnings as $earning)
                <div class="text-xl sm:text-2xl font-bold text-gray-900">
                    {{ $earning->formatted() }}
                </div>
            @endforeach
        </div>

        @if(count($weeklyEarnings) > 1)
            <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex justify-end">
                    <div class="text-xs text-gray-500">
                        {{ __('Total (combined)') }}: <span class="font-medium">{{ number_format($totalAmount / 100, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($totalAmount / 100, 2) }}</div>
    @endif
</div>
