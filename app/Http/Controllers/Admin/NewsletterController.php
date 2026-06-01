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
    // ── Index ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $this->authorise();

        $newsletters = Newsletter::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.newsletter.index', compact('newsletters'));
    }

    // ── Subscribers (AJAX) ────────────────────────────────────────────────────
    public function subscribers(Request $request)
    {
        try {
            $query = NewsletterSubscriber::query();

            if ($request->filled('date_from')) {
                $query->whereDate('subscribed_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('subscribed_at', '<=', $request->date_to);
            }

            $perPage     = $request->get('per_page', 100);
            $subscribers = $query->orderBy('subscribed_at', 'desc')->paginate($perPage);

            $formatted = $subscribers->getCollection()->map(fn($s) => [
                'email'         => $s->email,
                'subscribed_at' => $s->subscribed_at
                    ? $s->subscribed_at->format('M d, Y')
                    : 'N/A',
            ]);

            return response()->json([
                'success'      => true,
                'subscribers'  => $formatted,
                'total'        => $subscribers->total(),
                'current_page' => $subscribers->currentPage(),
                'last_page'    => $subscribers->lastPage(),
                'per_page'     => $subscribers->perPage(),
                'has_more_pages' => $subscribers->hasMorePages(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching subscribers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => 'Failed to fetch subscribers',
            ], 500);
        }
    }

    // ── Create form ───────────────────────────────────────────────────────────
    public function create()
    {
        $this->authorise();

        $newsletter = null; // so _form partial doesn't error on $newsletter->x

        return view('admin.newsletter.create', compact('newsletter'));
    }

    // ── Store (save as draft) ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorise();

        $data = $request->validate($this->validationRules($request));

        $newsletter = Newsletter::create([
            'subject'           => $data['subject'],
            'body'              => $data['body'],
            'audience'          => $data['audience'],
            'send_sms'          => $request->boolean('send_sms'),
            'sms_message'       => $data['sms_message'] ?? null,
            'sms_audience'      => $data['sms_audience'] ?? null,
            'sms_extra_numbers' => $this->cleanExtraNumbers($data['sms_extra_numbers'] ?? []),
            'status'            => Newsletter::STATUS_DRAFT,
            'created_by'        => Auth::guard('admin')->id(),
        ]);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter saved as draft.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Newsletter $newsletter)
    {
        $this->authorise();

        return view('admin.newsletter.show', compact('newsletter'));
    }

    // ── Edit form ─────────────────────────────────────────────────────────────
    public function edit(Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be edited.');

        return view('admin.newsletter.edit', compact('newsletter'));
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be edited.');

        $data = $request->validate($this->validationRules($request));

        $newsletter->update([
            'subject'           => $data['subject'],
            'body'              => $data['body'],
            'audience'          => $data['audience'],
            'send_sms'          => $request->boolean('send_sms'),
            'sms_message'       => $data['sms_message'] ?? null,
            'sms_audience'      => $data['sms_audience'] ?? null,
            'sms_extra_numbers' => $this->cleanExtraNumbers($data['sms_extra_numbers'] ?? []),
        ]);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter updated.');
    }

    // ── Send (dispatch job) ───────────────────────────────────────────────────
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

    // ── Delete (draft only) ───────────────────────────────────────────────────
    public function destroy(Newsletter $newsletter)
    {
        $this->authorise();

        abort_unless($newsletter->isDraft(), 403, 'Only draft newsletters can be deleted.');

        $newsletter->delete();

        return redirect()
            ->route('admin.newsletter.index')
            ->with('success', 'Newsletter deleted.');
    }

    // ── Shared validation rules ───────────────────────────────────────────────
    private function validationRules(Request $request): array
    {
        $sendSms = $request->boolean('send_sms');

        return [
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
            'audience' => 'required|in:buyers,sellers,both,guests,new_buyers,buyers_no_orders,buyers_with_orders,sellers_no_listings',

            
            'send_sms'              => 'boolean',
            'sms_message'           => $sendSms ? 'required|string|max:320' : 'nullable|string|max:320',
            'sms_audience'          => $sendSms ? 'required|in:users,sellers,both' : 'nullable|in:users,sellers,both',
            'sms_extra_numbers'     => 'nullable|array',
            'sms_extra_numbers.*'   => 'nullable|string|max:15',
        ];
    }

    // ── Strip empty extra numbers ─────────────────────────────────────────────
    private function cleanExtraNumbers(array $numbers): array
    {
        return array_values(array_filter(array_map('trim', $numbers)));
    }

    // ── Guard helper ──────────────────────────────────────────────────────────
    private function authorise(): void
    {
        abort_unless(
            Auth::guard('admin')->user()->canManageNewsletter(),
            403,
            'You do not have permission to manage newsletters.'
        );
    }
}