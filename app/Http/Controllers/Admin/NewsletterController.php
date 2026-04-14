<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\Newsletter;
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