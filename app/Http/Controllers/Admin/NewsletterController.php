<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
    // ── Index ─────────────────────────────────────────────────
    public function index()
    {
        $this->authorise();

        $newsletters = Newsletter::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.newsletter.index', compact('newsletters'));
    }

    public function subscribers(Request $request)
    {
        try {
            $query = NewsletterSubscriber::query();
            
            // Apply date filters if provided
            if ($request->filled('date_from')) {
                $query->whereDate('subscribed_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('subscribed_at', '<=', $request->date_to);
            }
            
            // Paginate with 100 items per page
            $perPage = $request->get('per_page', 100);
            $subscribers = $query->orderBy('subscribed_at', 'desc')->paginate($perPage);
            
            // Format the data
            $formattedSubscribers = $subscribers->getCollection()->map(function ($subscriber) {
                return [
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->subscribed_at ? $subscriber->subscribed_at->format('M d, Y') : 'N/A'
                ];
            });
            
            return response()->json([
                'success' => true,
                'subscribers' => $formattedSubscribers,
                'total' => $subscribers->total(),
                'current_page' => $subscribers->currentPage(),
                'last_page' => $subscribers->lastPage(),
                'per_page' => $subscribers->perPage(),
                'has_more_pages' => $subscribers->hasMorePages()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching subscribers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch subscribers'
            ], 500);
        }
    }

    // ── Create form ───────────────────────────────────────────
    public function create()
    {
        $this->authorise();

        return view('admin.newsletter.create');
    }

    // ── Store (save as draft) ─────────────────────────────────
    public function store(Request $request)
    {
        $this->authorise();

        $data = $request->validate([
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
            'audience' => 'required|in:buyers,sellers,both,guests',
        ]);

        $newsletter = Newsletter::create([
            ...$data,
            'status'     => Newsletter::STATUS_DRAFT,
            'created_by' => Auth::guard('admin')->id(),
        ]);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter saved as draft.');
    }

    // ── Show ──────────────────────────────────────────────────
    public function show(Newsletter $newsletter)
    {
        $this->authorise();

        return view('admin.newsletter.show', compact('newsletter'));
    }

    // ── Edit ──────────────────────────────────────────────────
    public function edit(Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be edited.');

        return view('admin.newsletter.edit', compact('newsletter'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be edited.');

        $data = $request->validate([
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
            'audience' => 'required|in:buyers,sellers,both',
        ]);

        $newsletter->update($data);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter updated.');
    }

    // ── Send (dispatch job) ───────────────────────────────────
    public function send(Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be dispatched.');

        $newsletter->update(['status' => Newsletter::STATUS_QUEUED]);

        SendNewsletterJob::dispatch($newsletter->id);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter queued for sending. It will be dispatched in the background.');
    }

    // ── Delete (draft only) ───────────────────────────────────
    public function destroy(Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be deleted.');

        $newsletter->delete();

        return redirect()
            ->route('admin.newsletter.index')
            ->with('success', 'Newsletter deleted.');
    }

    // ── Guard helper ──────────────────────────────────────────
    private function authorise(): void
    {
        abort_unless(
            Auth::guard('admin')->user()->canManageNewsletter(),
            403,
            'You do not have permission to manage newsletters.'
        );
    }
}