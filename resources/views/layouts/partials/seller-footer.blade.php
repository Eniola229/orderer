
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
                    <a href="{{ route('home') }}" target="_blank"
                       class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-external-link me-1" style="font-size:12px;"></i>Storefront
                    </a>
                    <span class="text-muted">|</span>
                    <a href="#" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-file-text me-1" style="font-size:12px;"></i>Terms
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('seller.support') }}"
                       class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-headphones me-1" style="font-size:12px;"></i>Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Support float button --}}
<div class="support-float-btn" id="supportBtn">
    <i class="feather-headphones"></i>
    <span>Support</span>
</div>

{{-- Support modal --}}
<div class="support-modal" id="supportModal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h5>Contact Support</h5>
            <button class="support-close" id="closeModal">&times;</button>
        </div>
        <div class="support-modal-body">
            <p class="text-muted mb-4">How would you like to reach us?</p>
            <div class="support-options">
                <a href="{{ route('seller.support') }}" class="support-option">
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

{{-- Share copied toast --}}
<div id="shareCopiedToast"
     style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1a1a1a;color:#fff;padding:10px 20px;border-radius:100px;font-size:13px;font-weight:500;z-index:9999;">
    <i class="feather-check me-2"></i> Link copied!
</div>

{{-- JS --}}
<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme-customizer-init.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const supportBtn   = document.getElementById('supportBtn');
    const supportModal = document.getElementById('supportModal');
    const closeModal   = document.getElementById('closeModal');

    if (supportBtn) {
        supportBtn.addEventListener('click',  () => supportModal.classList.add('active'));
        closeModal.addEventListener('click',  () => supportModal.classList.remove('active'));
        supportModal.addEventListener('click', e => {
            if (e.target === supportModal) supportModal.classList.remove('active');
        });
    }
});

// Global share helpers — usable anywhere in the dashboard
function shareTo(platform, url, title) {
    url   = url   || window.location.href;
    title = title || document.title;
    const u = encodeURIComponent(url);
    const t = encodeURIComponent(title);
    const targets = {
        facebook : `https://www.facebook.com/sharer/sharer.php?u=${u}`,
        twitter  : `https://twitter.com/intent/tweet?url=${u}&text=${t}`,
        whatsapp : `https://wa.me/?text=${t}%20${u}`,
        telegram : `https://t.me/share/url?url=${u}&text=${t}`,
        linkedin : `https://www.linkedin.com/shareArticle?mini=true&url=${u}&title=${t}`,
    };
    if (targets[platform]) {
        window.open(targets[platform], '_blank', 'width=600,height=450,noopener,noreferrer');
    }
}

function copyShareLink(url) {
    url = url || window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        const toast = document.getElementById('shareCopiedToast');
        if (!toast) return;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 2500);
    });
}
</script>

@stack('scripts')
</body>
</html>