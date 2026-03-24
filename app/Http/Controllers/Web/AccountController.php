<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    private function ensureAdmin()
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้น');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $roles    = config('accounts.roles', []);
        $roleDefs = array_keys($roles) ?: ['admin','editor','dpo','reviewer','staff','auditor'];

        $query = User::query();

        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('name',  'like', '%'.$request->search.'%')
                  ->orWhere('email','like', '%'.$request->search.'%')
            );
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(30)->withQueryString();

        $stats = [];
        foreach ($roleDefs as $role) {
            $stats[$role] = User::where('role', $role)->count();
        }
        $stats['total']    = User::count();
        $stats['active']   = User::where('status', 'active')->count();
        $stats['inactive'] = User::where('status', 'inactive')->count();
        $stats['locked']   = User::where('status', 'locked')->count();

        return view('modules.settings.accounts.index', compact('users','stats','roles'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $roles = config('accounts.roles', []);
        return view('modules.settings.accounts.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $allowedRoles = array_keys(config('accounts.roles', []));
        if (empty($allowedRoles)) {
            $allowedRoles = ['admin','editor','dpo','reviewer','staff','auditor'];
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'role'     => ['required', Rule::in($allowedRoles)],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'phone'    => 'nullable|string|max:20',
            'status'   => 'required|in:active,inactive',
        ], [
            'email.unique'    => 'อีเมลนี้มีในระบบแล้ว',
            'password.min'    => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        ]);

        $user = User::create([
            'organization_id'   => Auth::user()->organization_id,
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'phone'             => $request->phone,
            'status'            => $validated['status'],
            'email_verified_at' => now(),
            'is_builtin'        => false,
        ]);

        AuditLog::record('created', 'user_account', $user);
        return redirect()->route('settings.accounts.index')
                         ->with('success', "สร้าง account '{$user->name}' สำเร็จ");
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        $roles = config('accounts.roles', []);
        return view('modules.settings.accounts.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $allowedRoles = array_keys(config('accounts.roles', []));
        if (empty($allowedRoles)) {
            $allowedRoles = ['admin','editor','dpo','reviewer','staff','auditor'];
        }

        $validated = $request->validate([
            'name'   => 'required|string|max:150',
            'email'  => ['required', 'email', Rule::unique('users','email')->ignore($user->id)],
            'role'   => ['required', Rule::in($allowedRoles)],
            'phone'  => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,locked',
        ], [
            'email.unique' => 'อีเมลนี้มีในระบบแล้ว',
        ]);

        // Prevent demoting yourself
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'ไม่สามารถเปลี่ยน role ตัวเองได้']);
        }

        $before = $user->only(['name','email','role','status']);
        $user->update($validated);
        AuditLog::record('updated', 'user_account', $user, $before, $user->only(array_keys($before)));

        return redirect()->route('settings.accounts.index')
                         ->with('success', "บันทึกข้อมูล '{$user->name}' สำเร็จ");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->ensureAdmin();

        $request->validate([
            'new_password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ], [
            'new_password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);
        AuditLog::record('password_reset', 'user_account', $user);

        return back()->with('success', "รีเซ็ตรหัสผ่านของ '{$user->name}' สำเร็จ");
    }

    public function toggleStatus(Request $request, User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['status' => 'ไม่สามารถเปลี่ยนสถานะตัวเองได้']);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);
        AuditLog::record('status_changed', 'user_account', $user, ['status' => $user->status], ['status' => $newStatus]);

        return back()->with('success', "เปลี่ยนสถานะ '{$user->name}' เป็น {$newStatus}");
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'ไม่สามารถลบ account ตัวเองได้']);
        }

        if ($user->is_builtin) {
            return back()->withErrors(['delete' => 'ไม่สามารถลบ Built-in account ได้ ให้เปลี่ยนสถานะเป็น Inactive แทน']);
        }

        AuditLog::record('deleted', 'user_account', $user);
        $user->delete();

        return redirect()->route('settings.accounts.index')
                         ->with('success', "ลบ account '{$user->name}' สำเร็จ");
    }
}
