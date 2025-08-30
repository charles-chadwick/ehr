<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full bg-gray-100 dark:bg-zinc-900"
>
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Laravel</title>

    <!-- Fonts -->
    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >
    <script
        src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1"
        type="module"
    ></script>
    @fluxAppearance
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

</head>


<body class="min-h-screen bg-white dark:bg-zinc-800">
{{-- modals --}}
<flux:modal name="patients.create">
    <livewire:patients.create
        modal="patients.create"
    />
</flux:modal>

{{-- header ---}}
<flux:header
    class="bg-emerald-500 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700"
>
    <flux:sidebar.toggle
        class="lg:hidden"
        icon="bars-2"
    />

    {{-- left hand side --}}
    <flux:navbar class="-mb-px max-lg:hidden">

        <flux:navbar.item
            href="#"
        >{{ __('ehr.dashboard') }}
        </flux:navbar.item>

        <flux:separator
            vertical
            class="my-2"
        />

        <flux:dropdown class="max-lg:hidden">
            <flux:navbar.item icon:trailing="chevron-down">{{ __('patients.patients') }}</flux:navbar.item>
            <flux:navmenu>
                <flux:navmenu.item href="{{ route('patients.index') }}">
                    {{ __('ehr.show_all', ['items' => __('patients.patients')]) }}
                </flux:navmenu.item>
                <flux:separator />
                <flux:navmenu.item href="#">
                    <flux:modal.trigger name="patients.create">{{ __('ehr.create_new', ['item' => __('patients.patient')]) }}</flux:modal.trigger>
                </flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>

        <flux:separator
            vertical
            class="my-2"
        />

        <flux:navbar.item
            href="#"
        >Calendar
        </flux:navbar.item>

        <flux:separator
            vertical
            class="my-2"
        />
    </flux:navbar>

    <flux:spacer />

    {{-- right hand side --}}
    <flux:navbar class="me-4">
        <flux:navbar.item
            class="max-lg:hidden"
            icon="cog-6-tooth"
            href="#"
            label="{{ __('ehr.settings') }}"
        />
        <flux:navbar.item
            class="max-lg:hidden"
            icon="information-circle"
            href="#"
            label="{{ __('ehr.help') }}"
        />
    </flux:navbar>

    <flux:dropdown
        position="top"
        align="start"
    >
        <flux:profile avatar="{{ auth()->user()->avatar  }}" />
        <flux:menu>
            <flux:menu.item>{{ auth()->user()->full_name }}</flux:menu.item>
            <flux:menu.separator />
            <flux:menu.item icon="arrow-right-start-on-rectangle">{{ __('ehr.sign_out') }}</flux:menu.item>
        </flux:menu>
    </flux:dropdown>

</flux:header>

<flux:sidebar
    stashable
    sticky
    class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700"
>
    <flux:sidebar.toggle
        class="lg:hidden"
        icon="x-mark"
    />

    <flux:navlist variant="outline">
        <flux:navlist.item
            icon="home"
            href="#"
            current
        >Home
        </flux:navlist.item>
        <flux:navlist.item
            icon="inbox"
            badge="12"
            href="#"
        >Inbox
        </flux:navlist.item>
        <flux:navlist.item
            icon="document-text"
            href="#"
        >Documents
        </flux:navlist.item>
        <flux:navlist.item
            icon="calendar"
            href="#"
        >Calendar
        </flux:navlist.item>
        <flux:navlist.group
            expandable
            heading="Favorites"
            class="max-lg:hidden"
        >
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>
    <flux:spacer />
    <flux:navlist variant="outline">
        <flux:navlist.item
            icon="cog-6-tooth"
            href="#"
        >Settings
        </flux:navlist.item>
        <flux:navlist.item
            icon="information-circle"
            href="#"
        >Help
        </flux:navlist.item>
    </flux:navlist>
</flux:sidebar>
<flux:main>
    <div class="flex max-md:flex-col items-start">
        <flux:separator class="md:hidden" />
        <div class="flex-1 max-md:pt-6 self-stretch">
            {{ $slot }}
        </div>
    </div>
</flux:main>
</body>
@fluxScripts
@livewireScripts
@persist('toast')
<flux:toast position="top end" />
@endpersist

</html>
