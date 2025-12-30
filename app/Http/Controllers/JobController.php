<?php
namespace App\Http\Controllers;

use App\Mail\JobCreatedNotification;
use App\Mail\JobDeletedNotification;
use App\Mail\JobUpdatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\{Customer, Job, JobAction, JobCategory, JobPhoto};
use App\Services\ContentGuard;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class JobController extends Controller
{

    public function index(Request $r)
    {
        // ✅ ИСПРАВЛЕННЫЙ ВАРИАНТ: Определяем $jobs до использования
        $jobs = null;

        if ($r->get('page', 1) == 1 && !$r->filled('category') && !$r->filled('city') && !$r->filled('q') && !$r->filled('sort')) {

            // Оплаченные объявления (максимум 5)
            $paidQuery = Job::with(['category','photos'])
                ->where('status','published')
                ->whereNotNull('paid_at')
                ->orderByDesc('paid_at')
                ->limit(5);

            // Обычные объявления (15 штук, чтобы в сумме было 20)
            $regularQuery = Job::with(['category','photos'])
                ->where('status','published')
                ->whereNull('paid_at')
                ->orderByDesc('created_at')
                ->limit(15);

            // Применяем фильтры к обоим запросам
            if ($r->filled('category')) {
                $paidQuery->whereHas('category', fn($c) => $c->where('slug', $r->category));
                $regularQuery->whereHas('category', fn($c) => $c->where('slug', $r->category));
            }

            if ($r->filled('city')) {
                $paidQuery->where('city', 'like', '%'.$r->city.'%');
                $regularQuery->where('city', 'like', '%'.$r->city.'%');
            }

            if ($r->filled('q')) {
                $searchFn = fn($x) => $x->where('title', 'like', '%'.$r->q.'%')
                    ->orWhere('description', 'like', '%'.$r->q.'%');
                $paidQuery->where($searchFn);
                $regularQuery->where($searchFn);
            }

            $paidJobs = $paidQuery->get();
            $regularJobs = $regularQuery->get();

            // Объединяем результаты
            $allJobs = $paidJobs->concat($regularJobs);

            // Создаем пагинатор вручную с правильными данными
            $totalCount = Job::where('status','published')
                ->when($r->filled('category'), function($q) use ($r) {
                    $q->whereHas('category', fn($c) => $c->where('slug', $r->category));
                })
                ->when($r->filled('city'), function($q) use ($r) {
                    $q->where('city', 'like', '%'.$r->city.'%');
                })
                ->when($r->filled('q'), function($q) use ($r) {
                    $q->where(function ($x) use ($r) {
                        $x->where('title', 'like', '%'.$r->q.'%')
                            ->orWhere('description', 'like', '%'.$r->q.'%');
                    });
                })
                ->count();

            $jobs = new \Illuminate\Pagination\LengthAwarePaginator(
                $allJobs, // Используем объединенную коллекцию
                $totalCount,
                20,
                $r->get('page', 1)
            );

        } else {
            // Стандартный запрос для других страниц/фильтров
            $q = Job::with(['category','photos'])
                ->where('status','published')
                ->orderByRaw('CASE WHEN paid_at IS NOT NULL THEN 0 ELSE 1 END')
                ->orderByDesc('paid_at')
                ->orderByDesc('created_at');

            // фильтры
            if ($r->filled('category')) {
                $q->whereHas('category', fn($c) => $c->where('slug', $r->category));
            }

            if ($r->filled('city')) {
                $q->where('city', 'like', '%'.$r->city.'%');
            }

            if ($r->filled('q')) {
                $q->where(function ($x) use ($r) {
                    $x->where('title', 'like', '%'.$r->q.'%')
                        ->orWhere('description', 'like', '%'.$r->q.'%');
                });
            }

            // сортировка
            if ($r->filled('sort')) {
                switch ($r->sort) {
                    case 'price':
                        $q->orderByDesc('price');
                        break;
                    case 'views':
                        $q->orderByDesc('views');
                        break;
                    case 'new':
                    default:
                        // уже установлено
                        break;
                }
            }

            $jobs = $q->paginate(20)->appends($r->query());
        }

        $categories = JobCategory::orderBy('sort')->get();
        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show(Job $job, Request $r)
    {
        abort_if($job->status !== 'published', 404);

        $viewed = collect(json_decode($r->cookie('viewed_jobs', '[]'), true));

        if (!$viewed->contains($job->id)) {
            $job->increment('views');
            $viewed->push($job->id);
        }
        $isFavorite = false;
        $email = session('email');

        if ($email) {
            $isFavorite = JobAction::where('email', $email)
                ->where('job_id', $job->id)
                ->where('action', 'favorite')
                ->exists();
        }

        // 🔥 ДОБАВЛЯЕМ ПЕРЕМЕННЫЕ ДЛЯ КОМПОНЕНТА
        $jobId = $job->id;
        $jobTitle = $job->title;
        $authorEmail = $job->email;
        $authorName = $job->contact_name;

        return response()
            ->view('jobs.show', [
                'job' => $job->load('category', 'photos'),
                'isFavorite' => $isFavorite,
                // 🔥 ПЕРЕДАЕМ ПЕРЕМЕННЫЕ В ШАБЛОН
                'jobId' => $jobId,
                'jobTitle' => $jobTitle,
                'authorEmail' => $authorEmail,
                'authorName' => $authorName,
            ])
            ->withCookie(cookie('viewed_jobs', $viewed->take(50)->toJson(), 60 * 24 * 7));
    }
    public function create(){
        $categories = JobCategory::orderBy('sort')->get();
        return view('jobs.create', compact('categories'));
    }
    public function store(Request $r, ContentGuard $guard)
    {
        // 1) honeypot + минимальная задержка
        if ($r->filled('website')) {
            abort(422, 'Spam');
        }
        $submitted = $r->input('submitted_at');
        if ($submitted && now()->diffInSeconds($submitted) < 1) {
            abort(422, 'Too fast');
        }

        // 2) валидация
        $data = $r->validate([
            'job_category_id'  => 'required|exists:job_categories,id',
            'city'             => 'required|string|max:100',
            'district'         => 'nullable|string|max:100',
            'title'            => 'required|string|max:120',
            'description'      => 'required|string|max:5000',
            'pay_type'         => 'required|in:per_hour,per_job',
            'price'            => 'nullable|integer|min:0',
            'price_negotiable' => 'sometimes|boolean',

            // личные данные
            'contact_name'     => 'required|string|max:100',
            'phone'            => 'required|string|max:40',
            'email'            => 'nullable|email|max:100',
            'plain_password'   => 'required|string|min:3|max:100',

            'telegram'         => 'nullable|string|max:60',
            'whatsapp'         => 'nullable|string|max:40',
            'photos.*'         => 'image|mimes:jpg,jpeg,png,webp|max:15360',
        ]);

        // 3) IP / UA всегда нужны дальше
        $ip = $r->ip();
        $ua = substr($r->userAgent() ?? '', 0, 255);

        // 4) ограничение по IP — ВЫКЛЮЧАЕМ на локалке
        if (!app()->environment('local')) {
            $max = (int) config('contentguard.limits.max_posts_per_ip_per_hour', 5);
            $count = Job::where('ip', $ip)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($count >= $max) {
                abort(429, 'Too many posts');
            }
        }
        $status = 'published'; // Всегда публикуем

        // 🔥 СОЗДАЕМ/ОБНОВЛЯЕМ CUSTOMER
        $customer = null;
        if (!empty($data['email'])) {
            $customer = Customer::where('email', $data['email'])->first();

            if (!$customer) {
                $persistentToken = hash('sha256', Str::random(40));

                $customer = Customer::create([
                    'email' => $data['email'],
                    'name' => $data['contact_name'],
                    'phone' => $data['phone'],
                    'source' => 'author',
                    'persistent_token' => $persistentToken,
                    'last_seen_at' => now(),
                ]);
            } else {
                $currentSource = $customer->source ?? 'visitor';
                $newSource = $this->updateCustomerRole($currentSource, 'author');

                $updateData = [
                    'source' => $newSource,
                    'last_seen_at' => now(),
                ];

                if (empty($customer->name) && !empty($data['contact_name'])) {
                    $updateData['name'] = $data['contact_name'];
                }
                if (empty($customer->phone) && !empty($data['phone'])) {
                    $updateData['phone'] = $data['phone'];
                }

                $customer->update($updateData);
            }
        }

        // 6) создаём объявление
        $job = new Job($data);
        $job->slug = $this->makeSlugFromTitle($data['title']);
        $job->top  = false;
        $job->price_negotiable = (bool) ($data['price_negotiable'] ?? false);
        $job->status     = $status;
        $job->edit_token = Str::random(48);
        $job->lang       = substr(app()->getLocale(), 0, 2);
        $job->ip         = $ip;
        $job->ua         = $ua;
        $job->email         = $data['email'] ?? null;
        $job->password_hash = Hash::make($data['plain_password']);
        $job->password_plain = $data['plain_password'];

        $job->save();

        // 7) фото
        if ($r->hasFile('photos')) {
            $jobImageDir = public_path("images/jobs/{$job->id}");

            if (!file_exists($jobImageDir)) {
                mkdir($jobImageDir, 0775, true);
            }

            // Создаем экземпляр ImageManager с указанием драйвера
            $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
            $manager = $driver === 'imagick' ? ImageManager::imagick() : ImageManager::gd();

            foreach ($r->file('photos') as $i => $file) {
                if ($file->isValid()) {

                    $filename = uniqid().'.'.$file->getClientOriginalExtension();
                    $path = $jobImageDir.'/'.$filename;

                    // 🔥 Обработка фото через ImageManager
                    $img = $manager->read($file->getRealPath());

                    // Ресайз по большей стороне (например, 1920px)
                    $img->resize(1920, 1920, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Сжимаем до max ~500 KB
                    $quality = 75;
                    $maxKb = 500;
                    do {
                        $img->save($path, $quality);
                        $sizeKb = filesize($path) / 1024;
                        $quality -= 5;
                    } while ($sizeKb > $maxKb && $quality >= 30);

                    JobPhoto::create([
                        'job_id' => $job->id,
                        'path'   => "/images/jobs/{$job->id}/{$filename}",
                        'sort'   => $i,
                    ]);
                }
            }
        }

        // 8) сохраняем сессию и cookie
        if ($customer) {
            session([
                'customer_email' => $customer->email,
                'customer_name' => $customer->name,
                'customer_id' => $customer->id,
                'customer_source' => $customer->source
            ]);

            $customerCookie = cookie(
                'fuska_customer_token',
                $customer->persistent_token,
                60 * 24 * 365,
                null, null, false, false
            );
        }

        // 9) cookie с edit_token
        $tokens = collect(json_decode($r->cookie('fuska_tokens', '[]'), true))
            ->filter()
            ->values();
        $tokens->push($job->edit_token);
        $tokens = $tokens->unique()->take(50);

        $successMessage = match(app()->getLocale()) {
            'cs' => 'Inzerát byl úspěšně publikován',
            'ru' => 'Объявление успешно опубликовано',
            'sk' => 'Inzerát bol úspešne publikovaný',
            'pl' => 'Ogłoszenie zostało pomyślnie opublikowane',
            'de' => 'Anzeige erfolgreich veröffentlicht',
            default => 'Advertisement published successfully'
        };

        $response = redirect()->route('jobs.index')
            ->withCookie(cookie('fuska_tokens', $tokens->toJson(), 60 * 24 * 180))
            ->with('ok', $successMessage);

        if ($customer) {
            $response->withCookie($customerCookie);
            $response->headers->set('X-Debug-Customer-Token', $customer->persistent_token);
        }

        // 10) отправка емайла
        if (!empty($data['email'])) {
            Mail::to($data['email'])->send(new JobCreatedNotification($job));
        }

        return $response;
    }
    /**
     * 🔥 МЕТОД ДЛЯ ОБНОВЛЕНИЯ РОЛЕЙ
     */
    private function updateCustomerRole($currentSource, $newRole)
    {
        if ($currentSource === $newRole) {
            return $currentSource;
        }

        if ($currentSource === 'visitor' || empty($currentSource)) {
            return $newRole;
        }

        $roles = [$currentSource, $newRole];

        if (in_array('author', $roles) && in_array('candidat', $roles)) {
            return 'both';
        }

        return $newRole;
    }

    private function ensureToken(Request $r, Job $job): void {
        $token = $r->input('token') ?? $r->route('token') ?? $r->query('token');
        abort_if(!$token || $token!==$job->edit_token,403);
    }
    public function update(Request $request, $slug)
    {
        // 1️⃣ Находим объявление по slug
        $job = Job::where('slug', $slug)->firstOrFail();

        // 2️⃣ Валидация
        $data = $request->validate([
            'job_category_id' => 'required|exists:job_categories,id',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'title' => 'required|string|max:120',
            'description' => 'required|string|max:5000',
            'pay_type' => 'required|in:per_hour,per_job',
            'price' => 'nullable|integer|min:0',
            'price_negotiable' => 'sometimes|boolean',
            'contact_name' => 'required|string|max:100',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:100',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:15360',
            'photo_order' => 'nullable|string',
        ]);

        // 3️⃣ Обновление текста
        $job->update([
            ...$data,
            'price_negotiable' => (bool) ($data['price_negotiable'] ?? false),
        ]);

        // 4️⃣ Загрузка новых фото
        if ($request->hasFile('photos')) {
            $jobImageDir = public_path("images/jobs/{$job->id}");

            if (!file_exists($jobImageDir)) {
                mkdir($jobImageDir, 0775, true);
            }

            $existingCount = $job->photos()->count();
            $uploaded = 0;

            foreach ($request->file('photos') as $file) {
                if ($file->isValid()) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($jobImageDir, $filename);

                    JobPhoto::create([
                        'job_id' => $job->id,
                        'path'   => "/images/jobs/{$job->id}/{$filename}",
                        'sort'   => $existingCount + $uploaded,
                    ]);

                    $uploaded++;
                }
            }
        }

        // 5️⃣ Сортировка фото
        if ($request->filled('photo_order')) {
            $ids = explode(',', $request->input('photo_order'));

            foreach ($ids as $index => $photoId) {
                JobPhoto::where('id', $photoId)
                    ->where('job_id', $job->id)
                    ->update(['sort' => $index]);
            }
        }

        // 6️⃣ отправка письма
        $job->refresh();
        if ($job->email) {
            Mail::to($job->email)->send(new JobUpdatedNotification($job));
        }

        // 7️⃣ Редирект обратно на страницу редактирования
        return redirect()
            ->route('jobs.my', ['slug' => $job->slug])
            ->with('ok', __('messages.notifications.ad_updated'));
    }
    public function manageBySlug($slug, Request $request)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        // Получаем список токенов из cookie
        $tokens = collect(json_decode($request->cookie('fuska_tokens', '[]'), true))->filter()->values();

        // Проверяем: пользователь может управлять этим объявлением?
        if (!in_array($job->edit_token, $tokens->toArray())) {
            abort(403, 'Nemáte přístup k tomuto inzerátu.');
        }

        return view('jobs.manage', compact('job'));
    }
    public function report(Request $r, Job $job){
        \DB::table('job_reports')->insert(['job_id'=>$job->id,'reason'=>$r->input('reason','not_job'),'ip'=>$r->ip(),'created_at'=>now(),'updated_at'=>now()]);
        $cnt=\DB::table('job_reports')->where('job_id',$job->id)->count();
        if ($cnt>=3 && $job->status==='published'){ $job->update(['status'=>'pending']); }
        return back()->with('ok', __('messages.reported'));
    }
    public function my(Request $request)
    {
        // 🔥 ЕСЛИ ЕСТЬ CUSTOMER В СЕССИИ - ПОКАЗЫВАЕМ ЕГО ОБЪЯВЛЕНИЯ
        $customerEmail = session('customer_email');
        $jobs = collect();

        if ($customerEmail) {
            $jobs = Job::where('email', $customerEmail)
                ->when($request->has('archiv'), function($q) {
                    $q->where('status', '!=', 'published');
                }, function($q) {
                    $q->where('status', 'published');
                })
                ->orderByDesc('created_at')
                ->get();

            \Log::info("📋 Showing jobs for session customer", [
                'customer_email' => $customerEmail,
                'jobs_count' => $jobs->count()
            ]);
        }

        // 🔥 ЕСЛИ НЕТ ОБЪЯВЛЕНИЙ - ИСПОЛЬЗУЕМ СТАРУЮ ЛОГИКУ
        if ($jobs->isEmpty()) {
            $tokens = collect(json_decode($request->cookie('fuska_tokens', '[]'), true))
                ->filter()
                ->values();

            if ($tokens->isNotEmpty()) {
                $jobs = Job::whereIn('edit_token', $tokens)
                    ->when($request->has('archiv'), function($q) {
                        $q->where('status', '!=', 'published');
                    }, function($q) {
                        $q->where('status', 'published');
                    })
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        return view('jobs.my', compact('jobs'));
    }
    /**
     * 🔥 СВЯЗЫВАЕМ ОБЪЯВЛЕНИЯ С CUSTOMER
     */
    private function linkJobsToCustomer($jobs, $customerEmail)
    {
        $customer = Customer::where('email', $customerEmail)->first();

        if ($customer) {
            $updatedCount = Job::where('email', $customerEmail)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);

            \Log::info("🔗 Linked jobs to customer", [
                'customer_id' => $customer->id,
                'jobs_linked' => $updatedCount
            ]);
        }
    }

    /**
     * 🔥 СОЗДАЕМ CUSTOMER ИЗ ОБЪЯВЛЕНИЯ
     */
    private function createCustomerFromJob(Job $job)
    {
        if (!$job->email) return;

        $customer = Customer::where('email', $job->email)->first();

        if (!$customer) {
            // Создаем нового customer
            $persistentToken = hash('sha256', Str::random(40));

            $customer = Customer::create([
                'email' => $job->email,
                'name' => $job->contact_name,
                'phone' => $job->phone,
                'source' => 'author',
                'persistent_token' => $persistentToken,
                'last_seen_at' => now(),
            ]);
        }

        // Сохраняем в сессию
        session([
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'customer_source' => $customer->source,
        ]);

        return $customer;
    }
    public function myAds(Request $r)
    {
        \Log::info("myAds() called", [
            'customer_email' => session('customer_email'),
            'tokens_cookie' => $r->cookie('fuska_tokens')
        ]);

        // 🔥 Если в сессии есть email — ищем объявления по нему
        if (session('customer_email')) {

            \Log::info("📌 myAds(): using session customer_email", [
                'email' => session('customer_email')
            ]);

            $jobs = Job::where('email', session('customer_email'))
                ->when(!$r->boolean('archiv'), fn($q) => $q->whereNull('deleted_at'))
                ->when($r->boolean('archiv'), fn($q) => $q->onlyTrashed())
                ->orderByDesc('created_at')
                ->get();

            return view('jobs.my', [
                'jobs' => $jobs,
                'categories' => JobCategory::orderBy('sort')->get()
            ]);
        }

        // --- старая логика с токенами ---
        $tokens = collect(json_decode($r->cookie('fuska_tokens', '[]'), true))
            ->filter()
            ->values();

        if ($tokens->isEmpty()) {
            $jobs = collect();
        } else {
            $jobsQuery = Job::withTrashed()->whereIn('edit_token', $tokens);

            if ($r->boolean('archiv')) {
                $jobsQuery->onlyTrashed();
            } else {
                $jobsQuery->whereNull('deleted_at');
            }

            $jobs = $jobsQuery->orderByDesc('created_at')->get();
        }

        $categories = JobCategory::orderBy('sort')->get();

        return view('jobs.my', compact('jobs', 'categories'));
    }

    public function recoverAds(Request $request)
    {
        // 🔥 ЕСЛИ АВТОР АВТОРИЗОВАН - НЕ ДАЕМ ПЕРЕКЛЮЧАТЬСЯ НА КАНДИДАТА
        if (session('author_logged_in') || session('user_email')) {
            Log::warning("ATTEMPT TO SWITCH CUSTOMER DURING AUTHOR SESSION BLOCKED", [
                'author_email' => session('user_email'),
                'attempted_customer' => $request->email
            ]);
            return redirect()->back()->with('error', 'Cannot switch customer during author session.');
        }
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // 🔥 АВТОРИЗУЕМ CUSTOMER ПЕРЕД ВОССТАНОВЛЕНИЕМ
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            session([
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'customer_name' => $customer->name,
                'customer_source' => $customer->source,
                'customer_phone' => $customer->phone,
            ]);

            \Log::info("👤 Customer switched via recover form", [
                'customer_id' => $customer->id,
                'email' => $customer->email
            ]);
        } else {
            // 🔥 ЕСЛИ CUSTOMER НЕ СУЩЕСТВУЕТ - СОЗДАЕМ ИЗ ОБЪЯВЛЕНИЙ
            $customer = $this->createCustomerFromJobs($email);
        }

        // Существующая логика восстановления
        $jobs = Job::where('email', $email)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        if ($jobs->isEmpty()) {
            return back()->withErrors(['email' => __('messages.my_ads.no_ads_for_email')]);
        }

        return redirect()->route('jobs.my');
    }

    /**
     * 🔥 СОЗДАЕМ CUSTOMER ИЗ ОБЪЯВЛЕНИЙ ЕСЛИ ЕГО НЕТ
     */
    private function createCustomerFromJobs($email)
    {
        // Ищем объявления с этим email
        $jobs = Job::where('email', $email)->get();

        if ($jobs->isEmpty()) {
            return null;
        }

        $firstJob = $jobs->first();

        // Создаем нового customer
        $persistentToken = hash('sha256', Str::random(40));

        $customer = Customer::create([
            'email' => $email,
            'name' => $firstJob->contact_name,
            'phone' => $firstJob->phone,
            'source' => 'author',
            'persistent_token' => $persistentToken,
            'last_seen_at' => now(),
        ]);

        // 🔥 СВЯЗЫВАЕМ ОБЪЯВЛЕНИЯ С CUSTOMER
        Job::where('email', $email)->update(['customer_id' => $customer->id]);

        // Сохраняем в сессию
        session([
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'customer_source' => $customer->source,
            'customer_phone' => $customer->phone,
        ]);

        \Log::info("👤 New customer created from jobs", [
            'customer_id' => $customer->id,
            'email' => $email,
            'jobs_count' => $jobs->count()
        ]);

        return $customer;
    }
    private function makeSlugFromTitle(string $title): string
    {
        // берём первое слово
        $first = trim(strtok($title, ' '));

        // делаем его "человеческим" латинским
        $base = Str::slug($first);

        // 6-значный рандом
        $rand = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        return $base . '-' . $rand;
    }
// Показ страницы управления
    public function manage(string $slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return view('jobs.manage', compact('job'));
    }


// Обработка кнопок "Upravit" или "Vymazat"
    public function manageAction(Request $request, $slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        // Проверка пароля
        if (!Hash::check($request->input('heslobazar'), $job->password_hash)) {
            return redirect()->route('jobs.manage', $job->slug)->with('wrong_password', true);
        }

        $action = $request->input('administrace');

        if ($action === 'edit') {
            return redirect()->route('jobs.edit', ['slug' => $slug]);
        }

        if ($action === 'delete') {
            $job->delete();
            // 🔄 Редирект на "мои объявления"
            return redirect()->route('jobs.my')->with('ok', __('messages.notifications.ad_deleted'));
        }

        return redirect()->route('jobs.manage', $job->slug)->with('error', __('messages.notifications.invalid_action'));
    }
    public function editBySlug($slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();
        $categories = JobCategory::all();

        return view('jobs.edit', compact('job', 'categories'));
    }


    public function requestPassword(Request $request)
    {
        // Допустимые слова подтверждения для всех языков
        $allowedConfirmations = [
            'cs' => 'Ano',
            'ru' => 'Да',
            'uk' => 'Так',
            'uz' => 'Ha',
            'ro' => 'Da',
            'en' => 'Yes'
        ];
        // Получаем текущую локаль
        $locale = app()->getLocale();

        // Получаем слово подтверждения для текущего языка
        $confirmationWord = $allowedConfirmations[$locale] ?? 'Yes';

        $request->validate([
            'potvrzeni' => 'required|string|in:' . $confirmationWord,
            'job_id' => 'required|exists:jobs,id'
        ]);

        $job = Job::findOrFail($request->job_id);

        if (!$job->email) {
            return back()->with('error', __('messages.password_recovery.email_not_available'));
        }

        $plainPassword = $job->password_plain;

        if (!$plainPassword) {
            return back()->with('error', __('messages.password_recovery.password_not_available'));
        }

        // Отправляем email с переведенным текстом
        Mail::raw(
            __('messages.password_recovery.email_body', [
                'id' => $job->id,
                'password' => $plainPassword
            ], $locale),
            function ($message) use ($job, $locale) {
                $message->to($job->email)
                    ->subject(
                        __('messages.password_recovery.email_subject', ['id' => $job->id], $locale)
                    );
            }
        );

        return back()->with('success', __('messages.password_recovery.password_sent'));
    }
    public function destroy($slug, Request $request)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        // Проверка токена
        $tokens = collect(json_decode($request->cookie('fuska_tokens', '[]'), true))->filter();
        if (!in_array($job->edit_token, $tokens->toArray())) {
            abort(403);
        }
        // 📧 Отправка письма перед удалением
        if ($job->email) {
            try {
                Mail::to($job->email)->send(new JobDeletedNotification($job));
            } catch (\Exception $e) {
                logger()->error('Ошибка при отправке письма об удалении: ' . $e->getMessage());
            }
        }
        $job->delete(); // мягкое удаление

        return redirect()->route('jobs.my')->with('ok', 'Inzerát byl smazán.');
    }
    public function prolong(Request $request, $slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        // Проверка по токену управления
        $tokens = collect(json_decode($request->cookie('fuska_tokens', '[]'), true))->filter();
        if (!in_array($job->edit_token, $tokens->toArray())) {
            abort(403, 'Nemáte přístup k tomuto inzerátu.');
        }

        // Обновим дату публикации и сбросим предупреждение
        $job->update([
            'created_at' => now(),
            'warning_sent_at' => null,
        ]);

        return redirect()->route('jobs.manage', $job->slug)
            ->with('ok', 'Platnost inzerátu byla prodloužena o další měsíc.');
    }

}
