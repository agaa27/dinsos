        </div> <!-- Close main-content -->
    </div> <!-- Close main-container -->

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Dinas Sosial & Pemberdayaan Masyarakat Kota Tarakan</p>
            <p>Jl. Jenderal Sudirman No. 10, Kota Tarakan, Kalimantan Utara</p>
            <p>Email: dinsos@tarakankota.go.id | Telp: (0551) 123456</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        // JavaScript untuk dropdown menu
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('active');
                    
                    // Tutup dropdown lainnya
                    dropdownToggles.forEach(otherToggle => {
                        if (otherToggle !== this) {
                            otherToggle.parentElement.classList.remove('active');
                        }
                    });
                });
            });
            
            // Tutup dropdown saat klik di luar
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-item.dropdown')) {
                    dropdownToggles.forEach(toggle => {
                        toggle.parentElement.classList.remove('active');
                    });
                }
            });
        });
    </script>
</body>
</html>