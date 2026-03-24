<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยื่นคำขอสิทธิ์ — {{ $org->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen py-10 px-4">

<div class="max-w-lg mx-auto">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900">ยื่นคำขอสิทธิ์เจ้าของข้อมูล</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $org->name }}</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-4 mb-6 text-sm">
        <p class="font-semibold mb-0.5">ส่งคำขอสำเร็จ!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 mb-6 text-sm">
        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('rights.submit-public', $org->slug) }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="requester_name" value="{{ old('requester_name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                    <input type="email" name="requester_email" value="{{ old('requester_email') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">โทรศัพท์</label>
                    <input type="tel" name="requester_phone" value="{{ old('requester_phone') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ประเภทคำขอ <span class="text-red-500">*</span></label>
                <select name="request_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">เลือกประเภทคำขอ</option>
                    <option value="access" {{ old('request_type') === 'access' ? 'selected' : '' }}>ขอเข้าถึงข้อมูลส่วนบุคคล</option>
                    <option value="rectification" {{ old('request_type') === 'rectification' ? 'selected' : '' }}>ขอแก้ไขข้อมูลให้ถูกต้อง</option>
                    <option value="erasure" {{ old('request_type') === 'erasure' ? 'selected' : '' }}>ขอลบ/ทำลายข้อมูล</option>
                    <option value="restriction" {{ old('request_type') === 'restriction' ? 'selected' : '' }}>ขอระงับการใช้ข้อมูล</option>
                    <option value="portability" {{ old('request_type') === 'portability' ? 'selected' : '' }}>ขอโอนย้ายข้อมูล</option>
                    <option value="objection" {{ old('request_type') === 'objection' ? 'selected' : '' }}>คัดค้านการประมวลผลข้อมูล</option>
                    <option value="withdraw_consent" {{ old('request_type') === 'withdraw_consent' ? 'selected' : '' }}>ถอนความยินยอม</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดคำขอ <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="กรุณาระบุรายละเอียดคำขอของท่าน...">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition text-sm">
                ส่งคำขอ
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        คำขอจะได้รับการดำเนินการภายใน 30 วันนับจากวันที่ได้รับ ตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562
    </p>
</div>
</body>
</html>
