<p><strong>Váš inzerát byl upraven.</strong></p>

<p>
    Můžete si jej zobrazit nebo upravit znovu na následujícím odkazu:<br>
    <a href="{{ route('jobs.edit', $job->slug) }}">{{ route('jobs.edit', $job->slug) }}</a>
</p>

<hr>

<h3>{{ $job->title }}</h3>

<p>{!! nl2br(e($job->description)) !!}</p>

<p>
    <strong>Jméno:</strong> {{ $job->contact_name }}<br>
    <strong>Telefon:</strong> {{ $job->phone }}<br>
    <strong>E-mail:</strong> {{ $job->email }}<br>
    <strong>Cena:</strong> {{ $job->price_label }}
</p>

<p>Seznam všech vašich inzerátů najdete zde:<br>
    <a href="{{ route('jobs.my') }}">{{ route('jobs.my') }}</a>
</p>

<p>S pozdravem,<br>tým <strong>Fuska.fun</strong></p>

