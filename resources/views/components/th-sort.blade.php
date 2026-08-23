{{-- resources/views/components/th-sort.blade.php --}}
@props(['field', 'label', 'sortField', 'sortDirection', 'width' => null, 'align' => 'left'])

<th wire:click="sortBy('{{ $field }}')"
    style="text-align:{{ $align }};padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;cursor:pointer;user-select:none;{{ $width ? "width:{$width};" : '' }}">
    <span style="display:inline-flex;align-items:center;gap:4px;{{ $align === 'center' ? 'justify-content:center;' : '' }}">
        {{ $label }}
        <span style="font-size:9px;{{ $sortField === $field ? 'color:#C0392B;' : 'color:#D1D5DB;' }}">
            @if($sortField === $field)
                {{ $sortDirection === 'asc' ? '▲' : '▼' }}
            @else
                ⇅
            @endif
        </span>
    </span>
</th>