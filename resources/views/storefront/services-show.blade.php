@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center">
            <h2>{{ $service->title }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($service->title, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div></div></div>
</div>

<style>
    .service-gallery {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .main-image:hover {
        transform: scale(1.02);
    }
    .thumbnail-list {
        display: flex;
        gap: 12px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .thumbnail {
        width: 80px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    .thumbnail.active {
        border-color: #2ECC71;
        transform: scale(1.05);
    }
    .thumbnail:hover {
        border-color: #2ECC71;
    }
    .info-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #eee;
    }
    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #212529;
    }
    .feature-badge {
        background: #f8f9fa;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        margin: 0 5px 5px 0;
        display: inline-block;
    }
    .share-btn {
        cursor: pointer;
        transition: all 0.3s;
    }
    .share-btn:hover {
        transform: translateY(-2px);
    }
    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    .contact-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .whatsapp-btn {
        background: #25D366;
        color: white;
        padding: 12px;
        border-radius: 50px;
        display: inline-block;
        width: 100%;
        transition: all 0.3s;
    }
    .whatsapp-btn:hover {
        background: #128C7E;
        color: white;
        transform: translateY(-2px);
    }
    .rating-stars {
        color: #FFA500;
        font-size: 14px;
    }
</style>

<section class="section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                {{-- Image Gallery --}}
                <div class="service-gallery mb-4">
                    @if($service->portfolio_images && count($service->portfolio_images) > 0)
                        <img id="mainImage" src="{{ $service->portfolio_images[0]['url'] }}"
                             class="main-image" alt="{{ $service->title }}" onclick="openImageModal(this.src)">
                        @if(count($service->portfolio_images) > 1)
                            <div class="thumbnail-list">
                                @foreach($service->portfolio_images as $index => $image)
                                    <img src="{{ $image['url'] }}"
                                         class="thumbnail {{ $index == 0 ? 'active' : '' }}"
                                         onclick="changeMainImage(this)"
                                         data-image="{{ $image['url'] }}">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="main-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-cogs" style="font-size: 80px; color: #dee2e6;"></i>
                        </div>
                    @endif
                </div>

                {{-- Service Details --}}
                <div class="info-section">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div>
                            <h3 class="mb-2">{{ $service->title }}</h3>
                            <div class="d-flex align-items-center gap-3 text-muted flex-wrap">
                                <span><i class="fa fa-tag mr-1" style="color:#2ECC71;"></i> {{ $service->category->name ?? 'Uncategorized' }}</span>
                                <span><i class="fa fa-calendar mr-1"></i> Posted {{ $service->created_at->format('M d, Y') }}</span>
                                @if($service->average_rating)
                                    <span class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($service->average_rating))
                                                <i class="fa fa-star"></i>
                                            @elseif($i - 0.5 <= $service->average_rating)
                                                <i class="fa fa-star-half-o"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                        ({{ $service->total_reviews ?? 0 }} reviews)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="badge" style="background: #2ECC71; color: white; padding: 8px 16px; border-radius: 20px;">
                                {{ ucfirst($service->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Pricing</div>
                            <div class="info-value fs-3 fw-bold" style="color:#2ECC71;">
                                @if($service->pricing_type === 'negotiable')
                                    Negotiable
                                @else
                                    ${{ number_format($service->price, 2) }}
                                    @if($service->pricing_type === 'hourly')
                                        <small class="fs-6">/hour</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Pricing Type</div>
                            <div class="info-value">{{ ucfirst($service->pricing_type) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Delivery Time</div>
                            <div class="info-value">
                                <i class="fa fa-clock-o mr-1"></i> {{ $service->delivery_time ?? 'Flexible' }}
                            </div>
                        </div>
                    </div>

                    @if($service->location)
                        <hr>
                        <div class="mb-3">
                            <div class="info-label">Service Location</div>
                            <div class="info-value">
                                <i class="fa fa-map-marker mr-1" style="color:#2ECC71;"></i> {{ $service->location }}
                            </div>
                        </div>
                    @endif

                    @if($service->description)
                        <hr>
                        <div class="mb-3">
                            <div class="info-label">Description</div>
                            <div class="info-value" style="line-height: 1.6;">{{ $service->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Share Section --}}
                <div class="info-section">
                    <h5 class="mb-3">Share This Service</h5>
                    <div class="d-flex gap-2 justify-content-between">
                        <button class="btn btn-outline-primary flex-fill share-btn" onclick="shareService('facebook')">
                            <i class="fa fa-facebook"></i> Facebook
                        </button>
                        <button class="btn btn-outline-info flex-fill share-btn" onclick="shareService('twitter')">
                            <i class="fa fa-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-outline-success flex-fill share-btn" onclick="shareService('whatsapp')">
                            <i class="fa fa-whatsapp"></i> WhatsApp
                        </button>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-secondary w-100 share-btn" onclick="copyToClipboard()">
                            <i class="fa fa-link mr-1"></i> Copy Link
                        </button>
                    </div>
                </div>

                {{-- Contact Service Provider --}}
                <div class="contact-card">
                    <i class="fa fa-user-circle" style="font-size: 48px; color: #2ECC71; margin-bottom: 15px;"></i>
                    <h5 class="mb-2">{{ $service->seller->business_name ?? $service->seller->name ?? 'Service Provider' }}</h5>
                    <p class="text-muted small mb-3">Member since {{ $service->seller->created_at->format('M Y') }}</p>
                    
                    @if($service->seller->email)
                        <div class="mb-2">
                            <i class="fa fa-envelope mr-2"></i> {{ $service->seller->email }}
                        </div>
                    @endif
                    @if($service->seller->phone)
                        <div class="mb-3">
                            <i class="fa fa-phone mr-2"></i> {{ $service->seller->phone }}
                        </div>
                    @endif
                    
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $service->seller->phone ?? '234800000000') }}?text=Hi,%20I'm%20interested%20in%20your%20service:%20{{ urlencode($service->title) }}"
                       target="_blank"
                       class="whatsapp-btn mb-2">
                        <i class="fa fa-whatsapp mr-2"></i> Contact on WhatsApp
                    </a>
                </div>

                {{-- Service Stats --}}
                <div class="info-section">
                    <h5 class="mb-3">Service Information</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service ID:</span>
                        <span class="fw-semibold">#{{ $service->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Date Added:</span>
                        <span>{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Last Updated:</span>
                        <span>{{ $service->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Views:</span>
                        <span>{{ $service->views ?? 0 }} views</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-end">
                <button type="button" class="btn btn-light mb-2" data-bs-dismiss="modal" style="border-radius: 50%;">
                    <i class="fa fa-times"></i>
                </button>
                <img id="modalImage" src="" class="modal-image w-100" alt="">
            </div>
        </div>
    </div>
</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<script>
function changeMainImage(element) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = element.dataset.image;
    
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    element.classList.add('active');
}

function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function shareService(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("{{ $service->title }}");
    
    let shareUrl;
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${title}%20${url}`;
            break;
    }
    
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    });
}

// Keyboard navigation for gallery
let currentIndex = 0;
const images = @json(array_column($service->portfolio_images ?? [], 'url'));

document.addEventListener('keydown', function(e) {
    if (document.getElementById('imageModal').classList.contains('show') && images.length > 0) {
        if (e.key === 'ArrowLeft') {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById('modalImage').src = images[currentIndex];
        } else if (e.key === 'ArrowRight') {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById('modalImage').src = images[currentIndex];
        }
    }
});
</script>
</body>
</html>