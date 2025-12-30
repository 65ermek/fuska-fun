<footer class="bg-light border-top mt-auto py-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="small text-muted">
            © {{ date('Y') }} Fuska.fun – nabídky práce a výpomoci.
        </div>
        <div class="small text-muted">
            <a href="#" class="text-muted me-3">E-mail - tanatar.sro@seznam.cz</a>
        </div>
        <div class="small text-muted">
            <a href="#" class="text-muted me-3">+420 732 199 285</a>
        </div>
        <div class="small">
            <a href="{{ route('terms') }}" class="text-muted me-3">
                {{ __('terms.agreement') }}
            </a>
            <a href="#" class="text-muted">Kontakt</a>
        </div>
    </div>
</footer>
