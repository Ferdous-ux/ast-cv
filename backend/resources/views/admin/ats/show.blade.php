@extends('admin.layouts.app')

@section('title', __('ats.show_title'))

@section('content')
@php
    $details = is_array($analysis->details ?? null) ? $analysis->details : [];
    $matchedSkills = is_array($analysis->matched_skills ?? null) ? $analysis->matched_skills : [];
    $missingSkills = is_array($analysis->missing_skills ?? null) ? $analysis->missing_skills : [];
    $matchedKeywords = is_array($analysis->matched_keywords ?? null) ? $analysis->matched_keywords : [];
    $missingKeywords = is_array($analysis->missing_keywords ?? null) ? $analysis->missing_keywords : [];
    $recommendations = is_array($analysis->recommendations ?? null) ? $analysis->recommendations : [];

    $experience = is_array($details['experience'] ?? null) ? $details['experience'] : [];
    $education = is_array($details['education'] ?? null) ? $details['education'] : [];
    $structure = is_array($details['structure'] ?? null) ? $details['structure'] : [];
    $sections = is_array($structure['sections'] ?? null) ? $structure['sections'] : [];

    $isArabic = app()->getLocale() === 'ar';

    $labels = [
        'engine_version' => $isArabic ? 'إصدار محرك التحليل' : 'Engine Version',
        'skill_details' => $isArabic ? 'تفاصيل مطابقة المهارات' : 'Skill Matching Details',
        'skill_details_help' => $isArabic
            ? 'يوضح هذا الجدول سبب احتساب كل مهارة، وأهميتها، ووزنها، ومستوى المرشح ومصدر اكتشافها.'
            : 'This table explains how each skill affected the score, including importance, weight, level and detection source.',
        'skill' => $isArabic ? 'المهارة' : 'Skill',
        'match_status' => $isArabic ? 'الحالة' : 'Status',
        'importance' => $isArabic ? 'الأهمية' : 'Importance',
        'weight' => $isArabic ? 'الوزن' : 'Weight',
        'effective_weight' => $isArabic ? 'الوزن الفعلي' : 'Effective Weight',
        'earned_weight' => $isArabic ? 'الوزن المكتسب' : 'Earned Weight',
        'minimum_level' => $isArabic ? 'المستوى المطلوب' : 'Required Level',
        'candidate_level' => $isArabic ? 'مستوى المرشح' : 'Candidate Level',
        'job_source' => $isArabic ? 'مصدر متطلب الوظيفة' : 'Job Source',
        'resume_source' => $isArabic ? 'مصدر مهارة السيرة' : 'Resume Source',
        'configured' => $isArabic ? 'محدد يدويًا' : 'Configured',
        'detected' => $isArabic ? 'مكتشف تلقائيًا' : 'Auto-detected',
        'structured' => $isArabic ? 'قسم المهارات' : 'Skills Section',
        'required' => $isArabic ? 'إلزامية' : 'Required',
        'preferred' => $isArabic ? 'مفضلة' : 'Preferred',
        'matched' => $isArabic ? 'متطابقة' : 'Matched',
        'missing' => $isArabic ? 'مفقودة' : 'Missing',
        'not_set' => $isArabic ? 'غير محدد' : 'Not set',
        'meets_level' => $isArabic ? 'يحقق المستوى' : 'Meets level',
        'below_level' => $isArabic ? 'أقل من المطلوب' : 'Below required',
        'skill_statistics' => $isArabic ? 'إحصائيات المهارات' : 'Skill Statistics',
        'configured_skills' => $isArabic ? 'المهارات المحددة' : 'Configured Skills',
        'detected_job_skills' => $isArabic ? 'المكتشفة من الوظيفة' : 'Detected Job Skills',
        'resume_total_skills' => $isArabic ? 'إجمالي مهارات السيرة' : 'Resume Skills Total',
        'resume_structured_skills' => $isArabic ? 'مهارات السيرة المسجلة' : 'Structured Resume Skills',
        'resume_detected_skills' => $isArabic ? 'المكتشفة من نص السيرة' : 'Detected Resume Skills',
        'total_weight' => $isArabic ? 'إجمالي الوزن' : 'Total Weight',
        'earned_weight_total' => $isArabic ? 'الوزن المكتسب' : 'Earned Weight',
        'analysis_evidence' => $isArabic ? 'أدلة التحليل' : 'Analysis Evidence',
        'experience_required' => $isArabic ? 'الخبرة المطلوبة' : 'Required Experience',
        'experience_actual' => $isArabic ? 'خبرة المرشح' : 'Candidate Experience',
        'education_required' => $isArabic ? 'المؤهل المطلوب' : 'Required Education',
        'education_candidate' => $isArabic ? 'أعلى مؤهل للمرشح' : 'Candidate Highest Education',
        'years' => $isArabic ? 'سنة' : 'years',
        'yes' => $isArabic ? 'نعم' : 'Yes',
        'no' => $isArabic ? 'لا' : 'No',
        'structure_sections' => $isArabic ? 'أقسام السيرة' : 'Resume Sections',
        'complete' => $isArabic ? 'مكتمل' : 'Complete',
        'incomplete' => $isArabic ? 'غير مكتمل' : 'Incomplete',
        'summary' => $isArabic ? 'الملخص' : 'Summary',
        'skills' => $isArabic ? 'المهارات' : 'Skills',
        'experience' => $isArabic ? 'الخبرة' : 'Experience',
        'education' => $isArabic ? 'التعليم' : 'Education',
        'languages' => $isArabic ? 'اللغات' : 'Languages',
        'analysis_snapshot' => $isArabic
            ? 'هذه النتيجة محفوظة كلقطة وقت تنفيذ التحليل، لذلك لا تتغير التحليلات القديمة تلقائيًا عند تحديث المحرك أو بيانات السيرة والوظيفة.'
            : 'This result is stored as a snapshot at analysis time. Older analyses do not change automatically when the engine, resume or job data is updated.',
    ];

    $sectionLabel = fn (string $section) => $labels[$section] ?? ucfirst(str_replace('_', ' ', $section));

    $recommendationText = function (array $recommendation) use ($isArabic) {
        return match ($recommendation['code'] ?? '') {
            'add_missing_skills' => $isArabic
                ? 'أضف أو أبرز المهارات المفقودة: ' . implode(', ', $recommendation['skills'] ?? [])
                : 'Add or highlight missing skills: ' . implode(', ', $recommendation['skills'] ?? []),

            'increase_experience_match' => $isArabic
                ? 'الخبرة الحالية أقل من المطلوب. خبرة المرشح: ' . ($recommendation['actual_years'] ?? 0) . ' سنة، والمطلوب: ' . ($recommendation['required_years'] ?? 0) . ' سنة.'
                : 'Candidate experience is below the requirement. Actual: ' . ($recommendation['actual_years'] ?? 0) . ' years, required: ' . ($recommendation['required_years'] ?? 0) . ' years.',

            'education_requirement_not_met' => $isArabic
                ? 'المؤهل التعليمي المطلوب غير متحقق: ' . ($recommendation['required'] ?? '')
                : 'Required education is not met: ' . ($recommendation['required'] ?? ''),

            'add_missing_keywords' => $isArabic
                ? 'أضف الكلمات المفتاحية المناسبة إذا كانت تعكس خبرتك الحقيقية: ' . implode(', ', $recommendation['keywords'] ?? [])
                : 'Add relevant keywords when they accurately reflect your experience: ' . implode(', ', $recommendation['keywords'] ?? []),

            'complete_section' => $isArabic
                ? 'أكمل قسم ' . ucfirst((string) ($recommendation['section'] ?? '')) . ' في السيرة.'
                : 'Complete the ' . ucfirst((string) ($recommendation['section'] ?? '')) . ' section of the resume.',

            'strong_match' => $isArabic
                ? 'السيرة متوافقة بشكل قوي مع متطلبات الوظيفة الحالية.'
                : 'The resume is a strong match for the current job requirements.',

            default => $isArabic ? 'راجع بيانات السيرة والوظيفة لتحسين التوافق.' : 'Review the resume and job data to improve the match.',
        };
    };
