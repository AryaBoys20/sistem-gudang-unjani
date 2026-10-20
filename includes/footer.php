</div>

<footer>
    <strong>&copy; <?= date('Y') ?> WMS UNJANI</strong> — Sistem Informasi Manajemen Pergudangan
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ═══════════════════════════════════════════════════════════════
// WMS UNJANI — GLOBAL JAVASCRIPT (FIXED)
// ═══════════════════════════════════════════════════════════════

(function() {
    'use strict';

    // Cleanup modal backdrop
    window.cleanupModal = function() {
        document.querySelectorAll('.modal-backdrop').forEach(function(bd) {
            bd.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    };

    // Auto notif dismiss
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
        
        // Auto show modal
        var autoModal = document.querySelector('[data-auto-show="true"]');
        if (autoModal) {
            cleanupModal();
            var modal = new bootstrap.Modal(autoModal);
            modal.show();
        }
    });

    // Cleanup backdrop sebelum modal buka
    document.addEventListener('show.bs.modal', function() {
        cleanupModal();
    });

    // Cleanup setelah modal tutup
    document.addEventListener('hidden.bs.modal', function() {
        setTimeout(function() {
            var openModals = document.querySelectorAll('.modal.show');
            if (openModals.length === 0) {
                cleanupModal();
            }
        }, 100);
    });

    // Submit form biasa (TANPA loading spinner supaya tidak stuck)
    document.addEventListener('submit', function(e) {
        // Validasi form sederhana
        var form = e.target;
        var required = form.querySelectorAll('[required]');
        var valid = true;
        
        required.forEach(function(field) {
            if (!field.value.trim()) {
                field.style.borderColor = '#ef4444';
                valid = false;
            } else {
                field.style.borderColor = '';
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (bertanda *)');
            return false;
        }
    });

})();
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