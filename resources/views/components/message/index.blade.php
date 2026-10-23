{{--
    Message — satu baris pesan dalam percakapan.
    <x-shadcn::message align="end">
        <x-shadcn::message.avatar initials="AD" />
        <x-shadcn::message.content>
            <x-shadcn::message.header>Admin</x-shadcn::message.header>
            <x-shadcn::bubble>Pesanan sudah dikirim.</x-shadcn::bubble>
            <x-shadcn::message.footer>Terkirim · 09.14</x-shadcn::message.footer>
        </x-shadcn::message.content>
    </x-shadcn::message>
--}}
@props([
    'align' => 'start', // start = orang lain, end = pengguna sendiri
])

<div {{ $attributes->class(['sc-message']) }} data-align="{{ $align }}">
    {{ $slot }}
</div>
