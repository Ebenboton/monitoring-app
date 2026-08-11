@if(session('success') || session('error') || session('warning'))
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;min-width:320px;">
    @if(session('success'))
    <div id="toast" style="
        background:#fff;
        border-left:4px solid #22c55e;
        border-radius:8px;
        box-shadow:0 4px 20px rgba(0,0,0,0.12);
        padding:16px 20px;
        display:flex;
        align-items:center;
        gap:12px;
        animation: slideIn 0.3s ease;
    ">
        <span style="font-size:20px;">✅</span>
        <div>
            <div style="font-weight:600;color:#166534;font-size:14px;">Succès</div>
            <div style="color:#374151;font-size:13px;margin-top:2px;">{{ session('success') }}</div>
        </div>
        <button onclick="document.getElementById('toast').remove()"
            style="margin-left:auto;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;">×</button>
    </div>
    @endif

    @if(session('error'))
    <div id="toast" style="
        background:#fff;
        border-left:4px solid #ef4444;
        border-radius:8px;
        box-shadow:0 4px 20px rgba(0,0,0,0.12);
        padding:16px 20px;
        display:flex;
        align-items:center;
        gap:12px;
    ">
        <span style="font-size:20px;">❌</span>
        <div>
            <div style="font-weight:600;color:#991b1b;font-size:14px;">Erreur</div>
            <div style="color:#374151;font-size:13px;margin-top:2px;">{{ session('error') }}</div>
        </div>
        <button onclick="document.getElementById('toast').remove()"
            style="margin-left:auto;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;">×</button>
    </div>
    @endif

    @if(session('warning'))
    <div id="toast" style="
        background:#fff;
        border-left:4px solid #f59e0b;
        border-radius:8px;
        box-shadow:0 4px 20px rgba(0,0,0,0.12);
        padding:16px 20px;
        display:flex;
        align-items:center;
        gap:12px;
    ">
        <span style="font-size:20px;">⚠️</span>
        <div>
            <div style="font-weight:600;color:#92400e;font-size:14px;">Attention</div>
            <div style="color:#374151;font-size:13px;margin-top:2px;">{{ session('warning') }}</div>
        </div>
        <button onclick="document.getElementById('toast').remove()"
            style="margin-left:auto;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;">×</button>
    </div>
    @endif
</div>

<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
</style>

<script>
    setTimeout(() => {
        const t = document.getElementById('toast');
        if (t) t.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => { if (t) t.remove(); }, 300);
    }, 4000);
</script>
@endif