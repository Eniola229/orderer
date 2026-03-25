<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdCategory;
use App\Models\AdBannerSlot;
use App\Models\Product;
use App\Models\ServiceListing;
use App\Models\HouseListing;
use App\Services\CloudinaryService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinary,
        protected WalletService $wallet
    ) {}

    public function index()
    {
        $ads = Ad::with(['adCategory', 'bannerSlot'])
            ->where('seller_id', auth('seller')->id())
            ->latest()
            ->paginate(15);

        return view('seller.ads.index', compact('ads'));
    }

    public function create()
    {
        $seller     = auth('seller')->user();
        $categories = AdCategory::where('is_active', true)->get();
        $slots      = AdBannerSlot::where('is_active', true)->get();

        // Get seller's approved listings for promotion
        $products  = Product::where('seller_id', $seller->id)
                            ->where('status', 'approved')
                            ->get();
        $services  = ServiceListing::where('seller_id', $seller->id)
                                   ->where('status', 'approved')
                                   ->get();
        $houses    = HouseListing::where('seller_id', $seller->id)
                                 ->where('status', 'approved')
                                 ->get();

        return view('seller.ads.create', compact(
            'seller', 'categories', 'slots', 'products', 'services', 'houses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'ad_category_id'     => ['required', 'exists:ad_categories,id'],
            'ad_banner_slot_id'  => ['nullable', 'exists:ad_banner_slots,id'],
            'promotable_type'    => ['required', 'in:product,service,house,brand'],
            'promotable_id'      => ['required', 'string'],
            'budget'             => ['required', 'numeric', 'min:1'],
            'start_date'         => ['required', 'date', 'after_or_equal:today'],
            'end_date'           => ['required', 'date', 'after:start_date'],
            'media'              => ['nullable', 'file', 'max:51200'],
            'click_url'          => ['nullable', 'url'],
        ]);

        $seller = auth('seller')->user();

        // Check ads balance
        $adCategory = AdCategory::findOrFail($request->ad_category_id);

        // Calculate cost
        $days      = now()->parse($request->start_date)->diffInDays($request->end_date) + 1;
        $slot      = $request->ad_banner_slot_id
            ? AdBannerSlot::find($request->ad_banner_slot_id)
            : null;
        $costPerDay = $slot ? $slot->price_per_day : 5.00;
        $totalCost  = $costPerDay * $days;

        if ($request->budget < $totalCost) {
            return back()->withErrors(['budget' => "Minimum budget for {$days} days is \${$totalCost}"]);
        }

        if ($seller->ads_balance < $request->budget) {
            return back()->withErrors(['budget' => 'Insufficient ads balance. Please top up first.']);
        }

        // Map promotable type
        $promotableClass = match($request->promotable_type) {
            'product' => 'App\Models\Product',
            'service' => 'App\Models\ServiceListing',
            'house'   => 'App\Models\HouseListing',
            default   => 'App\Models\Seller',
        };

        // Upload media
        $mediaUrl  = null;
        $publicId  = null;
        $mediaType = 'image';

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mime = $file->getMimeType();

            if (str_contains($mime, 'video')) {
                $uploaded  = $this->cloudinary->uploadVideo($file, 'orderer/ads/ads_videos');
                $mediaType = 'video';
            } else {
                $uploaded  = $this->cloudinary->uploadImage($file, 'orderer/ads/ads_images');
                $mediaType = 'image';
            }

            $mediaUrl = $uploaded['url'];
            $publicId = $uploaded['public_id'];
        }

        Ad::create([
            'seller_id'          => $seller->id,
            'ad_category_id'     => $request->ad_category_id,
            'ad_banner_slot_id'  => $request->ad_banner_slot_id,
            'promotable_type'    => $promotableClass,
            'promotable_id'      => $request->promotable_id,
            'title'              => $request->title,
            'media_url'          => $mediaUrl,
            'cloudinary_public_id'=> $publicId,
            'media_type'         => $mediaType,
            'click_url'          => $request->click_url,
            'budget'             => $request->budget,
            'cost_per_day'       => $costPerDay,
            'start_date'         => $request->start_date,
            'end_date'           => $request->end_date,
            'status'             => 'pending',
        ]);

        return redirect()->route('seller.ads.index')
            ->with('success', 'Ad submitted for review. We will notify you once approved.');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->seller_id !== auth('seller')->id()) abort(403);

        // Refund unspent budget if ad was approved/active
        if (in_array($ad->status, ['approved', 'active', 'paused'])) {
            $unspent = $ad->budget - $ad->amount_spent;
            if ($unspent > 0) {
                $this->wallet->topupAdsBalance(auth('seller')->user(), $unspent);
            }
        }

        if ($ad->cloudinary_public_id) {
            $this->cloudinary->delete(
                $ad->cloudinary_public_id,
                $ad->media_type === 'video' ? 'video' : 'image'
            );
        }

        $ad->delete();

        return redirect()->route('seller.ads.index')
            ->with('success', 'Ad deleted. Unspent budget refunded to ads balance.');
    }

    public function pause(Ad $ad)
    {
        if ($ad->seller_id !== auth('seller')->id()) abort(403);
        if ($ad->status !== 'active') return back()->with('error', 'Only active ads can be paused.');
        $ad->update(['status' => 'paused']);
        return back()->with('success', 'Ad paused.');
    }

    public function resume(Ad $ad)
    {
        if ($ad->seller_id !== auth('seller')->id()) abort(403);
        if ($ad->status !== 'paused') return back()->with('error', 'Only paused ads can be resumed.');

        $seller = auth('seller')->user();
        if ($seller->ads_balance <= 0) {
            return back()->with('error', 'Insufficient ads balance to resume. Please top up.');
        }

        $ad->update(['status' => 'active']);
        return back()->with('success', 'Ad resumed.');
    }
}