@props([
    'variant' => 'default', // default | icon | image | avatar
])

<div {{ $attributes->class(['sc-item-media', 'sc-item-media-' . $variant]) }}>{{ $slot }}</div>
