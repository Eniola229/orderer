<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(
            new Configuration([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME')),
                    'api_key'    => config('cloudinary.api_key',    env('CLOUDINARY_API_KEY')),
                    'api_secret' => config('cloudinary.api_secret', env('CLOUDINARY_API_SECRET')),
                ],
                'url' => [
                    'secure' => true,
                ],
            ])
        );
    }

    public function uploadImage(UploadedFile $file, string $folder = 'orderer/products'): array
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'         => $folder,
            'resource_type'  => 'image',
            'transformation' => [
                'quality'      => 'auto',
                'fetch_format' => 'auto',
            ],
        ]);

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function uploadVideo(UploadedFile $file, string $folder = 'orderer/videos'): array
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'        => $folder,
            'resource_type' => 'video',
        ]);

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function uploadDocument(UploadedFile $file, string $folder = 'orderer/documents'): array
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'        => $folder,
            'resource_type' => 'raw',
        ]);

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}