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
        margin-bottom: var(--space-lg);
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

    .doc-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
        margin-bottom: var(--space-lg);
    }

    .doc-item {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: var(--space-md);
        position: relative;
    }

    .doc-item.has-file {
        border-color: var(--primary);
    }

    .doc-item-header {
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        margin-bottom: var(--space-sm);
    }

    .doc-item-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--bg-input);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .doc-item.has-file .doc-item-num {
        background: rgba(34, 197, 94, 0.12);
        color: var(--primary);
    }

    .doc-item-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .doc-upload-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 40px;
        border: 1px dashed var(--border-input);
        border-radius: var(--radius-sm);
        background: transparent;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-family: inherit;
        cursor: pointer;
        transition: all var(--transition-fast);
        position: relative;
        overflow: hidden;
    }

    .doc-upload-btn:hover {
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .doc-upload-btn input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .doc-upload-btn svg {
        width: 16px;
        height: 16px;
    }

    .doc-file-info {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: rgba(34, 197, 94, 0.06);
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 500;
    }

    .doc-file-info.show {
        display: flex;
    }

    .doc-file-info svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    .doc-file-info span {
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

    .doc-hint {
        font-size: 0.6875rem;
        color: var(--text-muted);
        text-align: center;
        margin-bottom: var(--space-lg);
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
        <p>Загрузите 3 документа для подтверждения</p>
    </div>

    <!-- Upload form -->
    <form class="animate-in animate-in-delay-2" method="POST" action="{{ route('register.process', 'verification') }}" enctype="multipart/form-data">
        @csrf

        <div class="doc-list">
            <!-- 1. Диплом -->
            <div class="doc-item" id="docItem1">
                <div class="doc-item-header">
                    <div class="doc-item-num">1</div>
                    <div class="doc-item-title">Копия диплома</div>
                </div>
                <div class="doc-upload-btn" id="uploadBtn1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Выбрать файл</span>
                    <input type="file" name="doc_diploma" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFile(this, 1)">
                </div>
                <div class="doc-file-info" id="fileInfo1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="fileName1"></span>
                </div>
                @error('doc_diploma')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 2. Удостоверение личности -->
            <div class="doc-item" id="docItem2">
                <div class="doc-item-header">
                    <div class="doc-item-num">2</div>
                    <div class="doc-item-title">Удостоверение личности</div>
                </div>
                <div class="doc-upload-btn" id="uploadBtn2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Выбрать файл</span>
                    <input type="file" name="doc_id" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFile(this, 2)">
                </div>
                <div class="doc-file-info" id="fileInfo2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="fileName2"></span>
                </div>
                @error('doc_id')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. Выписка из ПФ -->
            <div class="doc-item" id="docItem3">
                <div class="doc-item-header">
                    <div class="doc-item-num">3</div>
                    <div class="doc-item-title">Выписка из пенсионного фонда</div>
                </div>
                <div class="doc-upload-btn" id="uploadBtn3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Выбрать файл</span>
                    <input type="file" name="doc_pension" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFile(this, 3)">
                </div>
                <div class="doc-file-info" id="fileInfo3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="fileName3"></span>
                </div>
                @error('doc_pension')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="doc-hint">JPG, PNG, PDF — до 10 МБ каждый</div>

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
    function handleFile(input, num) {
        const file = input.files[0];
        if (file) {
            document.getElementById('docItem' + num).classList.add('has-file');
            document.getElementById('uploadBtn' + num).style.display = 'none';
            document.getElementById('fileInfo' + num).classList.add('show');
            document.getElementById('fileName' + num).textContent = file.name;
        }
    }
</script>
@endpush
