<?php

namespace App\View\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserDatetime extends Component
{
    public string $formattedDatetime;

    /** Create a new component instance. */
    public function __construct(
        public $datetime,
        public ?string $fallback = null
    ) {
        $this->formattedDatetime = $this->formatDatetime();
    }

    /** Format the datetime according to user's preference */
    private function formatDatetime(): string
    {
        if (empty($this->datetime)) {
            return $this->fallback ?? '';
        }

        $carbon = $this->datetime instanceof Carbon ? $this->datetime : Carbon::parse($this->datetime);
        $user = auth()->user();

        if (! $user) {
            return $carbon->format('M j, Y g:i A'); // Fallback format
        }

        $dateFormat = $user->getPreferredDateFormat();
        $timeFormat = $user->getPreferredTimeFormat();

        return $carbon->format($dateFormat->datetimeFormatWithTime($timeFormat));
    }

    /** Get the view / contents that represent the component. */
    public function render(): View|Closure|string
    {
        return view('components.user-datetime');
    }
}
