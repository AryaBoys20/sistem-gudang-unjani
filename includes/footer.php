</div>

<footer>
    <strong>&copy; <?= date('Y') ?> WMS UNJANI</strong> — Sistem Informasi Manajemen Pergudangan
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto-scroll + notif auto-hilang
document.addEventListener('DOMContentLoaded', function() {
    var alert = document.getElementById('notifAlert');
    if (alert) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s, transform 0.5s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function() { alert.remove(); }, 500);
        }, 4000);
    }
    
    var urlParams = new URLSearchParams(window.location.search);
    var highlightId = urlParams.get('highlight');
    if (highlightId) {
        var links = document.querySelectorAll('a[href*="edit=' + highlightId + '"]');
        links.forEach(function(link) {
            var row = link.closest('tr');
            if (row) {
                row.style.animation = 'highlightFlash 2s ease-out';
                setTimeout(function() {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    }
});

// Loading spinner
document.addEventListener('submit', function(e) {
    var btn = e.target.querySelector('button[type="submit"]');
    if (btn && !btn.classList.contains('no-loading')) {
        btn.disabled = true;
        var original = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Proses...';
    }
});
</script>

<style>
@keyframes highlightFlash {
    0% { background: #fef3c7 !important; }
    50% { background: #fde68a !important; }
    100% { background: transparent; }
}
</style>
</body>
</html>