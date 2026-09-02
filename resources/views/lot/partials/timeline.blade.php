<section class="mt-10" aria-labelledby="sledljivost-naslov">
    <h2 id="sledljivost-naslov" class="mb-5 text-2xl font-bold italic uppercase tracking-wide text-borovnica-dark">Sledljivost lota</h2>

    <ol class="relative ms-3 border-s-2 border-borovnica-dark/30">
        @foreach ($dogadjaji as $dogadjaj)
            <li class="relative mb-6 ms-6 last:mb-0">
                <span class="absolute -start-[31px] top-1.5 h-3.5 w-3.5 rounded-full border-2 border-borovnica-light bg-borovnica-dark"></span>
                <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-4 shadow-lg sm:p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <h3 class="font-bold italic uppercase">{{ $dogadjaj->tip->label() }}</h3>
                        <time datetime="{{ $dogadjaj->vreme_dogadjaja->toIso8601String() }}" class="whitespace-nowrap text-sm font-semibold text-borovnica-dark/70">{{ $dogadjaj->vreme_dogadjaja->format('d.m.Y. H:i') }}</time>
                    </div>

                    <dl class="mt-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                        @if ($dogadjaj->prethodni_status || $dogadjaj->novi_status)
                            <div><dt class="font-bold">Promena statusa</dt><dd>{{ $dogadjaj->prethodni_status?->label() ?? '—' }} → {{ $dogadjaj->novi_status?->label() ?? '—' }}</dd></div>
                        @endif
                        @if ($dogadjaj->prethodnaSkladisnaLokacija || $dogadjaj->novaSkladisnaLokacija)
                            <div><dt class="font-bold">Promena lokacije</dt><dd>{{ $dogadjaj->prethodnaSkladisnaLokacija?->naziv ?? '—' }} → {{ $dogadjaj->novaSkladisnaLokacija?->naziv ?? '—' }}</dd></div>
                        @endif
                        @if ($dogadjaj->kolicina_g !== null)
                            <div><dt class="font-bold">Promena količine</dt><dd>{{ $dogadjaj->kolicina_g > 0 ? '+' : '' }}{{ number_format($dogadjaj->kolicina_g, 0, ',', '.') }} g</dd></div>
                        @endif
                        <div><dt class="font-bold">Evidentirao</dt><dd>{{ $dogadjaj->evidentiraoUser?->name ?? 'Sistem' }}</dd></div>
                    </dl>

                    @if ($dogadjaj->razlog)
                        <p class="mt-3 border-t border-borovnica-dark/10 pt-3 text-sm"><span class="font-bold">Napomena/razlog:</span> {{ $dogadjaj->razlog }}</p>
                    @endif
                </article>
            </li>
        @endforeach
    </ol>
</section>
