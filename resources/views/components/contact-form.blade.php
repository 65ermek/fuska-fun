@props([
    'jobId',
    'jobTitle',
    'authorEmail' => '',
    'authorName' => ''
])

<div class="kontaktniformular mt-4"
     data-job-id="{{ $jobId }}"
     data-job-title="{{ $jobTitle }}"
     data-author-email="{{ $authorEmail }}"
     data-author-name="{{ $authorName }}">
    <div class="oranzovy" id="overlaymail">
        <b>{{ __('messages.job_detail.contact_form.title') }}</b><br><br>

        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <input type="text"
                       name="name"
                       id="contact_form_name"
                       maxlength="50"
                       class="form-control"
                       placeholder="{{ __('messages.job_detail.name') }}"
                       autocomplete="name"
                       value="{{ session('customer_name') ?? session('user_name') ?? '' }}">
            </div>
            <div class="col-md-3">
                <input type="tel"
                       name="phone"
                       id="contact_form_phone"
                       maxlength="20"
                       class="form-control"
                       placeholder="{{ __('messages.job_detail.phone') }}"
                       autocomplete="tel"
                       value="{{ session('customer_phone') ?? '' }}">
            </div>
            <div class="col-md-6">
                <input type="email"
                       name="email"
                       id="contact_form_email"
                       maxlength="50"
                       required
                       class="form-control"
                       placeholder="Email *"
                       autocomplete="email"
                       value="{{ session('customer_email') ?? session('user_email') ?? '' }}">

            </div>
        </div>

        <label for="contact_form_texto">{{ __('messages.job_detail.contact_form.message') }} *</label>
        <textarea name="texto"
                  id="contact_form_texto"
                  class="form-control mb-3 textpridat"
                  rows="4"
                  required
                  autocomplete="off"
                  placeholder="{{ __('messages.job_detail.contact_form.message') }}"></textarea>

        <button type="button" id="contact_form_button" class="btn btn-warning btn-sm">{{ __('messages.job_detail.contact_form.send') }}</button>
    </div>
</div>

