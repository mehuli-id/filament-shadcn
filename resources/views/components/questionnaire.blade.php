{{--
    Questionnaire — form bertahap: satu pertanyaan per langkah, pilihan tunggal/ganda
    atau isian bebas, wajib atau boleh dilewati. Tombol angka 1–9 memilih jawaban.

    $items = [
        [
            'name' => 'kepuasan',
            'prompt' => 'Seberapa puas Anda dengan layanan kami?',
            'description' => 'Pilih satu.',
            'required' => true,
            'choices' => [
                ['value' => 'puas', 'label' => 'Puas', 'description' => 'Semua berjalan lancar'],
                ['value' => 'cukup', 'label' => 'Cukup'],
            ],
        ],
        [
            'name' => 'fitur',
            'prompt' => 'Fitur apa yang paling sering dipakai?',
            'multiple' => true,
            'choices' => [['value' => 'laporan', 'label' => 'Laporan'], ['value' => 'stok', 'label' => 'Stok']],
            'input' => 'Lainnya…', // tambahkan isian bebas (placeholder)
        ],
    ];

    Kirim ke URL:          <x-shadcn::questionnaire :items="$items" action="/survei" />
    Kirim ke Livewire:     <x-shadcn::questionnaire :items="$items" wire="simpanJawaban" />
                           → public function simpanJawaban(array $data) { $data['answers'], $data['texts'] }
--}}
@props([
    'items' => [],
    'action' => null,
    'method' => 'POST',
    'wire' => null,
    // Label bawaan dari terjemahan ui.questionnaire.*; isi untuk menimpa.
    'submitLabel' => null,
    'nextLabel' => null,
    'previousLabel' => null,
    'skipLabel' => null,
    'requiredMessage' => null,
    'progressLabel' => null,
])

@php
    $submitLabel ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.submit');
    $nextLabel ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.next');
    $previousLabel ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.previous');
    $skipLabel ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.skip');
    $requiredMessage ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.required');
    $progressLabel ??= __('filament-shadcn::filament-shadcn.ui.questionnaire.progress');

    $items = array_values($items);
    $total = count($items);
    $answers = [];
    $texts = [];

    foreach ($items as $item) {
        $answers[$item['name']] = ($item['multiple'] ?? false) ? [] : '';
        $texts[$item['name']] = '';
    }

    $meta = array_map(fn (array $item): array => [
        'name' => $item['name'],
        'required' => (bool) ($item['required'] ?? false),
        'multiple' => (bool) ($item['multiple'] ?? false),
    ], $items);

    [$progressBefore, $progressAfter] = explode(':current', str_replace(':total', (string) $total, $progressLabel)) + [1 => ''];
@endphp

<form
    @if (filled($action)) action="{{ $action }}" method="{{ $method }}" @endif
    x-data="{
        step: 0,
        total: {{ $total }},
        items: @js($meta),
        answers: @js((object) $answers),
        texts: @js((object) $texts),
        error: null,
        done: false,
        wire: @js($wire),
        hasAnswer(index) {
            const item = this.items[index]
            const answer = this.answers[item.name]
            const text = (this.texts[item.name] ?? '').trim()

            return (Array.isArray(answer) ? answer.length > 0 : !! answer) || text !== ''
        },
        focusStep() {
            this.$nextTick(() => this.$root.querySelector(`[data-step='${this.step}'] input`)?.focus())
        },
        validate() {
            if (this.items[this.step].required && ! this.hasAnswer(this.step)) {
                this.error = this.step
                this.focusStep()

                return false
            }

            this.error = null

            return true
        },
        next() {
            if (! this.validate()) return
            if (this.step < this.total - 1) {
                this.step++
                this.focusStep()
            }
        },
        previous() {
            if (this.step === 0) return
            this.step--
            this.error = null
            this.focusStep()
        },
        skip() {
            this.error = null
            if (this.step < this.total - 1) {
                this.step++
                this.focusStep()
            }
        },
        submit(event) {
            if (! this.validate()) {
                event.preventDefault()

                return
            }

            if (this.wire && typeof this.$wire !== 'undefined') {
                event.preventDefault()
                this.$wire.call(this.wire, { answers: this.answers, texts: this.texts })
                this.done = true
            }
        },
        shortcut(event) {
            if (event.target.matches('input[type=text], textarea') || event.metaKey || event.ctrlKey) return
            const number = parseInt(event.key)
            if (! number) return
            const inputs = this.$root.querySelectorAll(`[data-step='${this.step}'] .sc-questionnaire-choice input`)
            if (inputs[number - 1]) {
                event.preventDefault()
                inputs[number - 1].click()
            }
        },
    }"
    x-on:submit="submit($event)"
    x-on:keydown="shortcut($event)"
    {{ $attributes->class(['sc-questionnaire']) }}
