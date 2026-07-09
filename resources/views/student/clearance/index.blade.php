<x-app-layout>
    <x-slot name="header">My Application</x-slot>

    <div style="max-width:780px;margin:0 auto;">

        @if (session('success'))
            <div class="alert-success" style="margin-bottom:16px;">✅ {{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="alert-success" style="margin-bottom:16px;">ℹ️ {{ session('info') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error" style="margin-bottom:16px;">❌ {{ session('error') }}</div>
        @endif

        @if (!$application)
            {{-- No application yet --}}
            <div class="card card-p" style="text-align:center;padding:48px;">
                <div style="font-size:48px;margin-bottom:12px;">📋</div>
                <div class="section-title">No Active Application</div>
                <p style="font-size:13px;color:#6B7280;margin:8px 0 20px;">
                    Hello <strong>{{ $student->display_name }}</strong>,
                    you have not submitted any application yet.
                </p>
                <a href="{{ route('student.clearance.create') }}" class="btn-primary">
                    + Submit New Application
                </a>
            </div>

        @else
            @php
                $sv = $application->status->value;
                $typeLabels = [
                    'graduation' => '🎓 Graduation Clearance',
                    'deferral'   => '📅 Deferral of Studies',
                    'transfer'   => '🔄 Transfer to Another Institution',
                    'withdrawal' => '🚪 Withdrawal from University',
                    'other'      => '📝 Other',
                ];
                $progress = $application->progressPercentage();
            @endphp

            {{-- Application Overview --}}
            <div class="card card-p" style="margin-bottom:16px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:16px;font-weight:700;color:#003B5C;">
                            {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Application' }}
                        </div>
                        <div style="font-size:13px;color:#6B7280;margin-top:4px;">
                            Academic Year: <strong>{{ $application->academic_year }}</strong>
                        </div>
                        <div style="font-size:13px;color:#6B7280;">
                            Applicant: <strong>{{ $application->student_full_name }}</strong>
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">
                            Submitted: {{ $application->submitted_at?->format('d M Y, g:i A') ?? $application->created_at->format('d M Y') }}
                        </div>
                    </div>

                    @if($sv === 'awaiting_registrar')
                        <span class="badge" style="background:#DBEAFE;color:#1D4ED8;padding:6px 14px;">
                            📬 Awaiting Registrar
                        </span>
                    @elseif($sv === 'approved')
                        <span class="badge badge-approved" style="padding:6px 14px;">✅ Approved</span>
                    @elseif($sv === 'rejected')
                        <span class="badge badge-rejected" style="padding:6px 14px;">❌ Rejected</span>
                    @else
                        <span class="badge badge-pending" style="padding:6px 14px;">⏳ Pending</span>
                    @endif
                </div>

                @if ($application->remarks && $sv === 'rejected')
                    <div style="margin-top:12px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;font-size:13px;color:#991B1B;">
                        <strong>Rejection reason:</strong> {{ $application->remarks }}
                    </div>
                @endif

                {{-- Progress bar --}}
                <div style="margin-top:16px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:#6B7280;margin-bottom:6px;">
                        <span>Department approvals</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:{{ $progress }}%;"></div>
                    </div>
                </div>
            </div>

            {{-- Department Status --}}
            <div class="card card-p" style="margin-bottom:16px;">
                <div class="section-title">Department Review Status</div>
                @foreach ($application->departmentClearances as $clearance)
                    @php $ds = $clearance->status->value; @endphp
                    <div class="dept-row">
                        <div class="dept-name">
                            @if($ds === 'approved') ✅
                            @elseif($ds === 'rejected') ❌
                            @else ⏳
                            @endif
                            <div>
                                <div>{{ $clearance->department->name }}</div>
                                @if($clearance->remarks)
                                    <div style="font-size:12px;color:#6B7280;">{{ $clearance->remarks }}</div>
                                @endif
                                @if($clearance->reviewed_at)
                                    <div style="font-size:11px;color:#9CA3AF;">
                                        Reviewed {{ $clearance->reviewed_at->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="badge badge-{{ $ds }}">{{ $clearance->status->label() }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Supporting Documents --}}
            <div class="card card-p" style="margin-bottom:16px;">
                <div class="section-title">Supporting Documents</div>

                <form method="POST" action="{{ route('student.clearance.documents.upload') }}"
                      enctype="multipart/form-data"
                      style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:16px;">
                    @csrf
                    <select name="department_id" class="form-input" style="width:auto;min-width:180px;">
                        <option value="">General document</option>
                        @foreach ($application->departmentClearances as $clearance)
                            <option value="{{ $clearance->department->id }}">
                                {{ $clearance->department->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="file" name="document" required style="font-size:13px;">
                    <button type="submit" class="btn-primary">Upload</button>
                </form>

                @error('document')
                    <p style="color:#DC2626;font-size:12px;margin-bottom:10px;">{{ $message }}</p>
                @enderror

                @if ($application->documents->isNotEmpty())
                    @foreach ($application->documents as $doc)
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:9px 0;border-bottom:1px solid #F3F4F6;">
                            <span style="font-size:13px;">📄 {{ $doc->original_name }}</span>
                            <a href="{{ route('student.clearance.documents.download', $doc) }}"
                               style="font-size:12px;color:#00AEEF;">Download</a>
                        </div>
                    @endforeach
                @else
                    <p style="font-size:13px;color:#9CA3AF;">No documents uploaded yet.</p>
                @endif
            </div>

            {{-- ══════════════════════════════════
                 CERTIFICATE SECTION
            ══════════════════════════════════ --}}
            @if ($application->certificate)
                <div class="card card-p" style="background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border:2px solid #16A34A;">
                    <div style="text-align:center;">
                        <div style="font-size:36px;margin-bottom:8px;">🎓</div>
                        <div style="font-size:18px;font-weight:700;color:#14532D;margin-bottom:4px;">
                            Your Certificate is Ready!
                        </div>
                        <div style="font-size:13px;color:#166534;margin-bottom:4px;">
                            Certificate No: <strong>{{ $application->certificate->certificate_number }}</strong>
                        </div>
                        <div style="font-size:12px;color:#4B7C59;margin-bottom:20px;">
                            Issued: {{ $application->certificate->issued_at->format('d F Y') }}
                            · Approved by Academic Registrar
                        </div>
                        <a href="{{ route('student.certificate.download', $application->certificate) }}"
                           class="btn-primary"
                           style="background:#16A34A;font-size:14px;padding:12px 28px;">
                            ⬇ Download Certificate (PDF)
                        </a>
                        <div style="font-size:11px;color:#4B7C59;margin-top:12px;">
                            Your certificate is digitally verifiable. Keep it safe.
                        </div>
                    </div>
                </div>

            @elseif($sv === 'awaiting_registrar')
                <div class="card card-p" style="background:#EFF6FF;border:1px solid #BFDBFE;text-align:center;">
                    <div style="font-size:28px;margin-bottom:8px;">📬</div>
                    <div style="font-size:14px;font-weight:600;color:#1E3A5F;">Awaiting Registrar Approval</div>
                    <div style="font-size:13px;color:#3B82F6;margin-top:6px;">
                        All departments have cleared your application.
                        The Academic Registrar is conducting a final review.
                        You will be notified once your certificate is issued.
                    </div>
                </div>

            @elseif($sv === 'rejected')
                <div class="card card-p" style="background:#FEF2F2;border:1px solid #FECACA;text-align:center;">
                    <div style="font-size:28px;margin-bottom:8px;">❌</div>
                    <div style="font-size:14px;font-weight:600;color:#991B1B;">Application Rejected</div>
                    <div style="font-size:13px;color:#B91C1C;margin-top:6px;">
                        Please resolve any outstanding issues and submit a new application.
                    </div>
                    <a href="{{ route('student.clearance.create') }}" class="btn-primary"
                       style="margin-top:14px;display:inline-block;background:#DC2626;">
                        Submit New Application
                    </a>
                </div>

            @else
                <div class="card card-p" style="text-align:center;color:#9CA3AF;">
                    <div style="font-size:28px;margin-bottom:8px;">⏳</div>
                    <div style="font-size:13px;">
                        Your certificate will be available here once all departments approve
                        and the Academic Registrar issues final clearance.
                    </div>
                </div>
            @endif

        @endif
    </div>
</x-app-layout>