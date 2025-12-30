<p>Váš inzerát <strong>„{{ $job->title }}“</strong> byl zveřejněn před měsícem.</p>

<p>Za 30 dní bude automaticky smazán.</p>

<form method="POST" action="{{ route('jobs.prolong', $job->slug) }}">
    @csrf
    <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none;">
        Prodloužit platnost inzerátu o měsíc
    </button>
</form>

<p><a href="{{ route('jobs.manage', $job->slug) }}">{{ route('jobs.manage', $job->slug) }}</a></p>

<hr>
<p>
    <strong>{{ $job->contact_name }}</strong><br>
    E-mail: {{ $job->email }}<br>
    Telefon: {{ $job->phone }}<br>
    Cena: {{ $job->price_label }}
</p>

<p>{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 300) }}</p>
