<div
    x-data="{
        open: @js($getDefaultOpen()),
        multiple: @js($isMultiple()),
        collapsible: @js($isCollapsible()),
        isOpen(value) {
            return this.open.includes(value)
        },
        toggle(value) {
            if (this.isOpen(value)) {
                if (this.multiple || this.collapsible) {
                    this.open = this.open.filter((item) => item !== value)
                }

                return
            }

            this.open = this.multiple ? [...this.open, value] : [value]
        },
    }"
    {{ $getExtraAttributeBag()->class([
        'sc-fi-accordion',
        'sc-fi-accordion-plain' => ! $isContained(),
    ]) }}
>
    {{ $getChildSchema() }}
</div>
