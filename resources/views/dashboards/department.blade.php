<x-app-layout>
    <x-slot name="header">Officer Dashboard</x-slot>

    <style>
        /* ── Tab bar ── */
        .tab-bar {
            display: flex;
            gap: 4px;
            background: white;
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            margin-bottom: 24px;
            width: fit-content;
        }
        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #6B7280;
            transition: all 0.2s ease;
            border: none;
            background: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .tab-btn:hover { background: #F3F4F6; color: #374151; }
        .tab-btn.active-pending  { background: #FEF3C7; color: #92400E; }
        .tab-btn.active-approved { background: #D1FAE5; color: #065F46; }
        .tab-btn.active-rejected { background: #FEE2E2; color: #991B1B; }
        .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .count-pending  { background: #F59E0B; color: white; }
        .count-approved { background: #10B981; color: white; }
        .count-rejected { background: #EF4444; color: white; }

        /* ── Student cards ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }
        .student-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            border: 1px solid #F3F4F6;
            overflow: hidden;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            display: flex;
            flex-direction: column;
        }
        .student-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            transform: translateY(-2px);
        }
        .card-header {
            padding: 16px 16px 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .student-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #003B5C, #00AEEF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .student-info { flex: 1; min-width: 0; }
        .student-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .student-reg {
            font-size: 12px;
            color: #6B7280;
            margin-top: 2px;
        }
        .student-faculty {
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Application type pill */
        .app-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #EFF6FF;
            color: #1D4ED8;
            flex-shrink: 0;
        }

        /* Progress section */
        .card-progress {
            padding: 0 16px 12px;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #9CA3AF;
            margin-bottom: 5px;
        }
        .progress-bar {
            height: 5px;
            background: #F3F4F6;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00AEEF, #003B5C);
            border-radius: 3px;
            transition: width 0.4s ease;
        }

        /* Submitted date */
        .card-meta {
            padding: 0 16px 12px;
            font-size: 11px;
            color: #9CA3AF;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Remarks (for rejected/approved) */
        .card-remarks {
            margin: 0 16px 12px;
            padding: 8px 12px;
            background: #FEF9C3;
            border-radius: 8px;
            font-size: 12px;
            color: #713F12;
            border-left: 3px solid #F59E0B;
        }
        .card-remarks.rejected-remark {
            background: #FEF2F2;
            color: #991B1B;
            border-left-color: #EF4444;
        }
        .card-remarks.approved-remark {
            background: #F0FDF4;
            color: #166534;
            border-left-color: #10B981;
        }

        /* Action buttons row */
        .card-actions {
            margin-top: auto;
            padding: 12px 16px;
            border-top: 1px solid #F3F4F6;
            display: flex;
            gap: 8px;
        }
        .btn-approve {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            min-height: 42px;
        }
        .btn-approve:hover { background: #059669; transform: scale(1.02); }
        .btn-approve:active { transform: scale(0.98); }
        .btn-reject {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            background: white;
            color: #EF4444;
            border: 2px solid #EF4444;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            min-height: 42px;
        }
        .btn-reject:hover { background: #FEF2F2; transform: scale(1.02); }
        .btn-reject:active { transform: scale(0.98); }

        /* Reviewed badge for approved/rejected cards */
        .reviewed-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            width: 100%;
            justify-content: center;
        }
        .reviewed-badge.approved { background: #D1FAE5; color: #065F46; }
        .reviewed-badge.rejected { background: #FEE2E2; color: #991B1B; }

        /* ── Rejection Modal ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 16px;
            backdrop-filter: blur(4px);
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: modalSlideIn 0.25s ease;
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid #FEE2E2;
            background: #FEF2F2;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FEE2E2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: #991B1B; }
        .modal-subtitle { font-size: 12px; color: #B91C1C; margin-top: 2px; }
        .modal-body { padding: 20px 24px; }
        .modal-student-info {
            background: #F9FAFB;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #003B5C, #00AEEF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .modal-student-name { font-size: 13px; font-weight: 600; color: #111827; }
        .modal-student-reg  { font-size: 11px; color: #6B7280; margin-top: 1px; }
        .reasons-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .reasons-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 14px;
        }
        .reason-chip {
            padding: 8px 10px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
            background: white;
            line-height: 1.3;
        }
        .reason-chip:hover { border-color: #EF4444; color: #EF4444; background: #FEF2F2; }
        .reason-chip.selected { border-color: #EF4444; background: #FEF2F2; color: #EF4444; font-weight: 600; }
        .remarks-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 13px;
            color: #374151;
            resize: vertical;
            min-height: 80px;
            transition: border-color 0.15s;
            font-family: inherit;
        }
        .remarks-textarea:focus { outline: none; border-color: #EF4444; }
        .modal-footer {
            padding: 16px 24px 20px;
            display: flex;
            gap: 10px;
        }
        .btn-modal-cancel {
            flex: 1;
            padding: 11px;
            background: #F3F4F6;
            color: #374151;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-modal-cancel:hover { background: #E5E7EB; }
        .btn-modal-confirm {
            flex: 2;
            padding: 11px;
            background: #EF4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-modal-confirm:hover { background: #DC2626; }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #111827;
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 320px;
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-success { border-left: 4px solid #10B981; }
        .toast-error   { border-left: 4px solid #EF4444; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 64px 24px;
            color: #9CA3AF;
        }
        .empty-state-icon { font-size: 56px; margin-bottom: 16px; display: block; }
        .empty-state-title { font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .empty-state-sub { font-size: 13px; color: #9CA3AF; }

        /* Dept info header */
        .dept-header {
            background: linear-gradient(135deg, #003B5C 0%, #005a8e 100%);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .dept-header-name { font-size: 18px; font-weight: 700; color: white; }
        .dept-header-sub  { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 2px; }
        .dept-header-stats {
            display: flex;
            gap: 16px;
        }
        .dept-stat { text-align: center; }
        .dept-stat-val { font-size: 22px; font-weight: 700; color: white; }
        .dept-stat-lbl { font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 2px; }

        @media (max-width: 768px) {
            .cards-grid { grid-template-columns: 1fr; }
            .tab-bar { width: 100%; overflow-x: auto; }
            .dept-header { padding: 16px; }
            .dept-header-name { font-size: 15px; }
            .reasons-grid { grid-template-columns: 1fr; }
        }
    </style>

    {{-- Toast notification --}}
    @if(session('success'))
        <div class="toast toast-success" id="successToast">
            <span style="font-size:16px;">✅</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="toast toast-error" id="errorToast">
            <span style="font-size:16px;">❌</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Department Header --}}
    <div class="dept-header">
        <div>
            <div class="dept-header-name">
                {{ $officer->departmentOfficer?->department?->name ?? 'My Department' }}
            </div>
            <div class="dept-header-sub">
                Department Officer · {{ $officer->name }}
            </div>
        </div>
        <div class="dept-header-stats">
            <div class="dept-stat">
                <div class="dept-stat-val">{{ $stats['pending'] }}</div>
                <div class="dept-stat-lbl">Pending</div>
            </div>
            <div class="dept-stat">
                <div class="dept-stat-val">{{ $stats['approved'] }}</div>
                <div class="dept-stat-lbl">Approved</div>
            </div>
            <div class="dept-stat">
                <div class="dept-stat-val">{{ $stats['rejected'] }}</div>
                <div class="dept-stat-lbl">Rejected</div>
            </div>
        </div>
    </div>

    {{-- Tab Bar --}}
    <div class="tab-bar">
        <a href="{{ route('department.dashboard', ['tab' => 'pending']) }}"
           class="tab-btn {{ $tab === 'pending' ? 'active-pending' : '' }}">
            ⏳ Pending
            @if($stats['pending'] > 0)
                <span class="tab-count count-pending">{{ $stats['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('department.dashboard', ['tab' => 'approved']) }}"
           class="tab-btn {{ $tab === 'approved' ? 'active-approved' : '' }}">
            ✅ Approved
            @if($stats['approved'] > 0)
                <span class="tab-count count-approved">{{ $stats['approved'] }}</span>
            @endif
        </a>
        <a href="{{ route('department.dashboard', ['tab' => 'rejected']) }}"
           class="tab-btn {{ $tab === 'rejected' ? 'active-rejected' : '' }}">
            ❌ Rejected
            @if($stats['rejected'] > 0)
                <span class="tab-count count-rejected">{{ $stats['rejected'] }}</span>
            @endif
        </a>
    </div>

    {{-- Cards Grid --}}
    @if($clearances->isEmpty())
        <div class="empty-state">
            @if($tab === 'pending')
                <span class="empty-state-icon">🎉</span>
                <div class="empty-state-title">You're all caught up!</div>
                <div class="empty-state-sub">No pending applications to review right now.</div>
            @elseif($tab === 'approved')
                <span class="empty-state-icon">📋</span>
                <div class="empty-state-title">No approved applications yet</div>
                <div class="empty-state-sub">Applications you approve will appear here.</div>
            @else
                <span class="empty-state-icon">📋</span>
                <div class="empty-state-title">No rejected applications</div>
                <div class="empty-state-sub">Applications you reject will appear here.</div>
            @endif
        </div>
    @else
        <div class="cards-grid">
            @foreach($clearances as $clearance)
                @php
                    $app     = $clearance->clearanceApplication;
                    $student = $app?->student;
                    $initials = strtoupper(substr($student?->full_name ?? 'UN', 0, 2));
                    $progress = $app?->progressPercentage() ?? 0;
                    $typeLabels = [
                        'graduation' => '🎓 Graduation',
                        'deferral'   => '📅 Deferral',
                        'transfer'   => '🔄 Transfer',
                        'withdrawal' => '🚪 Withdrawal',
                        'other'      => '📝 Other',
                    ];
                    $appType = $typeLabels[$app?->application_type ?? 'graduation'] ?? 'Application';
                @endphp

                <div class="student-card">
                    {{-- Card Header --}}
                    <div class="card-header">
                        <div class="student-avatar">{{ $initials }}</div>
                        <div class="student-info">
                            <div class="student-name">{{ $student?->full_name ?? 'Unknown Student' }}</div>
                            <div class="student-reg">{{ $student?->reg_number ?? '—' }}</div>
                            <div class="student-faculty">{{ $student?->faculty ?? '—' }} · {{ $student?->programme ?? '—' }}</div>
                        </div>
                        <span class="app-type-pill">{{ $appType }}</span>
                    </div>

                    {{-- Progress --}}
                    <div class="card-progress">
                        <div class="progress-label">
                            <span>Overall dept. progress</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $progress }}%;"></div>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="card-meta">
                        🕐 Submitted {{ $app?->submitted_at?->diffForHumans() ?? $app?->created_at?->diffForHumans() ?? '—' }}
                        · {{ $app?->academic_year }}
                    </div>

                    {{-- Remarks if reviewed --}}
                    @if($clearance->remarks && $tab !== 'pending')
                        <div class="card-remarks {{ $tab === 'rejected' ? 'rejected-remark' : 'approved-remark' }}">
                            "{{ $clearance->remarks }}"
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="card-actions">
                        @if($tab === 'pending')
                            {{-- Approve form --}}
                            <form method="POST"
                                  action="{{ route('department.clearance.review', $clearance) }}"
                                  style="flex:1;">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="remarks" value="">
                                <button type="submit" class="btn-approve"
                                        onclick="return confirm('Approve clearance for {{ addslashes($student?->full_name ?? 'this student') }}?')">
                                    ✓ Approve
                                </button>
                            </form>

                            {{-- Reject — opens modal --}}
                            <button type="button" class="btn-reject"
                                    onclick="openRejectModal(
                                        {{ $clearance->id }},
                                        '{{ addslashes($student?->full_name ?? 'Unknown') }}',
                                        '{{ $student?->reg_number ?? '' }}',
                                        '{{ route('department.clearance.review', $clearance) }}'
                                    )">
                                ✕ Reject
                            </button>

                        @elseif($tab === 'approved')
                            <div class="reviewed-badge approved" style="width:100%;">
                                ✅ Approved by you
                                @if($clearance->reviewed_at)
                                    · {{ $clearance->reviewed_at->format('d M Y') }}
                                @endif
                            </div>

                        @else
                            <div class="reviewed-badge rejected" style="width:100%;">
                                ❌ Rejected by you
                                @if($clearance->reviewed_at)
                                    · {{ $clearance->reviewed_at->format('d M Y') }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($clearances->hasPages())
            <div style="margin-top:24px;">{{ $clearances->links() }}</div>
        @endif
    @endif

    {{-- ═══════════════════════════════
         REJECTION MODAL
    ═══════════════════════════════ --}}
    <div class="modal-backdrop" id="rejectModal">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <div class="modal-icon">❌</div>
                <div>
                    <div class="modal-title">Reject Clearance</div>
                    <div class="modal-subtitle">Please provide a reason for the student</div>
                </div>
            </div>

            <div class="modal-body">
                {{-- Student info --}}
                <div class="modal-student-info">
                    <div class="modal-avatar" id="modalInitials">?</div>
                    <div>
                        <div class="modal-student-name" id="modalStudentName">—</div>
                        <div class="modal-student-reg"  id="modalStudentReg">—</div>
                    </div>
                </div>

                {{-- Common reasons --}}
                <div class="reasons-label">Select a common reason (or type below):</div>
                <div class="reasons-grid">
                    <div class="reason-chip" onclick="selectReason(this, 'Outstanding library fines or unreturned books.')">
                        📚 Outstanding library fines
                    </div>
                    <div class="reason-chip" onclick="selectReason(this, 'Unpaid fee balance. Please clear all outstanding fees at the Bursar.')">
                        💰 Unpaid fee balance
                    </div>
                    <div class="reason-chip" onclick="selectReason(this, 'Missing or unreturned equipment. Please return all department equipment.')">
                        🔧 Unreturned equipment
                    </div>
                    <div class="reason-chip" onclick="selectReason(this, 'Incomplete academic records. Please ensure all required documents are submitted.')">
                        📄 Incomplete records
                    </div>
                    <div class="reason-chip" onclick="selectReason(this, 'Hostel room not properly vacated. Please ensure all keys and items are returned.')">
                        🏠 Hostel not vacated
                    </div>
                    <div class="reason-chip" onclick="selectReason(this, 'Other issue. Please contact the department for more information.')">
                        📝 Other
                    </div>
                </div>

                {{-- Custom reason textarea --}}
                <textarea
                    id="rejectRemarks"
                    class="remarks-textarea"
                    placeholder="Or type a custom reason here..."
                    maxlength="1000"
                    rows="3"
                ></textarea>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;text-align:right;">
                    <span id="charCount">0</span>/1000
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeRejectModal()">
                    Cancel
                </button>
                <button type="button" class="btn-modal-confirm" onclick="submitRejection()">
                    ❌ Confirm Rejection
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden rejection form --}}
    <form id="rejectForm" method="POST" action="" style="display:none;">
        @csrf
        <input type="hidden" name="action" value="reject">
        <input type="hidden" name="remarks" id="rejectRemarksInput">
    </form>

    <script>
        // ── Toast auto-show ──
        document.addEventListener('DOMContentLoaded', function() {
            ['successToast', 'errorToast'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(() => el.classList.add('show'), 100);
                    setTimeout(() => el.classList.remove('show'), 4000);
                }
            });
        });

        // ── Rejection Modal ──
        let currentRejectUrl = '';

        function openRejectModal(id, name, reg, url) {
            currentRejectUrl = url;

            // Populate student info
            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentReg').textContent  = reg;
            document.getElementById('modalInitials').textContent    = name.substring(0, 2).toUpperCase();

            // Reset state
            document.getElementById('rejectRemarks').value = '';
            document.getElementById('charCount').textContent = '0';
            document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));

            // Open
            document.getElementById('rejectModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close modal when clicking backdrop
        document.getElementById('rejectModal').addEventListener('click', closeRejectModal);

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRejectModal();
        });

        // Select a reason chip
        function selectReason(chip, text) {
            document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));
            chip.classList.add('selected');
            const textarea = document.getElementById('rejectRemarks');
            textarea.value = text;
            document.getElementById('charCount').textContent = text.length;
            textarea.focus();
        }

        // Character counter
        document.getElementById('rejectRemarks').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Submit rejection
        function submitRejection() {
            const remarks = document.getElementById('rejectRemarks').value.trim();

            if (!remarks) {
                document.getElementById('rejectRemarks').style.borderColor = '#EF4444';
                document.getElementById('rejectRemarks').placeholder = '⚠️ Please provide a reason before rejecting.';
                document.getElementById('rejectRemarks').focus();
                return;
            }

            const form = document.getElementById('rejectForm');
            form.action = currentRejectUrl;
            document.getElementById('rejectRemarksInput').value = remarks;
            closeRejectModal();
            form.submit();
        }
    </script>

</x-app-layout>