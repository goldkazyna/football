@extends('layouts.guest')

@section('title', 'Регистрация — Верификация')

@push('styles')
<style>
    .steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: var(--space-xl);
    }

    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--border-input);
        transition: all var(--transition-base);
    }

    .step-dot.active {
        width: 24px;
        border-radius: 4px;
        background: var(--primary);
    }

    .step-dot.done {
        background: var(--primary);
    }

    .register-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }

    .register-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .register-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .info-box {
        display: flex;
        gap: var(--space-md);
        padding: var(--space-md);
        background: rgba(59, 130, 246, 0.06);
        border: 1px solid rgba(59, 130, 246, 0.12);
        border-radius: var(--radius-md);
        margin-bottom: var(--space-xl);
    }

    .info-box svg {
        width: 20px;
        height: 20px;
        color: var(--accent-blue);
        flex-shrink: 0;
        margin-top: 1px;
    }

    .info-box p {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .upload-zone {
        border: 2px dashed var(--border-input);
        border-radius: var(--radius-lg);
        padding: var(--space-2xl) var(--space-lg);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        margin-bottom: var(--space-lg);
        position: relative;
    }

    .upload-zone:hover {
        border-color: var(--accent-blue);
        background: rgba(59, 130, 246, 0.03);
    }

    .upload-zone.has-file {
        border-color: var(--primary);
        border-style: solid;
        background: rgba(34, 197, 94, 0.04);
    }

    .upload-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--bg-input);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-md);
    }

    .upload-icon svg {
        width: 24px;
        height: 24px;
        color: var(--text-muted);
    }

    .upload-zone.has-file .upload-icon svg {
        color: var(--primary);
    }

    .upload-zone h3 {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .upload-zone p {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .file-name {
        display: none;
        align-items: center;
        gap: var(--space-sm);
        padding: 10px var(--space-md);
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        margin-bottom: var(--space-lg);
        font-size: 0.8125rem;
        color: var(--text-secondary);
    }

    .file-name.show {
        display: flex;
    }

    .file-name svg {
        width: 16px;
        height: 16px;
        color: var(--primary);
        flex-shrink: 0;
    }

    .file-name span {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .nav-buttons {
        display: flex;
        gap: var(--space-sm);
    }

    .btn-back {
        width: 48px;
        min-width: 48px;
        height: 48px;
        flex-shrink: 0;
        background: transparent;
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.04);
    }

    .btn-back svg {
        width: 20px;
        height: 20px;
    }
</style>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-1">
        <h1>Верификация врача</h1>
        <p>Подтвердите, что вы являетесь медицинским специалистом</p>
    </div>

    <!-- Info -->
    <div class="info-box animate-in animate-in-delay-2">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <p>Загрузите скан или фото вашего диплома врача или действующего сертификата специалиста. Допустимые форматы: JPG, JPEG, PNG, PDF. Максимальный размер: 2 МБ.</p>
    </div>

    <!-- Upload form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'verification') }}" enctype="multipart/form-data">
        @csrf

        <div class="upload-zone" id="uploadZone">
            <div class="upload-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </div>
            <h3 id="uploadTitle">Загрузите документ</h3>
            <p id="uploadHint">Нажмите или перетащите файл сюда<br>JPG, JPEG, PNG, PDF &mdash; до 10 МБ</p>
            <input type="file" name="document" id="docFile" accept=".jpg,.jpeg,.png,.pdf">
        </div>

        @error('document')
            <div class="form-error" style="margin-bottom: var(--space-md);">{{ $message }}</div>
        @enderror

        <div class="file-name" id="fileName">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span id="fileNameText"></span>
        </div>

        <div class="nav-buttons">
            <a href="{{ route('register.step', 'team') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <button type="submit" class="btn btn-primary" style="flex:1;">Отправить на проверку</button>
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 5 из 6
    </div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('docFile');
    const uploadZone = document.getElementById('uploadZone');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadZone.classList.add('has-file');
            document.getElementById('uploadTitle').textContent = 'Файл выбран';
            document.getElementById('uploadHint').textContent = 'Нажмите, чтобы заменить';
            document.getElementById('fileName').classList.add('show');
            document.getElementById('fileNameText').textContent = file.name;
        }
    });

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = 'var(--accent-blue)';
        uploadZone.style.background = 'rgba(59,130,246,0.06)';
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.style.borderColor = '';
        uploadZone.style.background = '';
    });
</script>
@endpush
