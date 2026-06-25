@props(['route', 'label', 'icon' => 'circle'])

@php
    $active = request()->routeIs($route);
@endphp

<a href="{{ route($route) }}"
   style="{{ $active
       ? 'background:#1d4ed8;color:#ffffff;'
       : 'color:#94a3b8;' }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group"
   onmouseover="{{ $active ? '' : "this.style.background='rgba(255,255,255,0.06)';this.style.color='#e2e8f0';" }}"
   onmouseout="{{ $active ? '' : "this.style.background='transparent';this.style.color='#94a3b8';" }}">

    <i class="ti ti-{{ $icon }} flex-shrink-0" style="font-size:17px;width:18px;"></i>

    <span class="flex-1">{{ $label }}</span>

    @if($active)
        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:rgba(255,255,255,0.7);"></span>
    @endif
</a>