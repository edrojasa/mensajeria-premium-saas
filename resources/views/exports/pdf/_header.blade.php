@props(['title', 'generatedAt'])
<div style="margin-bottom: 14px; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: middle;">
                @if (brand_logo_can_embed_in_pdf())
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(brand_logo_public_path())) }}" alt="{{ config('app.name') }}" style="max-height: 36px; width: auto; display: block;" />
                @else
                    <div style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ config('app.name') }}</div>
                @endif
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div style="font-size: 13px; font-weight: 700; color: #334155;">{{ $title }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 4px;">{{ __('exports.generated_at') }}: {{ $generatedAt->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
</div>