<script>
    console.log('🔧 CONTACT_FORM_SCRIPT_LOADED');

    class ContactForm {
        constructor(options = {}) {
            console.log('🔧 CONTACT_FORM_CONSTRUCTOR_CALLED', options);

            this.jobId = options.jobId;
            this.jobTitle = options.jobTitle;
            this.authorEmail = options.authorEmail;
            this.authorName = options.authorName;

            this.selectors = {
                form: '.kontaktniformular',
                name: '#contact_form_name',
                phone: '#contact_form_phone',
                email: '#contact_form_email',
                message: '#contact_form_texto',
                button: '#contact_form_button'
            };

            this.init();
        }

        init() {
            console.log('🔧 CONTACT_FORM_INIT_CALLED');
            this.bindEvents();
            this.restoreFormData();
        }

        bindEvents() {
            console.log('🔧 CONTACT_FORM_BIND_EVENTS_CALLED');

            const sendButton = document.querySelector(this.selectors.button);
            console.log('🔧 SEND_BUTTON_FOUND:', !!sendButton);

            if (sendButton) {
                sendButton.addEventListener('click', () => {
                    console.log('🔧 SEND_BUTTON_CLICKED');
                    this.handleSubmit();
                });
            }

            const messageField = document.querySelector(this.selectors.message);
            if (messageField) {
                messageField.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' && e.ctrlKey) {
                        console.log('🔧 CTRL+ENTER_PRESSED');
                        this.handleSubmit();
                    }
                });
            }

            this.addRealTimeValidation();
        }

        addRealTimeValidation() {
            const emailInput = document.querySelector(this.selectors.email);
            const phoneInput = document.querySelector(this.selectors.phone);

            if (emailInput) {
                emailInput.addEventListener('blur', () => this.validateEmail(emailInput));
            }

            if (phoneInput) {
                phoneInput.addEventListener('blur', () => this.validatePhone(phoneInput));
            }
        }

        validateEmail(input) {
            const email = input.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email && !emailRegex.test(email)) {
                this.showFieldError(input, 'Zadejte platnou emailovou adresu');
                return false;
            } else {
                this.clearFieldError(input);
                return true;
            }
        }

        validatePhone(input) {
            const phone = input.value;
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;

            if (phone && !phoneRegex.test(phone)) {
                this.showFieldError(input, 'Zadejte platné telefonní číslo');
                return false;
            } else {
                this.clearFieldError(input);
                return true;
            }
        }

        showFieldError(input, message) {
            this.clearFieldError(input);

            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger small mt-1';
            errorDiv.textContent = message;

            input.parentNode.appendChild(errorDiv);
            input.classList.add('is-invalid');
        }

        clearFieldError(input) {
            const errorDiv = input.parentNode.querySelector('.text-danger');
            if (errorDiv) {
                errorDiv.remove();
            }
            input.classList.remove('is-invalid');
        }

        getFormData() {
            return {
                job_id: this.jobId,
                name: document.querySelector(this.selectors.name)?.value || '',
                phone: document.querySelector(this.selectors.phone)?.value || '',
                email: document.querySelector(this.selectors.email)?.value || '',
                texto: document.querySelector(this.selectors.message)?.value || '',
                author_name: this.authorName,
                author_email: this.authorEmail
            };
        }

        validateForm() {
            const data = this.getFormData();

            if (!data.email) {
                this.showToast('Zadejte prosím svůj email', 'error');
                return false;
            }

            if (!data.texto) {
                this.showToast('Zadejte prosím zprávu', 'error');
                return false;
            }

            if (!this.validateEmail(document.querySelector(this.selectors.email))) {
                return false;
            }

            if (data.phone && !this.validatePhone(document.querySelector(this.selectors.phone))) {
                return false;
            }

            return true;
        }

        async handleSubmit() {
            console.log('🔧 Вызываем');

            if (!this.validateForm()) {
                console.log('🔧 валидируем');
                return;
            }

            console.log('🔧 итоги валидации');

            const formData = this.getFormData();
            console.log('🔧 данные:', formData);

            this.setLoading(true);

            try {
                console.log('🚀 отправка письма', {
                    jobId: this.jobId,
                    email: formData.email,
                    hasMessage: !!formData.texto,
                    messageLength: formData.texto.length
                });

                const response = await fetch('{{ route("job.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                console.log('📨 ответ сервера', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok
                });

                const responseText = await response.text();
                console.log('📄 расшифровка данных', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('✅ разбор', result);
                } catch (e) {
                    console.error('❌ ошибка разбора', e);
                    if (response.ok) {
                        result = { status: 'ok' };
                    } else {
                        throw new Error('Chyba serveru: ' + responseText.substring(0, 100));
                    }
                }

                if (response.ok) {
                    console.log('🎉 данные прошли', {
                        customerId: result.customer_id,
                        customerEmail: result.customer_email,
                        sessionUpdated: result.session_updated,
                        savedToLocalStorage: true
                    });

                    this.saveFormData(formData);
                    this.showToast('Vaše zpráva byla odeslána autorovi!', 'success');
                    this.resetForm();

                    // 🔥 ПЕРЕЗАГРУЖАЕМ СТРАНИЦУ ДЛЯ ОБНОВЛЕНИЯ СЕССИИ
                    if (result.session_updated) {
                        console.log('🔄 сессия возобновлена');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }

                } else {
                    console.error('❌ ошибка сервера', result);
                    throw new Error(result.message || 'Odeslání zprávy se nezdařilo');
                }

            } catch (error) {
                console.error('💥 ошибка формы', {
                    error: error.message,
                    stack: error.stack
                });

                this.showToast('Chyba: ' + error.message, 'error');
            } finally {
                this.setLoading(false);
            }
        }

        saveFormData(formData) {
            try {
                const dataToSave = {
                    name: formData.name || '',
                    phone: formData.phone || '',
                    email: formData.email || ''
                };
                localStorage.setItem('contact_form_data', JSON.stringify(dataToSave));
                console.log('💾 FORM_DATA_SAVED_TO_LOCALSTORAGE');
            } catch (e) {
                console.warn('❌ COULD_NOT_SAVE_FORM_DATA');
            }
        }

        restoreFormData() {
            try {
                const savedData = localStorage.getItem('contact_form_data');
                if (savedData) {
                    const data = JSON.parse(savedData);

                    const nameField = document.querySelector(this.selectors.name);
                    const phoneField = document.querySelector(this.selectors.phone);
                    const emailField = document.querySelector(this.selectors.email);

                    if (nameField && !nameField.value && data.name) {
                        nameField.value = data.name;
                    }
                    if (phoneField && !phoneField.value && data.phone) {
                        phoneField.value = data.phone;
                    }
                    if (emailField && !emailField.value && data.email) {
                        emailField.value = data.email;
                    }
                    console.log('🔧 FORM_DATA_RESTORED_FROM_LOCALSTORAGE');
                }
            } catch (e) {
                console.log('🔧 NO_SAVED_FORM_DATA');
            }
        }

        setLoading(isLoading) {
            const button = document.querySelector(this.selectors.button);
            if (button) {
                if (isLoading) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Odesílání...';
                } else {
                    button.disabled = false;
                    button.textContent = 'Odeslat zprávu';
                }
            }
        }

        showToast(message, type = 'success') {
            if (typeof showFuskaAlert === 'function') {
                showFuskaAlert(message, type);
            } else {
                alert(message);
            }
        }

        resetForm() {
            document.querySelector(this.selectors.message).value = '';
            this.clearFieldError(document.querySelector(this.selectors.email));
            this.clearFieldError(document.querySelector(this.selectors.phone));
            console.log('🔧 FORM_RESET');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 DOM_CONTENT_LOADED - CONTACT_FORM');

        const contactFormElement = document.querySelector('.kontaktniformular');
        console.log('🔧 CONTACT_FORM_ELEMENT_FOUND:', !!contactFormElement);

        if (contactFormElement) {
            const options = {
                jobId: contactFormElement.dataset.jobId,
                jobTitle: contactFormElement.dataset.jobTitle,
                authorEmail: contactFormElement.dataset.authorEmail,
                authorName: contactFormElement.dataset.authorName
            };

            console.log('🔧 CONTACT_FORM_OPTIONS:', options);

            new ContactForm(options);
        }
    });
</script>
