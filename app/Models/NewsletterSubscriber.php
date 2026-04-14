<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NewsletterSubscriber extends Model
{
    use HasUuids;

    protected $table = 'newsletter_subscribers';

    protected $fillable = ['email', 'subscribed_at'];

    protected $casts = [
        'subscribed_at' => 'datetime',
    ];
}