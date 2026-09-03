<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10 sm:text-left">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Moj profil</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
                <p class="mt-3 text-sm font-semibold">Uloga: {{ $user->role->label() }}</p>
            </div>

            <div class="space-y-6">
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
                <div class="rounded-sm border border-red-900/20 bg-red-50/60 p-5 shadow-xl sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
