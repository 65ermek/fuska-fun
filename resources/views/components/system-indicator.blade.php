<div>
    {{-- 🔔 Иконка в левом нижнем углу --}}
    <div id="system-indicator" style="position: fixed; bottom: 15px; left: 15px; z-index: 1050; cursor: pointer;">
        <div style="width: 42px; height: 42px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 2px 6px rgba(0,0,0,.2);">
            ⚙️
        </div>
    </div>
    {{-- 🪟 Модальное окно --}}
    <div id="system-modal" class="d-none d-md-block"
         style="
     position: fixed;
     bottom: 15px;
     left: 15px;
     transform: translateX(-120%);
     transition: transform .3s ease;
     z-index: 1060;">
        <div id="system-modal-close" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #e9ecef;
        cursor: pointer;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 13px;
        min-width: 260px;
        box-shadow: 0 4px 12px rgba(0,0,0,.25);
        ">
            @if(session('author_logged_in') && session('user_email'))
                {{-- 🔥 ПЕРВЫЙ ПРИОРИТЕТ: АВТОР --}}
                <span style="color: #155724;">
            {{ session('user_name') }} ({{ session('user_email') }})
            <small style="color: #0c5460;">[автор]</small>
            </span>
            @elseif(session('customer_email') || request()->cookie('fuska_customer_email'))
                {{-- 🔥 ВТОРОЙ ПРИОРИТЕТ: КАНДИДАТ --}}
                <span style="color: #155724;">
            {{ session('customer_name') ?? 'User' }}
            ({{ session('customer_email') ?? request()->cookie('fuska_customer_email') }})
            </span>
            @else
                <span style="color: #856404;">не авторизован</span>
            @endif
        </div>
    </div>
</div>
<script>
    const indicator = document.getElementById('system-indicator');
    const modal = document.getElementById('system-modal');
    const closeBtn = document.getElementById('system-modal-close');


    indicator.addEventListener('click', () => {
        modal.style.transform = 'translateX(0)';
    });


    closeBtn.addEventListener('click', () => {
        modal.style.transform = 'translateX(-120%)';
    });
</script>
