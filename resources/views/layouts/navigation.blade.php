@php
    use App\Enums\UserRole;

    $user = auth()->user();
    $navigationItems = [
        ['label' => 'Početna', 'route' => 'home', 'active' => 'home'],
    ];

    if ($user?->role === UserRole::KUPAC) {
        $navigationItems[] = ['label' => 'Moje narudžbine', 'route' => 'user.orders', 'active' => 'user.orders'];
        $navigationItems[] = ['label' => 'Korpa', 'route' => 'korpa.index', 'active' => 'korpa.*'];
    }

    if (in_array($user?->role, [UserRole::ADMIN, UserRole::ZAPOSLENI], true)) {
        $navigationItems[] = ['label' => 'Proizvodi', 'route' => 'proizvod.index', 'active' => 'proizvod.*'];
        $navigationItems[] = ['label' => 'Skladišta', 'route' => 'skladiste.index', 'active' => 'skladiste.*'];
        $navigationItems[] = ['label' => 'Resursi', 'route' => 'resurs.index', 'active' => 'resurs.*'];
        $navigationItems[] = ['label' => 'Narudžbine', 'route' => 'narudzbine.index', 'active' => 'narudzbine.*'];
    }

    if ($user?->role === UserRole::ADMIN) {
        $navigationItems[] = ['label' => 'Finansije', 'route' => 'admin.finansije.create', 'active' => 'admin.finansije.*'];
    }
@endphp

<nav x-data="{ open: false }" class="border-b border-borovnica-accent bg-borovnica-dark text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex min-w-0">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 font-bold italic focus:outline-none focus:ring-2 focus:ring-borovnica-soft focus:ring-offset-2 focus:ring-offset-borovnica-dark">
                    <img src="{{ asset('images/logo.png') }}" class="h-12 w-12" alt="Logo sistema Borovnica">
                    <span class="hidden text-lg sm:inline">Borovnica sistem</span>
                </a>

                <div class="hidden items-stretch gap-5 lg:ms-8 lg:flex">
                    @foreach ($navigationItems as $item)
                        <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                            {{ $item['label'] }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <div class="hidden items-center lg:ms-6 lg:flex">
                @auth
                    <x-dropdown align="right" width="48" content-classes="py-1 bg-borovnica-soft">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 rounded-md border border-borovnica-soft/30 px-3 py-2 text-sm font-medium text-white transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-soft">
                                <span>{{ $user->name }}</span>
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Odjava
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('login') }}" class="transition hover:text-borovnica-soft">Prijava</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-full border border-borovnica-soft px-4 py-2 transition hover:bg-borovnica-accent">Registracija</a>
                        @endif
                    </div>
                @endauth
            </div>

            <div class="flex items-center lg:hidden">
                <button
                    type="button"
                    @click="open = ! open"
                    :aria-expanded="open"
                    aria-controls="mobile-navigation"
                    class="inline-flex items-center justify-center rounded-md p-2 text-borovnica-soft transition hover:bg-borovnica-accent hover:text-white focus:outline-none focus:ring-2 focus:ring-borovnica-soft"
                >
                    <span class="sr-only">Otvori glavni meni</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-navigation" x-show="open" x-cloak class="border-t border-borovnica-accent lg:hidden">
        <div class="space-y-1 py-2">
            @foreach ($navigationItems as $item)
                <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                    {{ $item['label'] }}
                </x-responsive-nav-link>
            @endforeach
        </div>

        <div class="border-t border-borovnica-accent px-4 py-3">
            @auth
                <div class="mb-2">
                    <div class="font-semibold">{{ $user->name }}</div>
                    <div class="text-sm text-borovnica-soft">{{ $user->email }}</div>
                </div>
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Odjava
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">Prijava</x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">Registracija</x-responsive-nav-link>
                @endif
            @endauth
        </div>
    </div>
</nav>
