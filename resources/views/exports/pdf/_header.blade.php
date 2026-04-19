@props(['title', 'generatedAt'])
<div style="margin-bottom: 16px; border-bottom: 2px solid #1e3a5f; padding-bottom: 12px;">
    <div style="font-size: 18px; font-weight: 700; color: #0f172a;">{{ config('app.name') }}</div>
    <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 4px;">{{ $title }}</div>
    <div style="font-size: 11px; color: #64748b; margin-top: 6px;">{{ __('exports.generated_at') }}: {{ $generatedAt->format('d/m/Y H:i') }}</div>
</div>
