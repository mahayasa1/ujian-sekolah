{{-- resources/views/livewire/teacher/question-bank.blade.php --}}
<div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:1rem;">
    {{-- Search --}}
    <div style="position:relative;flex:1;min-width:200px;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari soal..."
            style="width:100%;padding:0.6rem 0.75rem 0.6rem 2.25rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;outline:none;font-family:inherit;"
            onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
    </div>

    {{-- Filter type --}}
    <select wire:model.live="type"
        style="padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
        <option value="">Semua Tipe</option>
        <option value="pg">Pilihan Ganda</option>
        <option value="essay">Essay</option>
    </select>

    <button class="btn-digi-primary" wire:click="$set('showForm', true)">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Soal
    </button>
</div>

{{-- Add/Edit Form Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

        {{-- Modal header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span style="font-size:1rem;">✏️</span>
                <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Soal' : 'Tambah Soal Baru' }}
                </h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;line-height:1;padding:0.25rem;">✕</button>
        </div>

        <div style="padding:1.5rem;">

            {{-- Question type --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Tipe Soal</label>
                <div style="display:flex;gap:0.5rem;">
                    <label style="display:flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;border:2px solid {{ $questionType==='pg' ? '#C0392B' : '#E5E7EB' }};border-radius:0.5rem;cursor:pointer;background:{{ $questionType==='pg' ? '#FDEDEC' : 'white' }};font-size:0.85rem;font-weight:600;color:{{ $questionType==='pg' ? '#C0392B' : '#6B7280' }};">
                        <input type="radio" wire:model="questionType" value="pg" style="accent-color:#C0392B;">
                        Pilihan Ganda
                    </label>
                    <label style="display:flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;border:2px solid {{ $questionType==='essay' ? '#C0392B' : '#E5E7EB' }};border-radius:0.5rem;cursor:pointer;background:{{ $questionType==='essay' ? '#FDEDEC' : 'white' }};font-size:0.85rem;font-weight:600;color:{{ $questionType==='essay' ? '#C0392B' : '#6B7280' }};">
                        <input type="radio" wire:model="questionType" value="essay" style="accent-color:#C0392B;">
                        Essay
                    </label>
                </div>
            </div>

            {{-- Question text --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Pertanyaan <span style="color:#C0392B;">*</span></label>
                <textarea wire:model="questionText" rows="3" placeholder="Tulis pertanyaan di sini..."
                    style="width:100%;padding:0.75rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;resize:vertical;font-family:inherit;outline:none;line-height:1.5;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'"></textarea>
            </div>

            {{-- Options (PG only) --}}
            @if($questionType === 'pg')
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.5rem;">Pilihan Jawaban</label>
                @foreach(['A'=>'optionA','B'=>'optionB','C'=>'optionC','D'=>'optionD','E'=>'optionE'] as $label => $field)
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                    <span style="width:28px;height:28px;border-radius:50%;background:{{ $answerKey===$label ? '#C0392B' : '#F3F4F6' }};color:{{ $answerKey===$label ? 'white' : '#6B7280' }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;flex-shrink:0;">{{ $label }}</span>
                    <input type="text" wire:model="{{ $field }}" placeholder="Pilihan {{ $label }}"
                        style="flex:1;padding:0.55rem 0.75rem;border:1.5px solid {{ $answerKey===$label ? '#C0392B' : '#E5E7EB' }};border-radius:0.5rem;font-size:0.85rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='{{ $answerKey===$label ? '#C0392B' : '#E5E7EB' }}'">
                </div>
                @endforeach
            </div>

            {{-- Answer key --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Kunci Jawaban <span style="color:#C0392B;">*</span></label>
                <div style="display:flex;gap:0.5rem;">
                    @foreach(['A','B','C','D','E'] as $opt)
                    <button type="button"
                        wire:click="$set('answerKey', '{{ $opt }}')"
                        style="width:40px;height:40px;border-radius:0.5rem;border:2px solid {{ $answerKey===$opt ? '#C0392B' : '#E5E7EB' }};background:{{ $answerKey===$opt ? '#C0392B' : 'white' }};color:{{ $answerKey===$opt ? 'white' : '#6B7280' }};font-weight:700;cursor:pointer;font-size:0.875rem;transition:all 0.15s;">
                        {{ $opt }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Difficulty --}}
            <div style="margin-bottom:1.5rem;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Tingkat Kesulitan</label>
                <div style="display:flex;gap:0.5rem;">
                    @foreach(['mudah'=>'🟢 Mudah','sedang'=>'🟡 Sedang','sulit'=>'🔴 Sulit'] as $val => $lbl)
                    <button type="button"
                        wire:click="$set('difficulty', '{{ $val }}')"
                        style="padding:0.45rem 0.875rem;border-radius:0.5rem;border:2px solid {{ $difficulty===$val ? '#C0392B' : '#E5E7EB' }};background:{{ $difficulty===$val ? '#FDEDEC' : 'white' }};color:{{ $difficulty===$val ? '#C0392B' : '#6B7280' }};font-weight:600;cursor:pointer;font-size:0.8rem;">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;border-top:1px solid #F3F4F6;padding-top:1rem;">
                <button wire:click="$set('showForm', false)" class="btn-digi-outline">Batal</button>
                <button wire:click="save" class="btn-digi-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>💾 Simpan Soal</span>
                    <span wire:loading>⏳ Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Questions Table --}}
@php $questions = $this->questions; @endphp

<div class="digi-card" style="padding:0;overflow:hidden;">
    <div style="padding:0.875rem 1.25rem;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:0.875rem;font-weight:600;color:#374151;">
            📝 {{ $questions->total() }} soal ditemukan
        </span>
    </div>

    @if($questions->isEmpty())
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
        <p style="margin:0;font-weight:500;">Belum ada soal. Klik "Tambah Soal" untuk mulai.</p>
    </div>
    @else
    <table class="digi-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Pertanyaan</th>
                <th style="width:100px;">Tipe</th>
                <th style="width:100px;">Kesulitan</th>
                <th style="width:90px;">Kunci</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $i => $q)
            <tr>
                <td style="color:#9CA3AF;font-size:0.8rem;">{{ $questions->firstItem() + $i }}</td>
                <td>
                    <p style="margin:0;font-size:0.85rem;color:#1F2937;line-height:1.4;">
                        {{ Str::limit($q->question, 100) }}
                    </p>
                </td>
                <td>
                    <span style="background:{{ $q->type==='pg' ? '#EBF5FB' : '#F9EBEA' }};color:{{ $q->type==='pg' ? '#1A5276' : '#922B21' }};padding:0.2rem 0.6rem;border-radius:999px;font-size:0.72rem;font-weight:600;">
                        {{ $q->type === 'pg' ? 'PG' : 'Essay' }}
                    </span>
                </td>
                <td>
                    @php
                        $dColors = ['mudah'=>['#D5F5E3','#1E8449'],'sedang'=>['#FFFBEB','#92400E'],'sulit'=>['#FDEDEC','#922B21']];
                        [$bg,$fc] = $dColors[$q->difficulty] ?? ['#F3F4F6','#6B7280'];
                    @endphp
                    <span style="background:{{ $bg }};color:{{ $fc }};padding:0.2rem 0.6rem;border-radius:999px;font-size:0.72rem;font-weight:600;">
                        {{ ucfirst($q->difficulty) }}
                    </span>
                </td>
                <td>
                    <strong style="color:#C0392B;font-size:0.875rem;">{{ $q->answer_key ?? '—' }}</strong>
                </td>
                <td>
                    <div style="display:flex;gap:0.4rem;">
                        <button wire:click="edit({{ $q->id }})"
                            style="padding:0.3rem 0.6rem;background:#EBF5FB;color:#1A5276;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Edit
                        </button>
                        <button wire:click="delete({{ $q->id }})"
                            wire:confirm="Yakin hapus soal ini?"
                            style="padding:0.3rem 0.6rem;background:#FDEDEC;color:#C0392B;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($questions->hasPages())
    <div style="padding:0.875rem 1.25rem;border-top:1px solid #F3F4F6;">
        {{ $questions->links() }}
    </div>
    @endif
    @endif
</div>

</div>
