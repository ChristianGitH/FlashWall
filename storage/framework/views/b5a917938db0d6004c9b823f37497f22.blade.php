    <div>
        @if($label)
            <div class="pt-0 label label-text font-semibold">
                <span>
                    {{ $label }}

                    @if($attributes->get('required'))
                        <span class="text-error">*</span>
                    @endif
                </span>
            </div>
        @endif

        <div class="join">
            @foreach ($options as $option)
                <input
                    type="radio"
                    name="{{ $modelName() }}"
                    value="{{ data_get($option, $optionValue) }}"
                    aria-label="{{ data_get($option, $optionLabel) }}"
                    @if(data_get($option, 'disabled')) disabled @endif
                    {{ $attributes->whereStartsWith('wire:model') }}
                    {{
                        $attributes->class([
                            "join-item capitalize btn input-bordered input bg-base-200",
                            "border !input-bordered" => data_get($option, 'disabled')
                        ])
                    }}
                    />
            @endforeach
        </div>
        <!-- ERROR -->
        @if(!$omitError && $errors->has($errorFieldName()))
            @foreach($errors->get($errorFieldName()) as $message)
                @foreach(Arr::wrap($message) as $line)
                    <div class="{{ $errorClass }}" x-classes="text-red-500 label-text-alt p-1">{{ $line }}</div>
                    @break($firstErrorOnly)
                @endforeach
                @break($firstErrorOnly)
            @endforeach
        @endif

        @if($hint)
            <div class="{{ $hintClass }}" x-classes="label-text-alt text-gray-400 ps-1 mt-2">{{ $hint }}</div>
        @endif
    </div>