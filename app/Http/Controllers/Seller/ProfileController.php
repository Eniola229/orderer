<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index()
    {
        return view('seller.profile.index');
    }

    public function update(Request $request)
    {
        $seller = auth('seller')->user();

        $request->validate([
            'first_name'           => ['required', 'string', 'max:100'],
            'last_name'            => ['required', 'string', 'max:100'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'business_name'        => ['required', 'string', 'max:200'],
            'business_description' => ['nullable', 'string'],
            'business_address'     => ['nullable', 'string'],
            'address_code'     => ['nullable', 'string'],
            'avatar'               => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'current_password'     => ['nullable', 'string'],
            'password'             => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'phone',
            'business_name', 'business_description', 'business_address', 'address_code'
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($seller->avatar) {
                // Note: extract public_id from URL if stored separately
            }
            $uploaded = $this->cloudinary->uploadImage(
                $request->file('avatar'),
                'orderer/avatars/sellers'
            );
            $data['avatar'] = $uploaded['url'];
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $seller->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = $request->password;
        }

        $seller->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}