@endphp

<div class="dashboard">
    <div class="platform-hero">
        <div>
            <div class="role-badge" style="display:inline-flex;margin-bottom:12px;">
                {{ $labels['engine_version'] }}:
                {{ $details['engine_version'] ?? 'ATS' }}
            </div>

            <h1>{{ __('ats.show_title') }}</h1>

            <p>
                {{ $analysis->job?->title ?? '-' }}
                @if($analysis->job?->company_name)
                    · {{ $analysis->job->company_name }}
                @endif
            </p>

            <p>
                {{ __('ats.resume') }}:
                {{ $analysis->resumeVersion?->resume?->title ?? '-' }}

                @if($analysis->resumeVersion?->resume?->user?->name)
                    · {{ $analysis->resumeVersion->resume->user->name }}
                @endif
            </p>
        </div>

        <div style="text-align:center;min-width:180px;">
            <div style="font-size:13px;opacity:.85;margin-bottom:6px;">
                {{ __('ats.overall_score') }}
            </div>
            <div style="font-size:46px;font-weight:800;line-height:1;">
                {{ number_format((float) $analysis->overall_score, 1) }}%
            </div>
        </div>
    </div>

    <div class="staff-form-grid" style="margin-top:20px;">
        <div class="dashboard-card">
            <div class="staff-form-label">{{ __('ats.skills_score') }}</div>
            <div style="font-size:28px;font-weight:800;">
                {{ number_format((float) $analysis->skills_score, 1) }}%
            </div>
        </div>

        <div class="dashboard-card">
            <div class="staff-form-label">{{ __('ats.experience_score') }}</div>
            <div style="font-size:28px;font-weight:800;">
                {{ number_format((float) $analysis->experience_score, 1) }}%
            </div>
        </div>

        <div class="dashboard-card">
            <div class="staff-form-label">{{ __('ats.keywords_score') }}</div>
            <div style="font-size:28px;font-weight:800;">
                {{ number_format((float) $analysis->keywords_score, 1) }}%
            </div>
        </div>

        <div class="dashboard-card">
            <div class="staff-form-label">{{ __('ats.education_score') }}</div>
            <div style="font-size:28px;font-weight:800;">
                {{ number_format((float) $analysis->education_score, 1) }}%
            </div>
        </div>

        <div class="dashboard-card">
            <div class="staff-form-label">{{ __('ats.structure_score') }}</div>
            <div style="font-size:28px;font-weight:800;">
                {{ number_format((float) $analysis->structure_score, 1) }}%
            </div>
        </div>
    </div>

    <div class="dashboard-card" style="margin-top:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0 0 6px;">{{ $labels['skill_details'] }}</h2>
                <p style="margin:0;opacity:.75;">{{ $labels['skill_details_help'] }}</p>
            </div>

            <div class="role-badge">
                {{ count($matchedSkills) }} / {{ $details['skills_total'] ?? (count($matchedSkills) + count($missingSkills)) }}
                {{ $labels['matched'] }}
            </div>
        </div>

        <div style="overflow-x:auto;margin-top:18px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ $labels['skill'] }}</th>
                        <th>{{ $labels['match_status'] }}</th>
                        <th>{{ $labels['importance'] }}</th>
                        <th>{{ $labels['weight'] }}</th>
                        <th>{{ $labels['effective_weight'] }}</th>
                        <th>{{ $labels['earned_weight'] }}</th>
                        <th>{{ $labels['minimum_level'] }}</th>
                        <th>{{ $labels['candidate_level'] }}</th>
                        <th>{{ $labels['job_source'] }}</th>
                        <th>{{ $labels['resume_source'] }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($matchedSkills as $skill)
                        <tr>
                            <td><strong>{{ $skill['name'] ?? '-' }}</strong></td>
                            <td>
                                <span class="role-badge">✓ {{ $labels['matched'] }}</span>
                            </td>
                            <td>
                                {{ ($skill['importance'] ?? 'required') === 'preferred'
                                    ? $labels['preferred']
                                    : $labels['required'] }}
                            </td>
                            <td>{{ $skill['weight'] ?? '-' }}</td>
                            <td>{{ $skill['effective_weight'] ?? '-' }}</td>
                            <td>{{ $skill['earned_weight'] ?? '-' }}</td>
                            <td>{{ $skill['minimum_level'] ?? $labels['not_set'] }}</td>
                            <td>
                                {{ $skill['resume_level'] ?? $labels['not_set'] }}

                                @if(array_key_exists('meets_minimum_level', $skill))
                                    <div style="font-size:12px;opacity:.72;margin-top:4px;">
                                        {{ $skill['meets_minimum_level']
                                            ? '✓ ' . $labels['meets_level']
                                            : '⚠ ' . $labels['below_level'] }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ ($skill['job_source'] ?? 'configured') === 'detected'
                                    ? $labels['detected']
                                    : $labels['configured'] }}
                            </td>
                            <td>
                                {{ ($skill['resume_source'] ?? 'structured') === 'detected'
                                    ? $labels['detected']
                                    : $labels['structured'] }}
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    @foreach($missingSkills as $skill)
                        <tr>
                            <td><strong>{{ $skill['name'] ?? '-' }}</strong></td>
                            <td>
                                <span class="role-badge">✕ {{ $labels['missing'] }}</span>
                            </td>
                            <td>
                                {{ ($skill['importance'] ?? 'required') === 'preferred'
                                    ? $labels['preferred']
                                    : $labels['required'] }}
                            </td>
                            <td>{{ $skill['weight'] ?? '-' }}</td>
                            <td>{{ $skill['effective_weight'] ?? '-' }}</td>
                            <td>0</td>
                            <td>{{ $skill['minimum_level'] ?? $labels['not_set'] }}</td>
                            <td>{{ $labels['missing'] }}</td>
                            <td>
                                {{ ($skill['job_source'] ?? 'configured') === 'detected'
                                    ? $labels['detected']
                                    : $labels['configured'] }}
                            </td>
                            <td>—</td>
                        </tr>
                    @endforeach

                    @if(empty($matchedSkills) && empty($missingSkills))
                        <tr>
                            <td colspan="10">{{ __('ats.no_skills') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="staff-form-grid" style="margin-top:20px;">
        <div class="dashboard-card">
            <h2 style="margin-top:0;">{{ $labels['skill_statistics'] }}</h2>

            <div class="roles-box">
                <div class="role-option">
                    <span>{{ $labels['configured_skills'] }}</span>
                    <strong>{{ $details['skills_configured'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['detected_job_skills'] }}</span>
                    <strong>{{ $details['skills_detected'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['resume_total_skills'] }}</span>
                    <strong>{{ $details['resume_skills_total'] ?? $details['resume_skills_detected'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['resume_structured_skills'] }}</span>
                    <strong>{{ $details['resume_skills_structured'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['resume_detected_skills'] }}</span>
                    <strong>{{ $details['resume_skills_detected'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['total_weight'] }}</span>
                    <strong>{{ $details['skills_total_weight'] ?? '-' }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['earned_weight_total'] }}</span>
                    <strong>{{ $details['skills_earned_weight'] ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <h2 style="margin-top:0;">{{ $labels['analysis_evidence'] }}</h2>

            <div class="roles-box">
                <div class="role-option">
                    <span>{{ $labels['experience_actual'] }}</span>
                    <strong>{{ $experience['actual_years'] ?? 0 }} {{ $labels['years'] }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['experience_required'] }}</span>
                    <strong>{{ $experience['required_years'] ?? 0 }} {{ $labels['years'] }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['education_candidate'] }}</span>
                    <strong>{{ $education['candidate_highest'] ?? $labels['not_set'] }}</strong>
                </div>

                <div class="role-option">
                    <span>{{ $labels['education_required'] }}</span>
                    <strong>{{ $education['required'] ?? $labels['not_set'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="staff-form-grid" style="margin-top:20px;">
        <div class="dashboard-card">
            <h2 style="margin-top:0;">{{ __('ats.matched_keywords') }}</h2>

            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @forelse($matchedKeywords as $keyword)
                    <span class="role-badge">✓ {{ $keyword }}</span>
                @empty
                    <span>{{ __('ats.no_keywords') }}</span>
                @endforelse
            </div>
        </div>

        <div class="dashboard-card">
            <h2 style="margin-top:0;">{{ __('ats.missing_keywords') }}</h2>

            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @forelse($missingKeywords as $keyword)
                    <span class="role-badge">✕ {{ $keyword }}</span>
                @empty
                    <span>{{ __('ats.no_keywords') }}</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="dashboard-card" style="margin-top:20px;">
        <h2 style="margin-top:0;">{{ $labels['structure_sections'] }}</h2>

        <div class="roles-box">
            @forelse($sections as $section => $exists)
                <div class="role-option">
                    <span>{{ $sectionLabel($section) }}</span>
                    <strong>
                        {{ $exists ? '✓ ' . $labels['complete'] : '✕ ' . $labels['incomplete'] }}
                    </strong>
                </div>
            @empty
                <div class="role-option">
                    <span>{{ __('ats.no_recommendations') }}</span>
                </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-card" style="margin-top:20px;">
        <h2 style="margin-top:0;">{{ __('ats.recommendations') }}</h2>

        <div class="roles-box">
            @forelse($recommendations as $recommendation)
                <div class="role-option">
                    <span>{{ $recommendationText($recommendation) }}</span>
                </div>
            @empty
                <div class="role-option">
                    <span>{{ __('ats.no_recommendations') }}</span>
                </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-card" style="margin-top:20px;">
        <p style="margin:0;opacity:.72;">
            {{ $labels['analysis_snapshot'] }}
        </p>
    </div>

    <div class="staff-form-actions" style="margin-top:20px;">
        <a href="{{ route('admin.ats.index') }}" class="staff-btn staff-btn-secondary">
            {{ __('ats.cancel') }}
        </a>

        <a href="{{ route('admin.ats.create') }}" class="staff-btn staff-btn-primary">
            {{ __('ats.new_analysis') }}
        </a>
    </div>
</div>
@endsection
