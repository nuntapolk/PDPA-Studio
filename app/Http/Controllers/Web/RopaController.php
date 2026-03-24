<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RopaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RopaController extends Controller
{
    public function index(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $query = RopaRecord::where('organization_id', $orgId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('process_name', 'like', '%' . $request->search . '%')
                  ->orWhere('process_code', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;
        $records = $query->latest()->paginate($perPage)->withQueryString();

        $totalCount       = RopaRecord::where('organization_id', $orgId)->count();
        $activeCount      = RopaRecord::where('organization_id', $orgId)->where('status', 'active')->count();
        $needsReviewCount = RopaRecord::where('organization_id', $orgId)
            ->whereNotNull('next_review_date')
            ->where('next_review_date', '<', now())
            ->where('status', '!=', 'archived')
            ->count();
        $sensitiveCount   = RopaRecord::where('organization_id', $orgId)->where('has_sensitive_data', true)->count();

        $departments = RopaRecord::where('organization_id', $orgId)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('modules.ropa.index', compact(
            'records', 'totalCount', 'activeCount', 'needsReviewCount', 'sensitiveCount', 'departments'
        ));
    }

    public function create()
    {
        return view('modules.ropa.create');
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'process_name'    => 'required|string|max:255',
            'process_code'    => 'nullable|string|max:50',
            'department'      => 'nullable|string|max:255',
            'process_owner'   => 'nullable|string|max:255',
            'role'            => 'required|in:controller,processor,joint_controller',
            'purpose'         => 'required|string',
            'legal_basis'     => 'required|string',
            'legitimate_interest_description' => 'nullable|string',
            'data_categories'    => 'nullable|array',
            'data_subject_types' => 'nullable|array',
            'has_sensitive_data' => 'nullable|boolean',
            'sensitive_data_categories' => 'nullable|array',
            'recipients'         => 'nullable|array',
            'third_party_transfer'  => 'nullable|boolean',
            'cross_border_transfer' => 'nullable|boolean',
            'cross_border_countries'  => 'nullable|string',
            'cross_border_safeguards' => 'nullable|string',
            'retention_period'  => 'required|string|max:255',
            'retention_criteria' => 'nullable|string',
            'deletion_method'   => 'nullable|string',
            'security_measures' => 'nullable|array',
            'system_used'       => 'nullable|string',
            'next_review_date'  => 'nullable|date',
        ]);

        $record = RopaRecord::create(array_merge($validated, [
            'organization_id' => $orgId,
            'status'          => 'draft',
            'created_by'      => Auth::id(),
            'has_sensitive_data' => $request->boolean('has_sensitive_data'),
            'third_party_transfer' => $request->boolean('third_party_transfer'),
            'cross_border_transfer' => $request->boolean('cross_border_transfer'),
        ]));

        AuditLog::record('created', 'ropa', $record);

        $code = $record->process_code ?: $record->id;
        return redirect()->route('ropa.show', $record)->with('success', "สร้าง ROPA Record เรียบร้อย — {$code}");
    }

    public function show(RopaRecord $ropa)
    {
        $this->authorizeOrg($ropa->organization_id);
        AuditLog::record('viewed', 'ropa', $ropa);
        return view('modules.ropa.show', compact('ropa'));
    }

    public function edit(RopaRecord $ropa)
    {
        $this->authorizeOrg($ropa->organization_id);
        return view('modules.ropa.edit', compact('ropa'));
    }

    public function update(Request $request, RopaRecord $ropa)
    {
        $this->authorizeOrg($ropa->organization_id);

        $validated = $request->validate([
            'process_name'    => 'required|string|max:255',
            'process_code'    => 'nullable|string|max:50',
            'department'      => 'nullable|string|max:255',
            'process_owner'   => 'nullable|string|max:255',
            'role'            => 'required|in:controller,processor,joint_controller',
            'purpose'         => 'required|string',
            'legal_basis'     => 'required|string',
            'legitimate_interest_description' => 'nullable|string',
            'data_categories'    => 'nullable|array',
            'data_subject_types' => 'nullable|array',
            'has_sensitive_data' => 'nullable|boolean',
            'sensitive_data_categories' => 'nullable|array',
            'recipients'         => 'nullable|array',
            'third_party_transfer'  => 'nullable|boolean',
            'cross_border_transfer' => 'nullable|boolean',
            'cross_border_countries'  => 'nullable|string',
            'cross_border_safeguards' => 'nullable|string',
            'retention_period'  => 'required|string|max:255',
            'retention_criteria' => 'nullable|string',
            'deletion_method'   => 'nullable|string',
            'security_measures' => 'nullable|array',
            'system_used'       => 'nullable|string',
            'status'            => 'required|in:draft,active,under_review,archived',
            'next_review_date'  => 'nullable|date',
        ]);

        $ropa->update(array_merge($validated, [
            'has_sensitive_data' => $request->boolean('has_sensitive_data'),
            'third_party_transfer' => $request->boolean('third_party_transfer'),
            'cross_border_transfer' => $request->boolean('cross_border_transfer'),
        ]));

        AuditLog::record('updated', 'ropa', $ropa);

        return redirect()->route('ropa.show', $ropa)->with('success', 'อัปเดต ROPA Record เรียบร้อย');
    }

    public function markReviewed(RopaRecord $ropa)
    {
        $this->authorizeOrg($ropa->organization_id);

        $ropa->update([
            'last_reviewed_at' => now(),
            'next_review_date' => now()->addYear(),
            'reviewed_by'      => Auth::id(),
            'status'           => 'active',
        ]);

        AuditLog::record('reviewed', 'ropa', $ropa);

        return back()->with('success', 'บันทึกการ Review เรียบร้อย — กำหนด Review ครั้งถัดไป: ' . now()->addYear()->format('d/m/Y'));
    }

    public function export()
    {
        $orgId = Auth::user()->organization_id;
        $records = RopaRecord::where('organization_id', $orgId)
            ->where('status', '!=', 'archived')
            ->orderBy('department')
            ->orderBy('process_name')
            ->get();

        AuditLog::record('exported', 'ropa');

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'รหัส', 'ชื่อกิจกรรม', 'แผนก', 'เจ้าของกิจกรรม', 'บทบาท',
                'วัตถุประสงค์', 'ฐานกฎหมาย', 'ประเภทข้อมูล', 'ประเภทเจ้าของข้อมูล',
                'ข้อมูลอ่อนไหว', 'ผู้รับข้อมูล', 'ส่งต่างประเทศ',
                'ระยะเวลาเก็บ', 'มาตรการความปลอดภัย', 'สถานะ', 'Review ล่าสุด', 'Review ครั้งถัดไป',
            ]);
            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->process_code,
                    $r->process_name,
                    $r->department,
                    $r->process_owner,
                    match($r->role) { 'controller' => 'Controller', 'processor' => 'Processor', 'joint_controller' => 'Joint Controller', default => $r->role },
                    $r->purpose,
                    $r->getLegalBasisLabel(),
                    implode(', ', $r->data_categories ?? []),
                    implode(', ', $r->data_subject_types ?? []),
                    $r->has_sensitive_data ? 'ใช่' : 'ไม่',
                    implode(', ', $r->recipients ?? []),
                    $r->cross_border_transfer ? ($r->cross_border_countries ?? 'ใช่') : 'ไม่',
                    $r->retention_period,
                    implode(', ', $r->security_measures ?? []),
                    match($r->status) { 'draft' => 'ร่าง', 'active' => 'ใช้งาน', 'under_review' => 'กำลัง Review', 'archived' => 'เก็บถาวร', default => $r->status },
                    $r->last_reviewed_at?->format('d/m/Y') ?? '',
                    $r->next_review_date?->format('d/m/Y') ?? '',
                ]);
            }
            fclose($handle);
        }, 'ropa-export-' . now()->format('Ymd') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) {
            abort(403);
        }
    }
}
