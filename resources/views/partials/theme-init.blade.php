{{-- Apply theme before paint; keep <html class="dark"> in sync with storage / OS; expose finToggleTheme. --}}
<script>
(function () {
    function notifyTheme() {
        try {
            window.dispatchEvent(new CustomEvent('fin-theme-changed'));
        } catch (e) {}
    }
    function applyFromStorage() {
        try {
            var t = localStorage.getItem('theme');
            var dark = t === 'dark' || (t !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        } catch (e) {}
        notifyTheme();
    }
    applyFromStorage();
    try {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
            if (localStorage.getItem('theme') === 'light' || localStorage.getItem('theme') === 'dark') {
                return;
            }
            applyFromStorage();
        });
    } catch (e) {}
    window.finToggleTheme = function () {
        var root = document.documentElement;
        var nextDark = !root.classList.contains('dark');
        root.classList.toggle('dark', nextDark);
        try {
            localStorage.setItem('theme', nextDark ? 'dark' : 'light');
        } catch (e) {}
        notifyTheme();
    };
    window.finApplyTheme = applyFromStorage;
})();
</script>
