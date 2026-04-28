<footer class="ord-footer">
    <div class="ord-footer-top">
        <div class="container">
            <div class="row">

                {{-- Col 1: About --}}
                <div class="col-12 col-md-4 col-lg-4 mb-40">
                    <a href="{{ route('home') }}" class="ord-footer-logo">
                        <img src="{{ asset('img/core-img/logo2.png') }}" alt="Orderer">
                    </a>
                    <p class="ord-footer-about">
                        Orderer is an all-in-one marketplace — buy, sell, and deliver anything, anywhere.
                        From everyday essentials and branded goods to real estate, services, and last-mile rider
                        bookings, Orderer brings commerce to your fingertips. Built for individuals and businesses
                        across the globe.
                    </p>
                    <p class="ord-footer-parent">
                        A product of <strong>AfricGEM International Company Limited</strong>
                    </p>
                    <div class="ord-footer-social">
                        <a href="#" title="Facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" title="Instagram"><i class="fa fa-instagram"></i></a>
                        <a href="#" title="Twitter / X"><i class="fa fa-twitter"></i></a>
                        <a href="#" title="Pinterest"><i class="fa fa-pinterest"></i></a>
                        <a href="#" title="YouTube"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>

                {{-- Col 2: Explore --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Explore</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('shop.index') }}">Shop</a></li>
                        <li><a href="{{ route('brands.index') }}">Brands</a></li>
                        <li><a href="{{ route('services.index') }}">Services</a></li>
                        <li><a href="{{ route('houses.index') }}">Properties</a></li>
                        <li><a href="{{ route('rider.booking') }}">Book a Rider</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Col 3: Sellers --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Sellers</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('seller.register') }}">Start Selling</a></li>
                        <li><a href="{{ route('seller.login') }}">Seller Login</a></li>
                        <li><a href="#">Seller Guide</a></li>
                        <li><a href="{{ route('legal.seller-terms') }}">Seller Terms</a></li>
                        <li><a href="#">Fees &amp; Commission</a></li>
                    </ul>
                </div>

                {{-- Col 4: Support --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Support</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('buyer.orders') }}">Order Status</a></li>
                        <li><a href="{{ route('buyer.wallet') }}">Payment Options</a></li>
                        <li><a href="{{ route('legal.shipping') }}">Shipping &amp; Delivery</a></li>
                        <li><a href="{{ route('legal.refund') }}">Refund Policy</a></li>
                        <li><a href="{{ route('buyer.support') }}">Help Center</a></li>
                    </ul>
                </div>

                {{-- Col 5: Legal --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Legal</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('legal.terms') }}">Terms of Use</a></li>
                        <li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.buyer-terms') }}">Buyer Terms</a></li>
                        <li><a href="{{ route('legal.seller-terms') }}">Seller Terms</a></li>
                        <li><a href="{{ route('legal.cookies') }}">Cookie Policy</a></li>
                        <li><a href="{{ route('legal.aml') }}">AML Policy</a></li>
                        <li><a href="{{ route('legal.acceptable-use') }}">Acceptable Use</a></li>
                        <li><a href="{{ route('legal.disclaimer') }}">Disclaimer</a></li>
                    </ul>
                </div>

            </div>

            {{-- Newsletter — full width below columns --}}
            <div class="row">
                <div class="col-12 col-md-6 col-lg-5 mb-40">
                    <h6 class="ord-footer-heading">Newsletter</h6>
                    <p class="ord-footer-newsletter-text">Get deals, new arrivals and updates straight to your inbox.</p>
                    <form class="ord-footer-newsletter" action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="ord-footer-bottom">
        <div class="container">
            <div class="ord-footer-bottom-inner">
                <p>&copy; {{ date('Y') }} Orderer — <span>AfricGEM International Company Limited</span>. All rights reserved.</p>
                <div class="ord-footer-bottom-links">
                    <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
                    <a href="{{ route('legal.terms') }}">Terms of Use</a>
                    <a href="{{ route('legal.cookies') }}">Cookies</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUKH9vjgY18mb7sumK4RQByrUuV3jjJTg&libraries=places"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle all newsletter forms in footer with AJAX
    const newsletterForms = document.querySelectorAll('.ord-footer-newsletter');
    
    newsletterForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            const emailInput = this.querySelector('input[name="email"]');
            const originalEmail = emailInput.value;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            
            // Remove any existing message
            let msgDiv = this.parentNode.querySelector('.newsletter-message');
            if (msgDiv) msgDiv.remove();
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                if (response.redirected) {
                    // Success - redirect means success
                    emailInput.value = '';
                    showFooterToast('Thank you for subscribing!', 'success');
                } else {
                    const data = await response.json();
                    
                    if (response.ok) {
                        emailInput.value = '';
                        showFooterToast(data.message || 'You\'re on the list!', 'success');
                    } else {
                        const errorMsg = data.errors?.email?.[0] || data.message || 'Something went wrong';
                        showFooterToast(errorMsg, 'error');
                    }
                }
            } catch (err) {
                showFooterToast('Network error. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        });
    });
    
    function showFooterToast(message, type = 'success') {
        // Create toast element if doesn't exist
        let toast = document.getElementById('footer-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'footer-toast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#2ECC71' : '#E74C3C'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 9999;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            document.body.appendChild(toast);
        }
        
        toast.style.background = type === 'success' ? '#2ECC71' : '#E74C3C';
        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
        }, 4000);
    }
});
</script>