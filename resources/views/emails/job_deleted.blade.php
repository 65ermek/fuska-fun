<p><strong>Váš inzerát byl smazán.</strong></p>

<p>Následující inzerát byl úspěšně odstraněn z portálu Fuska:</p>

<hr>

<h3>{{ $job->title }}</h3>

<p>{!! nl2br(e($job->description)) !!}</p>

<p>
    <strong>Jméno:</strong> {{ $job->contact_name }}<br>
    <strong>Telefon:</strong> {{ $job->phone }}<br>
    <strong>E-mail:</strong> {{ $job->email }}<br>
    <strong>Cena:</strong> {{ $job->price_label }}
</p>

<p>Pokud jste inzerát nesmazali vy, kontaktujte prosím naši podporu.</p>

<p>S pozdravem,<br>tým <strong>Fuska.fun</strong></p>

