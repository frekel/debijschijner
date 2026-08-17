@if($successMessage !== '')
    <div style="margin:16px 0;padding:12px 16px;border:1px solid #2e7d32;background:#e8f5e9;color:#1b5e20;border-radius:4px;">{{ $successMessage }}</div>
@endif

@if(count($errorMessages) > 0)
    <div style="margin:16px 0;padding:12px 16px;border:1px solid #c62828;background:#ffebee;color:#b71c1c;border-radius:4px;">
        <strong>{{ $errorTitle }}</strong>
        <ul style="margin:8px 0 0 20px;">
            @foreach($errorMessages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
