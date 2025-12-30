@if($jobs->isEmpty())
    <div class="alert alert-info">Zatím žádné inzeráty.</div>
@else
    <div class="jobs-gallery">
        @foreach($jobs as $job)
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

            <a href="{{ route('jobs.show', $job) }}" class="job-card text-reset text-decoration-none">
                <img src="{{ $src }}" alt="{{ $job->title }}" class="job-card-img">
                <div class="job-card-body">
                    <div class="d-flex align-items-center gap-1 mb-1">
                        <h3 class="job-card-title mb-0">{{ $job->title }}</h3>
                        @if($job->top || $job->paid_at)
                            <span class="ztop">TOP</span>
                        @endif
                    </div>
                    <div class="job-card-meta">
                        {{ $job->city ?? '—' }}@if($job->district) – {{ $job->district }} @endif
                    </div>
                    <div class="job-card-price">
                        {{ $job->price_label ?? 'Dohodou' }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
