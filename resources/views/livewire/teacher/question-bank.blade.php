{{-- resources/views/livewire/teacher/question-bank.blade.php --}}
{{-- Sesuai screenshot: label "Soal", input field, tombol Simpan dan Batal --}}
<div>

{{-- Header toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
    <div style="font-size:13px;color:#8E8E93;">{{ $this->questions->total() }} soal</div>
    <button
        wire:click="$set('showForm', true)"
        style="background:#C0392B;color:white;border:none;border-radius:8px;padding:8px 14px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;"
    >
        + Tambah
    </button>
</div>

{{-- ======================== MODAL TAMBAH/EDIT SOAL ======================== --}}
{{-- Sesuai screenshot: form dengan label Soal, input, tombol Simpan + Batal --}}
@if($showForm)
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:12px;">

    {{-- Form section --}}
    <div style="padding:16px;">

        {{-- Tipe soal --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:8px;">Tipe Soal</div>
            <div style="display:flex;gap:8px;">
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid {{ $questionType==='pg' ? '#C0392B' : '#E5E5EA' }};border-radius:8px;cursor:pointer;background:{{ $questionType==='pg' ? '#FDEDEC' : 'white' }};font-size:14px;font-weight:600;color:{{ $questionType==='pg' ? '#C0392B' : '#8E8E93' }};">
                    <input type="radio" wire:model="questionType" value="pg" style="accent-color:#C0392B;"> PG
                </label>
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid {{ $questionType==='essay' ? '#C0392B' : '#E5E5EA' }};border-radius:8px;cursor:pointer;background:{{ $questionType==='essay' ? '#FDEDEC' : 'white' }};font-size:14px;font-weight:600;color:{{ $questionType==='essay' ? '#C0392B' : '#8E8E93' }};">
                    <input type="radio" wire:model="questionType" value="essay" style="accent-color:#C0392B;"> Essay
                </label>
            </div>
        </div>

        {{-- Field Soal --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:8px;">Soal</div>
            <textarea
                wire:model="questionText"
                rows="3"
                placeholder="Tulis soal di sini..."
                style="width:100%;background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;outline:none;resize:none;font-family:inherit;line-height:1.5;-webkit-appearance:none;"
                onfocus="this.style.borderColor='#C0392B';this.style.background='white';"
                onblur="this.style.borderColor='#E5E5EA';this.style.background='#F2F2F7';"
            ></textarea>
        </div>

        {{-- Pilihan (PG) --}}
        @if($questionType === 'pg')
        @foreach(['A'=>'optionA','B'=>'optionB','C'=>'optionC','D'=>'optionD','E'=>'optionE'] as $label => $field)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <div style="width:28px;height:28px;border-radius:50%;border:1.5px solid {{ $answerKey===$label ? '#C0392B' : '#8E8E93' }};background:{{ $answerKey===$label ? '#C0392B' : 'transparent' }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:{{ $answerKey===$label ? 'white' : '#8E8E93' }};flex-shrink:0;">{{ $label }}</div>
            <input
                type="text"
                wire:model="{{ $field }}"
                placeholder="Pilihan {{ $label }}"
                style="flex:1;background:#F2F2F7;border:0.5px solid {{ $answerKey===$label ? '#C0392B' : '#E5E5EA' }};border-radius:8px;padding:9px 12px;font-size:14px;color:#1C1C1E;outline:none;font-family:inherit;-webkit-appearance:none;"
            >
        </div>
        @endforeach

        {{-- Kunci jawaban --}}
        <div style="margin-top:14px;margin-bottom:14px;">
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:8px;">Kunci Jawaban</div>
            <div style="display:flex;gap:8px;">
                @foreach(['A','B','C','D','E'] as $opt)
                <button
                    type="button"
                    wire:click="$set('answerKey', '{{ $opt }}')"
                    style="width:38px;height:38px;border-radius:8px;border:1.5px solid {{ $answerKey===$opt ? '#C0392B' : '#E5E5EA' }};background:{{ $answerKey===$opt ? '#C0392B' : 'white' }};color:{{ $answerKey===$opt ? 'white' : '#8E8E93' }};font-weight:700;cursor:pointer;font-size:14px;font-family:inherit;"
                >{{ $opt }}</button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tingkat kesulitan --}}
        <div style="margin-bottom:6px;">
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:8px;">Kesulitan</div>
            <div style="display:flex;gap:8px;">
                @foreach(['mudah'=>'Mudah','sedang'=>'Sedang','sulit'=>'Sulit'] as $val => $lbl)
                <button
                    type="button"
                    wire:click="$set('difficulty', '{{ $val }}')"
                    style="padding:7px 14px;border-radius:8px;border:1px solid {{ $difficulty===$val ? '#C0392B' : '#E5E5EA' }};background:{{ $difficulty===$val ? '#FDEDEC' : 'white' }};color:{{ $difficulty===$val ? '#C0392B' : '#8E8E93' }};font-weight:600;cursor:pointer;font-size:13px;font-family:inherit;"
                >{{ $lbl }}</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tombol Simpan + Batal (sesuai screenshot) --}}
    <div style="display:flex;justify-content:flex-end;gap:10px;padding:12px 16px 16px;border-top:0.5px solid #E5E5EA;">
        <button
            wire:click="save"
            style="background:white;border:0.5px solid #E5E5EA;border-radius:8px;padding:9px 22px;font-size:15px;font-weight:500;color:#1C1C1E;cursor:pointer;font-family:inherit;"
            wire:loading.attr="disabled"
        >
            Simpan
        </button>
        <button
            wire:click="$set('showForm', false)"
            style="background:white;border:0.5px solid #E5E5EA;border-radius:8px;padding:9px 22px;font-size:15px;font-weight:500;color:#1C1C1E;cursor:pointer;font-family:inherit;"
        >
            Batal
        </button>
    </div>
</div>
@endif

{{-- ======================== DAFTAR SOAL ======================== --}}
@php $questions = $this->questions; @endphp

@if($questions->isEmpty() && !$showForm)
<div style="background:white;border-radius:12px;padding:48px 16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
    <div style="font-size:32px;margin-bottom:10px;">📝</div>
    <div style="font-size:14px;color:#8E8E93;font-weight:500;">Belum ada soal. Klik "Tambah" untuk mulai.</div>
</div>
@else
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    @foreach($questions as $i => $q)
    <div style="padding:14px 16px;border-bottom:0.5px solid #E5E5EA;">
        <div style="display:flex;align-items:start;gap:10px;">
            <div style="font-size:12px;font-weight:700;color:#8E8E93;min-width:24px;padding-top:2px;">{{ $questions->firstItem() + $i }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;color:#1C1C1E;line-height:1.4;margin-bottom:6px;">{{ Str::limit($q->question, 80) }}</div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="background:{{ $q->type==='pg' ? '#EBF5FB' : '#FDEDEC' }};color:{{ $q->type==='pg' ? '#1A5276' : '#C0392B' }};padding:2px 8px;border-radius:100px;font-size:11px;font-weight:700;">{{ strtoupper($q->type) }}</span>
                    @if($q->answer_key)
                    <span style="font-size:12px;color:#8E8E93;">Kunci: <strong style="color:#C0392B;">{{ $q->answer_key }}</strong></span>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0;">
                <button wire:click="edit({{ $q->id }})" style="background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;padding:5px 10px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">Edit</button>
                <button wire:click="delete({{ $q->id }})" wire:confirm="Hapus soal ini?" style="background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;padding:5px 10px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">Hapus</button>
            </div>
        </div>
    </div>
    @endforeach

    @if($questions->hasPages())
    <div style="padding:12px 16px;">{{ $questions->links() }}</div>
    @endif
</div>
@endif

</div>