>
    @if (filled($action) && strtoupper($method) !== 'GET')
        @csrf
    @endif

    <div class="sc-questionnaire-body" x-show="! done">
        <div class="sc-questionnaire-progress">
            <span class="sc-questionnaire-progress-label">{{ $progressBefore }}<span x-text="step + 1">1</span>{{ $progressAfter }}</span>
            <div class="sc-progress sc-progress-size-sm sc-progress-primary" aria-hidden="true">
                <div class="sc-progress-indicator" x-bind:style="`width: ${((step + 1) / total) * 100}%`"></div>
            </div>
        </div>

        @foreach ($items as $index => $item)
            @php
                $multiple = (bool) ($item['multiple'] ?? false);
                $fieldId = 'sc-q-' . $item['name'];
            @endphp

            <fieldset
                class="sc-questionnaire-item"
                data-step="{{ $index }}"
                x-show="step === {{ $index }}"
                x-bind:inert="step !== {{ $index }}"
                @if ($index > 0) x-cloak @endif
                aria-describedby="{{ $fieldId }}-description {{ $fieldId }}-error"
            >
                <legend class="sc-questionnaire-title">
                    {{ $item['prompt'] }}
                    @if ($item['required'] ?? false)
                        <span class="sc-questionnaire-required" aria-hidden="true">*</span>
                    @endif
                </legend>

                @if (filled($item['description'] ?? null))
                    <p id="{{ $fieldId }}-description" class="sc-questionnaire-description">{{ $item['description'] }}</p>
                @endif

                <div class="sc-questionnaire-choices">
                    @foreach ($item['choices'] ?? [] as $choiceIndex => $choice)
                        <label class="sc-questionnaire-choice">
                            <input
                                type="{{ $multiple ? 'checkbox' : 'radio' }}"
                                name="{{ $item['name'] }}{{ $multiple ? '[]' : '' }}"
                                value="{{ $choice['value'] }}"
                                class="{{ $multiple ? 'fi-checkbox-input' : 'fi-radio-input' }}"
                                x-model="answers[@js($item['name'])]"
                                x-on:change="error = null"
                            />
                            <span class="sc-questionnaire-choice-text">
                                <span class="sc-questionnaire-choice-label">{{ $choice['label'] }}</span>
                                @if (filled($choice['description'] ?? null))
                                    <span class="sc-questionnaire-choice-description">{{ $choice['description'] }}</span>
                                @endif
                            </span>
                            @if ($choiceIndex < 9)
                                <kbd class="sc-kbd" aria-hidden="true">{{ $choiceIndex + 1 }}</kbd>
                            @endif
                        </label>
                    @endforeach
                </div>

                @if (filled($item['input'] ?? null))
                    <div class="fi-input-wrp sc-questionnaire-input">
                        <div class="fi-input-wrp-content-ctn">
                            <input
                                type="text"
                                class="fi-input"
                                name="{{ $item['name'] }}_text"
                                placeholder="{{ is_string($item['input']) ? $item['input'] : '' }}"
                                aria-label="{{ is_string($item['input']) ? $item['input'] : $item['prompt'] }}"
                                x-model="texts[@js($item['name'])]"
                                x-on:input="error = null"
                                x-on:keydown.enter.prevent="next()"
                            />
                        </div>
                    </div>
                @endif

                <p
                    id="{{ $fieldId }}-error"
                    class="sc-questionnaire-error"
                    role="alert"
                    x-show="error === {{ $index }}"
                    x-cloak
                >{{ $requiredMessage }}</p>
            </fieldset>
        @endforeach

        <div class="sc-questionnaire-actions">
            <button type="button" class="fi-btn fi-size-md" x-on:click="previous()" x-bind:disabled="step === 0">{{ $previousLabel }}</button>
            <span class="sc-questionnaire-spacer"></span>
            <button type="button" class="fi-btn fi-size-md sc-btn-ghost" x-show="! items[step].required && step < total - 1" x-on:click="skip()">{{ $skipLabel }}</button>
            <button type="button" class="fi-btn fi-color fi-color-primary fi-size-md" x-show="step < total - 1" x-on:click="next()">{{ $nextLabel }}</button>
            <button type="submit" class="fi-btn fi-color fi-color-primary fi-size-md" x-show="step === total - 1" x-cloak>{{ $submitLabel }}</button>
        </div>
    </div>

    @isset($done)
        <div class="sc-questionnaire-done" x-show="done" x-cloak role="status">{{ $done }}</div>
    @endisset
</form>
