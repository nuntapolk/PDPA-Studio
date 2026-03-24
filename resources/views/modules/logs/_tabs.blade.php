@php
$tabs = [
    ['route'=>'logs.index',         'label'=>'📋 Audit Log',       'active'=>request()->routeIs('logs.index')],
    ['route'=>'logs.operation',     'label'=>'⚡ Operation Log',    'active'=>request()->routeIs('logs.operation')],
    ['route'=>'logs.security',      'label'=>'🔐 Security Log',     'active'=>request()->routeIs('logs.security')],
    ['route'=>'logs.data-access',   'label'=>'📊 Data Access',      'active'=>request()->routeIs('logs.data-access')],
    ['route'=>'logs.consent-events','label'=>'✅ Consent Events',   'active'=>request()->routeIs('logs.consent-events')],
    ['route'=>'logs.errors',        'label'=>'🚨 System Errors',    'active'=>request()->routeIs('logs.errors')],
];
@endphp
<div class="flex gap-1 mb-6 overflow-x-auto pb-1" style="border-bottom:2px solid #e2e8f0;">
    @foreach($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2 text-sm font-medium rounded-t-lg whitespace-nowrap transition"
           style="{{ $tab['active']
               ? 'background:#15572e;color:#fff;border:1px solid #15572e;border-bottom:2px solid #fff;margin-bottom:-2px;'
               : 'color:#64748b;border:1px solid transparent;' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
