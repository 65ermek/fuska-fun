@forelse($jobs as $job)
    @php
        $photoPath = optional($job->photos->first())->path;
        if ($photoPath) {
            $src = asset('/' . ltrim($photoPath, '/'));
        } else {
            $catImage = $job->category?->image
                ? 'images/' . ltrim($job->category->image, '/')
                : 'images/default.png';
            $src = asset($catImage);
        }
    @endphp

    <div class="inzeraty-wrapper">
        <a href="{{ route('jobs.show', $job) }}" class="text-reset text-decoration-none">
            <div class="inzeraty inzeratyflex">
                <div class="inzeratynadpis">
                    <img src="{{ $src }}"
                         class="obrazek"
                         alt="{{ $job->title }}"
                         width="170" height="128"
                         style="object-fit:cover;">

                    <div class="d-flex align-items-center flex-wrap mt-2">
                        <h2 class="nadpis me-2 mb-0">{{ $job->title }}</h2>

                        @if($job->top || $job->paid_at)
                            <span class="ztop mx-1">TOP</span>
                        @endif

                        <span class="velikost10 text-muted">[{{ $job->created_at->format('j.n. Y') }}]</span>
                    </div>

                    <div class="popis">
                        {{ \Illuminate\Support\Str::limit(strip_tags($job->description), 220) }}
                    </div>
                </div>

                <div class="inzeratycena">
                    <b><span translate="no">{{ $job->price_label ?? 'Dohodou' }}</span></b>
                </div>

                <div class="inzeratylok">
                    {{ $job->city ?? '—' }}<br>{{ $job->district ?? '' }}
                </div>

                <div class="inzeratyview">
                    {{ $job->views ?? 0 }} ×
                </div>
            </div>
        </a>

        {{-- Возвращаем блок действий --}}
        <div class="inzeratyakce">
            <a href="https://www.bazos.cz/report.php?idad={{ $job->id }}&report=1" class="akce" rel="nofollow">Označit špatný inzerát</a>
            <a href="https://www.bazos.cz/report.php?idad={{ $job->id }}&report=2" class="akce" rel="nofollow">Chybnou kategorii</a>
            <a href="https://www.bazos.cz/hodnoceni.php?idmail={{ crc32($job->email) }}&idphone={{ crc32($job->phone) }}&jmeno={{ urlencode($job->contact_name) }}" class="akce" rel="nofollow">Ohodnotit uživatele</a>
            <a href="{{ route('jobs.manage', $job->slug) }}">Smazat / Upravit / Topovat</a>
        </div>
    </div>
@empty
    <div class="alert alert-info">Zatím žádné inzeráty.</div>
@endforelse
<style>
    .inzeraty-wrapper {
        position: relative;
        transition: background-color 0.3s ease;
    }

    .inzeraty-wrapper:hover {
        background-color: #bbbbbb;
    }

    .inzeratyakce {
        opacity: 0;
        transition: opacity 0.3s ease;
        padding: 6px 10px;
        font-size: 13px;
        background-color: rgba(255, 255, 255, 0.9);
        position: relative;
        z-index: 1;
    }

    .inzeraty-wrapper:hover .inzeratyakce {
        opacity: 1;
    }
</style>
