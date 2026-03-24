<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\ConsentEventLog;
use App\Models\DataAccessLog;
use App\Models\OperationLog;
use App\Models\SecurityLog;
use App\Models\SystemErrorLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = 1;
        $users = User::where('organization_id', $orgId)->get();
        $admin = $users->first();

        $this->seedOperationLogs($orgId, $users);
        $this->seedSecurityLogs($orgId, $users);
        $this->seedDataAccessLogs($orgId, $users);
        $this->seedConsentEventLogs($orgId, $admin);
        $this->seedSystemErrorLogs();

        $this->command->info(sprintf(
            'LogSeeder: %d operation, %d security, %d data_access, %d consent_events, %d errors',
            OperationLog::count(), SecurityLog::count(), DataAccessLog::count(),
            ConsentEventLog::count(), SystemErrorLog::count()
        ));
    }

    private function seedOperationLogs(int $orgId, $users): void
    {
        $routes = [
            ['GET','dashboard','Web\DashboardController@index',200],
            ['GET','ropa.index','Web\RopaController@index',200],
            ['GET','ropa.show','Web\RopaController@show',200],
            ['POST','ropa.store','Web\RopaController@store',302],
            ['GET','consent.index','Web\ConsentController@index',200],
            ['GET','assessment.index','Web\AssessmentController@index',200],
            ['GET','training.index','Web\TrainingController@index',200],
            ['POST','training.quiz.submit','Web\TrainingController@submitQuiz',302],
            ['GET','dpo.index','Web\DpoTaskController@index',200],
            ['GET','logs.index','Web\LogController@index',200],
            ['GET','rights.index','Web\RightsRequestController@index',200],
            ['GET','breach.index','Web\BreachController@index',200],
            ['GET','privacy.index','Web\PrivacyNoticeController@index',200],
            ['POST','login.post','Web\AuthController@login',302],
            ['GET','ropa.export','Web\RopaController@export',200],
            ['GET','assessment.show','Web\AssessmentController@show',200],
            ['PUT','ropa.update','Web\RopaController@update',302],
            ['DELETE','ropa.destroy','Web\RopaController@destroy',302],
            ['GET','training.report','Web\TrainingController@report',200],
            ['PATCH','rights.update-status','Web\RightsRequestController@updateStatus',302],
        ];

        $baseTime = now()->subDays(7);
        $records  = [];

        for ($i = 0; $i < 200; $i++) {
            $user   = $users->random();
            $route  = $routes[array_rand($routes)];
            $dur    = match($route[0]) {
                'POST','PUT','DELETE' => rand(80, 400),
                default              => rand(20, 800),
            };
            if (rand(1,10) === 1) $dur = rand(1000, 5000); // occasional slow

            $records[] = [
                'organization_id' => $orgId,
                'user_id'         => $user->id,
                'user_name'       => $user->name,
                'method'          => $route[0],
                'url'             => 'https://pdpa.local/'.$route[1],
                'route_name'      => $route[1],
                'route_action'    => $route[2],
                'status_code'     => $route[3],
                'duration_ms'     => $dur,
                'memory_mb'       => rand(20, 60),
                'request_size'    => rand(200, 5000),
                'response_size'   => rand(1000, 80000),
                'ip_address'      => '192.168.1.'.rand(1,50),
                'session_id'      => \Illuminate\Support\Str::random(32),
                'user_agent'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Chrome/'.rand(110,120),
                'created_at'      => $baseTime->copy()->addMinutes(rand(0, 7*24*60))->format('Y-m-d H:i:s'),
            ];
        }
        OperationLog::insert($records);
    }

    private function seedSecurityLogs(int $orgId, $users): void
    {
        $admin = $users->first();
        $events = [
            // Critical/High
            ['login_failed','high','Login ล้มเหลว 7 ครั้งสำหรับ admin@example.com',['attempted_email'=>'admin@example.com','fail_count'=>7],'critical'],
            ['brute_force_detected','critical','Brute force จาก IP 45.33.32.156',['ip'=>'45.33.32.156','attempts'=>25],'critical'],
            ['account_locked','high','บัญชี admin ถูกล็อกหลัง 10 ครั้งล้มเหลว',['user'=>'admin@example.com'],'high'],
            ['suspicious_ip','high','เข้าสู่ระบบจาก IP ต่างประเทศ',['country'=>'RU','ip'=>'185.220.101.1'],'high'],
            ['permission_denied','medium','พยายามเข้าถึงข้อมูลองค์กรอื่น',[],null],
            ['data_export','medium','Export ข้อมูล Consent 2,847 รายการ',['module'=>'consents','count'=>2847,'format'=>'csv'],null],
            // Medium
            ['login_failed','medium','Login ล้มเหลว — รหัสผ่านไม่ถูกต้อง',['attempted_email'=>'staff@company.com','fail_count'=>2],null],
            ['mfa_failed','medium','MFA verification ล้มเหลว',['user'=>$admin->name],null],
            ['session_expired','low','Session หมดอายุ',['user'=>$admin->name],null],
            ['login_success','low','เข้าสู่ระบบสำเร็จ',[],null],
            ['logout','low','ออกจากระบบ',[],null],
            ['mfa_enabled','low','เปิดใช้งาน MFA',['user'=>$admin->name],null],
            ['password_changed','medium','เปลี่ยนรหัสผ่าน',['user'=>$admin->name],null],
            ['token_created','low','สร้าง API Token ใหม่',['name'=>'Integration Key'],null],
            ['login_failed','medium','Login ล้มเหลวซ้ำ',['attempted_email'=>'unknown@hacker.com','fail_count'=>5],null],
        ];

        $baseTime = now()->subDays(14);
        foreach ($events as $i => [$eventType, $severity, $desc, $meta, $overrideSev]) {
            $user = $users->random();
            SecurityLog::create([
                'organization_id' => $orgId,
                'user_id'         => in_array($eventType,['login_failed','brute_force_detected','suspicious_ip']) ? null : $user->id,
                'user_name'       => $user->name,
                'event_type'      => $eventType,
                'severity'        => $overrideSev ?? $severity,
                'description'     => $desc,
                'ip_address'      => '192.168.1.'.rand(1,50),
                'user_agent'      => 'Mozilla/5.0 Chrome/120',
                'metadata'        => $meta ?: null,
                'is_resolved'     => in_array($i,[0,1,2]) ? false : (bool)rand(0,1),
                'created_at'      => $baseTime->copy()->addDays(rand(0,14))->addHours(rand(0,23))->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function seedDataAccessLogs(int $orgId, $users): void
    {
        $accessTypes = [
            ['read','personal','consents',1,'ดูรายละเอียด Consent','consent'],
            ['search','personal','consents',null,'ค้นหา Consent','consent',50],
            ['export','personal','ropa_records',null,'Export ROPA รายงาน','legitimate_interest',120],
            ['read','sensitive','data_subjects',1,'ดูข้อมูลผู้ร้องขอสิทธิ์','legal_obligation'],
            ['export','health','data_subjects',null,'Export สำหรับรายงาน','contract',35],
            ['read','financial','consents',1,'ตรวจสอบ Consent การชำระเงิน','consent'],
            ['bulk_export','personal','audit_logs',null,'Export Audit Log ประจำเดือน','legal_obligation',500],
            ['rights_request','personal','data_subjects',1,'ตอบคำขอสิทธิ์เข้าถึงข้อมูล','legal_obligation'],
            ['read','personal','training_completions',1,'ดูใบรับรองพนักงาน','contract'],
            ['search','personal','data_subjects',null,'ค้นหาเจ้าของข้อมูล','legitimate_interest',20],
            ['api','sensitive','consents',null,'API ดึงข้อมูล Consent','consent',10],
            ['print','personal','rights_requests',1,'พิมพ์คำร้องสิทธิ์','legal_obligation'],
        ];

        $baseTime = now()->subDays(30);
        foreach ($accessTypes as $j => $at) {
            $user = $users->random();
            DataAccessLog::create([
                'organization_id' => $orgId,
                'user_id'         => $user->id,
                'user_name'       => $user->name,
                'access_type'     => $at[0],
                'data_category'   => $at[1],
                'table_name'      => $at[2],
                'record_id'       => $at[3],
                'fields_accessed' => $at[0]==='read' ? ['id','name','email','phone'] : null,
                'record_count'    => $at[6] ?? 1,
                'purpose'         => $at[4],
                'legal_basis'     => $at[5],
                'is_cross_border' => $j === 4,
                'destination_country' => $j === 4 ? 'Singapore' : null,
                'ip_address'      => '192.168.1.'.rand(1,50),
                'request_id'      => \Illuminate\Support\Str::uuid(),
                'created_at'      => $baseTime->copy()->addDays(rand(0,30))->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function seedConsentEventLogs(int $orgId, $admin): void
    {
        $events = [
            ['granted','web','v3','ยินยอมรับข้อมูลการตลาด','สมศักดิ์ ใจดี','somsak@mail.com'],
            ['granted','web','v3','ยินยอมประมวลผลข้อมูลส่วนบุคคล','สมหญิง สวยงาม','somying@mail.com'],
            ['withdrawn','web','v3','ถอนความยินยอมการตลาด','สมศักดิ์ ใจดี','somsak@mail.com'],
            ['granted','api','v2','ยินยอมการใช้ Cookie','นักพัฒนา ระบบ','dev@api.com'],
            ['expired','','v2','Consent หมดอายุ 2 ปี','วิชัย มาดี','wichai@example.com'],
            ['renewed','web','v3','ต่ออายุ Consent','วิชัย มาดี','wichai@example.com'],
            ['granted','paper','v1','ยินยอมแบบกระดาษ — นำเข้าระบบ','ลูกค้า ออฟไลน์','customer@offline.com'],
            ['imported','import','v2','นำเข้าข้อมูล Consent จาก Legacy System','ผู้ใช้เก่า ระบบเดิม','legacy@old.com'],
            ['amended','web','v3','แก้ไขข้อมูลการยินยอม','สมหญิง สวยงาม','somying@mail.com'],
            ['rejected','web','v3','ปฏิเสธให้ความยินยอม','ผู้ใช้นิรนาม ไม่ยินยอม','anon@example.com'],
            ['granted','email','v3','ยินยอมผ่านอีเมล link','อีเมล ลูกค้า','email@customer.com'],
            ['withdrawn','api','v3','ถอนผ่าน API','API User ภายนอก','apiuser@partner.com'],
        ];

        $baseTime = now()->subDays(60);
        foreach ($events as $i => [$eventType, $channel, $version, $purpose, $name, $email]) {
            ConsentEventLog::create([
                'organization_id'       => $orgId,
                'consent_id'            => rand(1,13),
                'data_subject_id'       => rand(1,14),
                'data_subject_name'     => $name,
                'data_subject_email'    => $email,
                'event_type'            => $eventType,
                'consent_purpose'       => $purpose,
                'consent_version'       => $version,
                'channel'               => $channel ?: 'web',
                'consent_text_snapshot' => "ข้าพเจ้ายินยอมให้ประมวลผลข้อมูลส่วนบุคคล เพื่อ{$purpose} ตามนโยบายความเป็นส่วนตัว ฉบับ {$version}",
                'proof_reference'       => 'REF-'.strtoupper(\Illuminate\Support\Str::random(8)),
                'ip_address'            => '171.96.'.rand(1,254).'.'.rand(1,254),
                'user_agent_hash'       => hash('sha256', 'Mozilla/5.0 Chrome/120 '.$i),
                'recorded_by'           => $admin->id,
                'notes'                 => null,
                'event_at'              => $baseTime->copy()->addDays(rand(0,60))->format('Y-m-d H:i:s'),
                'created_at'            => now()->subDays(rand(0,5))->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function seedSystemErrorLogs(): void
    {
        $errors = [
            ['error','app','SQLSTATE[42000]: Syntax error in query','Illuminate\Database\QueryException',
             'vendor/laravel/framework/src/Illuminate/Database/Connection.php',760,
             "Illuminate\Database\QueryException: SQLSTATE[42000]\n#0 Connection.php(760): PDOStatement->execute()\n#1 Builder.php(2345): Connection->run()"],
            ['critical','app','Class App\\Models\\InvalidModel not found','Symfony\Component\Debug\Exception\FatalErrorException',
             'app/Http/Controllers/Web/SomeController.php',45,
             "FatalErrorException: Class not found\n#0 SomeController.php(45): new InvalidModel()"],
            ['warning','queue','Job failed after 3 attempts: SendConsentEmail','Illuminate\Queue\MaxAttemptsExceededException',
             'vendor/laravel/framework/src/Illuminate/Queue/Worker.php',384,null],
            ['error','api','Unauthenticated API request — missing token','Illuminate\Auth\AuthenticationException',
             'vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php',67,null],
            ['warning','app','Memory limit approaching 128MB','','',0,null],
            ['error','scheduler','Scheduled task RopaReviewReminder failed','RuntimeException',
             'app/Console/Commands/RopaReviewReminder.php',89,
             "RuntimeException: Mail transport failed\n#0 Command.php(89): Mailer->send()"],
            ['info','app','Cache cleared successfully','','',0,null],
            ['error','app','File upload failed: disk full','Illuminate\Contracts\Filesystem\FileNotFoundException',
             'app/Http/Controllers/Web/BreachController.php',120,null],
            ['critical','queue','Redis connection refused','Predis\Connection\ConnectionException',
             'vendor/predis/predis/src/Connection/AbstractConnection.php',155,
             "ConnectionException: Connection refused [tcp://127.0.0.1:6379]\n#0 AbstractConnection.php(155)"],
            ['notice','app','Deprecated: strptime() is deprecated in PHP 8.5','','vendor/some/lib.php',23,null],
        ];

        $baseTime = now()->subDays(30);
        foreach ($errors as $i => [$level, $channel, $message, $exClass, $file, $line, $trace]) {
            SystemErrorLog::create([
                'level'             => $level,
                'channel'           => $channel,
                'message'           => $message,
                'exception_class'   => $exClass ?: null,
                'file'              => $file ?: null,
                'line'              => $line ?: null,
                'stack_trace'       => $trace,
                'context'           => ['route'=>'ropa.index','user_id'=>1],
                'request_url'       => 'https://pdpa.local/ropa',
                'request_method'    => 'GET',
                'ip_address'        => '192.168.1.1',
                'environment'       => 'production',
                'app_version'       => '1.0.0',
                'is_resolved'       => in_array($i,[2,6,9]) ? true : false,
                'resolved_at'       => in_array($i,[2,6,9]) ? now()->subDays(1) : null,
                'resolved_by'       => in_array($i,[2,6,9]) ? 'admin' : null,
                'resolution_note'   => in_array($i,[2]) ? 'ปรับ queue retry limit' : null,
                'occurrence_count'  => rand(1,15),
                'last_occurred_at'  => now()->subHours(rand(1,48)),
                'created_at'        => $baseTime->copy()->addDays(rand(0,30))->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
