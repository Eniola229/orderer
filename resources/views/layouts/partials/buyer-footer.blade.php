<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                    <span>Copyright &copy;</span>
                    <script>document.write(new Date().getFullYear());</script>
                    <span class="ms-1">Orderer. All rights reserved.</span>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3 mt-md-0">
                    <a href="{{ route('home') }}"
                       class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-shopping-bag me-1" style="font-size:12px;"></i>
                        Shop
                    </a>
                    <span class="text-muted">|</span>
                    <a href="#" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-file-text me-1" style="font-size:12px;"></i>
                        Terms
                    </a>
                    <span class="text-muted">|</span>
                    <a href="#" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-rotate-ccw me-1" style="font-size:12px;"></i>
                        Refund Policy
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('buyer.support') }}"
                       class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-headphones me-1" style="font-size:12px;"></i>
                        Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="support-float-btn" id="supportBtn">
    <i class="feather-headphones"></i>
    <span>Support</span>
</div>

<div class="support-modal" id="supportModal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h5>Contact Support</h5>
            <button class="support-close" id="closeModal">&times;</button>
        </div>
        <div class="support-modal-body">
            <p class="text-muted mb-4">How would you like to reach us?</p>
            <div class="support-options">
                <a href="{{ route('buyer.support') }}" class="support-option">
                    <div class="support-option-icon">
                        <i class="feather-message-circle"></i>
                    </div>
                    <div class="support-option-content">
                        <h6>Open a Ticket</h6>
                        <p>Submit a support ticket</p>
                    </div>
                </a>
                <a href="https://wa.me/YOUR_WHATSAPP_NUMBER" target="_blank" class="support-option">
                    <div class="support-option-icon whatsapp">
                        <i class="feather-message-square"></i>
                    </div>
                    <div class="support-option-content">
                        <h6>WhatsApp</h6>
                        <p>Chat with us on WhatsApp</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme-customizer-init.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supportBtn   = document.getElementById('supportBtn');
    const supportModal = document.getElementById('supportModal');
    const closeModal   = document.getElementById('closeModal');
    supportBtn.addEventListener('click',  () => supportModal.classList.add('active'));
    closeModal.addEventListener('click',  () => supportModal.classList.remove('active'));
    supportModal.addEventListener('click', e => {
        if (e.target === supportModal) supportModal.classList.remove('active');
    });
});
</script>

@stack('scripts')
</body>
</html>