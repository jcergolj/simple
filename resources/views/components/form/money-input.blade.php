<div class="grid grid-cols-2 gap-3 w-full">
    <div>
        <input
            type="number"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="input input-bordered input-lg w-full text-lg focus:input-primary @error($name) input-error @enderror"
            step="0.01"
            min="0"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => '']) }}
        />
    </div>
    <select
        name="{{ $currencyName }}"
        class="select select-bordered select-lg w-full text-lg focus:select-primary @error($currencyName) select-error @enderror"
        {!! $currencyAttributes !!}
    >
        @foreach($currencyOptions() as $code => $display)
            <option value="{{ $code }}" {{ $defaultCurrency === $code ? 'selected' : '' }}>
                {{ $display }}
            </option>
        @endforeach
    </select>
</div>
