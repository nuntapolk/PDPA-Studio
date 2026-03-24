<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PrivacyNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PrivacyNoticeController extends Controller
{
    private static array $types = [
        'privacy_policy', 'cookie_policy', 'employee_notice',
        'cctv_notice', 'marketing_notice', 'third_party_notice',
    ];

    // ── Index ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = PrivacyNotice::where('organization_id', $orgId);

        if ($request->filled('type'))     $query->where('type', $request->type);
        if ($request->filled('language')) $query->where('language', $request->language);
        if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');
        if ($request->filled('status')) {
            match($request->status) {
                'draft'     => $query->whereNull('published_at'),
                'published' => $query->whereNotNull('published_at')->where('is_active', true),
                'inactive'  => $query->whereNotNull('published_at')->where('is_active', false),
                'expired'   => $query->whereNotNull('expires_at')->where('expires_at', '<', now()),
                default     => null,
            };
        }

        $notices = $query->latest()->paginate($perPage)->withQueryString();

        $base           = PrivacyNotice::where('organization_id', $orgId);
        $totalCount     = (clone $base)->count();
        $publishedCount = (clone $base)->published()->count();
        $draftCount     = (clone $base)->draft()->count();
        $expiringSoon   = (clone $base)->expiringSoon()->count();

        return view('modules.privacy.index', compact(
            'notices', 'totalCount', 'publishedCount', 'draftCount', 'expiringSoon'
        ));
    }

    // ── Create ─────────────────────────────────────────────────────────────
    public function create()
    {
        $types = self::$types;
        return view('modules.privacy.create', compact('types'));
    }

    // ── Store ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'type'           => 'required|in:'.implode(',', self::$types),
            'title'          => 'required|string|max:255',
            'language'       => 'required|in:th,en',
            'version'        => 'required|integer|min:1',
            'content'        => 'required|string',
            'effective_date' => 'nullable|string|max:100',
            'expires_at'     => 'nullable|date',
        ]);

        $notice = PrivacyNotice::create([
            ...$validated,
            'organization_id' => $orgId,
            'is_active'       => false,
            'created_by'      => Auth::id(),
            'public_url'      => Str::random(32),
        ]);

        AuditLog::record('created', 'privacy_notice', $notice);

        return redirect()->route('privacy.show', $notice)
                         ->with('success', 'สร้าง Privacy Notice สำเร็จ');
    }

    // ── Show ───────────────────────────────────────────────────────────────
    public function show(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);

        // Version history — same type & language
        $history = PrivacyNotice::where('organization_id', $notice->organization_id)
            ->where('type', $notice->type)
            ->where('language', $notice->language)
            ->orderBy('version', 'desc')
            ->get();

        AuditLog::record('viewed', 'privacy_notice', $notice);

        return view('modules.privacy.show', compact('notice', 'history'));
    }

    // ── Edit ───────────────────────────────────────────────────────────────
    public function edit(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);
        $types = self::$types;
        return view('modules.privacy.edit', compact('notice', 'types'));
    }

    // ── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'language'       => 'required|in:th,en',
            'version'        => 'required|integer|min:1',
            'content'        => 'required|string',
            'effective_date' => 'nullable|string|max:100',
            'expires_at'     => 'nullable|date',
        ]);

        $notice->update($validated);
        AuditLog::record('updated', 'privacy_notice', $notice);

        return redirect()->route('privacy.show', $notice)
                         ->with('success', 'บันทึกการแก้ไขสำเร็จ');
    }

    // ── Publish ────────────────────────────────────────────────────────────
    public function publish(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);

        $notice->update([
            'is_active'    => true,
            'published_at' => $notice->published_at ?? now(),
            'approved_by'  => Auth::id(),
        ]);

        AuditLog::record('published', 'privacy_notice', $notice);

        return back()->with('success', 'เผยแพร่ Privacy Notice สำเร็จ');
    }

    // ── Unpublish ──────────────────────────────────────────────────────────
    public function unpublish(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);
        $notice->update(['is_active' => false]);
        AuditLog::record('unpublished', 'privacy_notice', $notice);
        return back()->with('success', 'ปิดการเผยแพร่สำเร็จ');
    }

    // ── New Version ────────────────────────────────────────────────────────
    public function newVersion(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);

        $new = $notice->replicate();
        $new->version    = $notice->version + 1;
        $new->is_active  = false;
        $new->published_at = null;
        $new->approved_by  = null;
        $new->public_url   = Str::random(32);
        $new->created_by   = Auth::id();
        $new->save();

        return redirect()->route('privacy.edit', $new)
                         ->with('success', 'สร้างเวอร์ชันใหม่ v'.$new->version.' สำเร็จ กรุณาแก้ไขเนื้อหา');
    }

    // ── Destroy ────────────────────────────────────────────────────────────
    public function destroy(PrivacyNotice $notice)
    {
        $this->authorizeOrg($notice->organization_id);
        AuditLog::record('deleted', 'privacy_notice', $notice);
        $notice->delete();
        return redirect()->route('privacy.index')
                         ->with('success', 'ลบ Privacy Notice สำเร็จ');
    }

    // ── Public Preview ─────────────────────────────────────────────────────
    public function publicView(string $token)
    {
        $notice = PrivacyNotice::where('public_url', $token)
                               ->where('is_active', true)
                               ->firstOrFail();
        return view('modules.privacy.public', compact('notice'));
    }
}
