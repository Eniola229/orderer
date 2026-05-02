@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Contact Us</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-4 mb-4">
                <div style="padding:30px;border:1px solid #eee;border-radius:12px;height:100%;">
                    <h5 style="font-weight:800;margin-bottom:24px;">Get in Touch</h5>
                    <div style="display:flex;gap:14px;margin-bottom:20px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#D5F5E3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa fa-envelope" style="color:#2ECC71;"></i>
                        </div>
                        <div>
                            <p style="margin:0;font-weight:600;font-size:14px;">Email</p>
                            <a href="mailto:support@ordererweb.com" style="color:#2ECC71;font-size:13px;">support@ordererweb.com</a>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;margin-bottom:20px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#D5F5E3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa fa-whatsapp" style="color:#2ECC71;"></i>
                        </div>
                        <div>
                            <p style="margin:0;font-weight:600;font-size:14px;">WhatsApp</p>
                            <a href="https://wa.me/08152880128" target="_blank" style="color:#2ECC71;font-size:13px;">+234 815 288 0128</a>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#D5F5E3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa fa-clock-o" style="color:#2ECC71;"></i>
                        </div>
                        <div>
                            <p style="margin:0;font-weight:600;font-size:14px;">Support Hours</p>
                            <p style="color:#888;font-size:13px;margin:0;">Mon – Fri: 8am – 8pm WAT<br>Sat – Sun: 10am – 6pm WAT</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div style="padding:30px;border:1px solid #eee;border-radius:12px;">
                    <h5 style="font-weight:800;margin-bottom:24px;">Send us a Message</h5>
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Your Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', auth('web')->user()->full_name ?? '') }}"
                                       placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', auth('web')->user()->email ?? '') }}"
                                       placeholder="john@example.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control"
                                   value="{{ old('subject') }}" placeholder="How can we help?" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control" rows="5"
                                      placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn essence-btn w-100" style="">
                            <i class="fa fa-send mr-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>
