<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.querySelector('#sidebarMenu');
        if (sidebarToggle) {
            const sidebarCollapse = new bootstrap.Collapse(sidebarToggle, {
                toggle: false
            });
        }
    });
</script>
</body>
</html>
