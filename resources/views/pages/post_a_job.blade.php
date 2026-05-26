@extends('layouts.employer', ['activeNav' => 'jobs'])

@section('title', isset($job) ? 'Edit Job' : 'Post a Job')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden')

@section('page-css', 'post_a_job.css')

@section('tailwind-config', 'tailwind-config-post-job.js')

@php
    $job = $job ?? null;
    $isEdit = $job !== null;
    $inputClass = 'input-dark';
    $jobDefault = function (string $field) use ($job, $isEdit): mixed {
        if (! $isEdit) {
            return match ($field) {
                'number_of_openings' => 1,
                'currency' => 'USD',
                'status' => 'active',
                default => '',
            };
        }
        $value = $job->{$field};
        if ($field === 'application_deadline' && $value) {
            return $value->format('Y-m-d');
        }
        if ($field === 'number_of_openings') {
            return $value ?? 1;
        }
        return $value ?? '';
    };
    $fv = fn (string $field) => old($field, $jobDefault($field));
    $skillTags = array_values(array_filter(array_map('trim', explode(',', (string) $fv('skills_required')))));
    $initialStep = (int) old('_step', $initialStep ?? 1);
    if ($errors->any() && ! old('_step')) {
        $stepByField = [
            'title' => 1, 'company_name' => 1, 'location' => 1, 'job_type' => 1, 'experience_required' => 1,
            'description' => 2, 'responsibilities' => 2, 'requirements' => 2, 'skills_required' => 2,
            'screening_question_1' => 3, 'screening_question_2' => 3, 'screening_question_3' => 3,
            'minimum_qualification' => 3, 'preferred_qualification' => 3, 'work_mode' => 3, 'notice_period' => 3,
            'salary' => 4, 'min_salary' => 4, 'max_salary' => 4, 'currency' => 4,
            'application_deadline' => 4, 'number_of_openings' => 4, 'status' => 4,
        ];
        foreach ($errors->keys() as $field) {
            if (isset($stepByField[$field])) {
                $initialStep = $stepByField[$field];
                break;
            }
        }
    }
@endphp

@push('candidate-header-actions')
<a href="{{ route('hr.jobs.create') }}" class="flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg font-title-md text-[14px] hover:shadow-lg transition-all active:scale-95">
<span class="material-symbols-outlined text-[18px]" data-icon="add">add</span>
Post a Job
</a>
@endpush

@section('employer-main-full')
<div class="pt-24 pb-12 px-container-margin max-w-[1440px] mx-auto">

    @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 font-body-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 font-body-sm">
            Please fix the errors below and try again.
        </div>
    @endif

    <form id="job-post-form" method="POST" action="{{ $isEdit ? route('hr.jobs.update', $job) : route('hr.jobs.store') }}" novalidate>
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        <input type="hidden" name="skills_required" id="skills_required" value="{{ $fv('skills_required') }}">
        <input type="hidden" name="status" id="job_status" value="{{ $fv('status') }}">
        <input type="hidden" name="_step" id="form_step" value="{{ old('_step', $initialStep) }}">

        <div class="mb-8 animate-fade-in">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="badge-violet text-[11px]">{{ $isEdit ? 'Edit' : 'Create' }}</span>
                    </div>
                    <h2 class="text-[28px] font-extrabold text-on-surface">{{ $isEdit ? 'Edit Position' : 'Create New Position' }}</h2>
                </div>
                <div class="flex gap-3">
                    <button type="submit" id="btn-save-draft-header" class="btn-ghost py-2.5 px-5 text-[14px]">Save Draft</button>
                    <button type="submit" id="btn-publish-header" class="btn-primary py-2.5 px-6 text-[14px]">{{ $isEdit ? 'Update Job' : 'Publish Job' }}</button>
                </div>
            </div>
            <p class="text-[14px] text-on-surface-variant">Design your executive role and find the perfect match with AI-driven insights.</p>
        </div>

        <div id="job-stepper" class="glass-card p-6 mb-6 animate-fade-in-delay-1">
            <div class="flex justify-between items-center max-w-4xl mx-auto">
                <div data-step-indicator="1" class="flex flex-col items-center gap-2">
                    <div class="step-circle w-10 h-10 rounded-full flex items-center justify-center font-bold">1</div>
                    <span class="step-label font-label-caps">Basic Info</span>
                </div>
                <div data-step-connector="1" class="flex-1 h-[2px] mx-4"></div>
                <div data-step-indicator="2" class="flex flex-col items-center gap-2">
                    <div class="step-circle w-10 h-10 rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="step-label font-label-caps">Description</span>
                </div>
                <div data-step-connector="2" class="flex-1 h-[2px] mx-4"></div>
                <div data-step-indicator="3" class="flex flex-col items-center gap-2 opacity-50">
                    <div class="step-circle w-10 h-10 rounded-full border-2 border-outline-variant text-outline flex items-center justify-center font-bold">3</div>
                    <span class="step-label font-label-caps text-outline">Screening</span>
                </div>
                <div data-step-connector="3" class="flex-1 h-[2px] bg-outline-variant mx-4"></div>
                <div data-step-indicator="4" class="flex flex-col items-center gap-2 opacity-50">
                    <div class="step-circle w-10 h-10 rounded-full border-2 border-outline-variant text-outline flex items-center justify-center font-bold">4</div>
                    <span class="step-label font-label-caps text-outline">Budget</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-gutter">
            <div class="col-span-8 space-y-8">
                {{-- Step 1: Basic Info --}}
                <section data-step="1" class="job-step glass-card p-6 rounded-2xl">
                    <h3 class="font-title-md text-title-md mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary" data-icon="work">work</span>
                        Basic Information
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="title">Job Title</label>
                            <input type="text" name="title" id="title" value="{{ $fv('title') }}" class="{{ $inputClass }}" placeholder="e.g. Senior Software Engineer" required/>
                            @error('title')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" value="{{ $fv('company_name') }}" class="{{ $inputClass }}" placeholder="Your company" required/>
                            @error('company_name')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="location">Location</label>
                            <input type="text" name="location" id="location" value="{{ $fv('location') }}" class="{{ $inputClass }}" placeholder="City, Country or Remote" required/>
                            @error('location')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="job_type">Job Type</label>
                                <select name="job_type" id="job_type" class="{{ $inputClass }}" required>
                                    <option value="">Select type</option>
                                    @foreach (['Full-time', 'Part-time', 'Contract', 'Internship', 'Temporary'] as $type)
                                        <option value="{{ $type }}" @selected($fv('job_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('job_type')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="experience_required">Experience Required</label>
                                <input type="text" name="experience_required" id="experience_required" value="{{ $fv('experience_required') }}" class="{{ $inputClass }}" placeholder="e.g. 5+ years"/>
                                @error('experience_required')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center pt-8">
                        <button type="button" data-nav-step="2" class="flex items-center gap-2 px-8 py-2.5 rounded-xl gradient-primary text-white shadow-lg hover:shadow-indigo-200 transition-all font-title-md text-[14px]">
                            Continue to Description
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
                        </button>
                    </div>
                </section>

                {{-- Step 2: Description --}}
                <section data-step="2" class="job-step hidden glass-card p-6 rounded-2xl">
                    <h3 class="font-title-md text-title-md mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary" data-icon="description">description</span>
                        Role Description &amp; Technical Skills
                    </h3>
                    <div class="space-y-4 mb-8">
                        <label class="font-label-caps text-on-surface-variant block" for="description">Job Overview</label>
                        <div class="border border-outline-variant rounded-xl overflow-hidden">
                            <div class="bg-surface-container-low p-3 flex gap-4 border-b border-outline-variant">
                                <span class="material-symbols-outlined cursor-pointer hover:text-secondary" data-icon="format_bold">format_bold</span>
                                <span class="material-symbols-outlined cursor-pointer hover:text-secondary" data-icon="format_italic">format_italic</span>
                                <span class="material-symbols-outlined cursor-pointer hover:text-secondary" data-icon="format_list_bulleted">format_list_bulleted</span>
                                <span class="material-symbols-outlined cursor-pointer hover:text-secondary" data-icon="link">link</span>
                            </div>
                            <textarea name="description" id="description" class="w-full p-4 min-h-[300px] border-none focus:ring-0 text-body-md" placeholder="Describe the mission, responsibilities, and impact of this role..." required>{{ $fv('description') }}</textarea>
                        </div>
                        @error('description')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-4 mb-6">
                        <label class="font-label-caps text-on-surface-variant block" for="responsibilities">Key Responsibilities</label>
                        <textarea name="responsibilities" id="responsibilities" rows="4" class="{{ $inputClass }}" placeholder="List main responsibilities...">{{ $fv('responsibilities') }}</textarea>
                        @error('responsibilities')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-4 mb-8">
                        <label class="font-label-caps text-on-surface-variant block" for="requirements">Requirements</label>
                        <textarea name="requirements" id="requirements" rows="4" class="{{ $inputClass }}" placeholder="Education, certifications, must-haves...">{{ $fv('requirements') }}</textarea>
                        @error('requirements')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-4">
                        <label class="font-label-caps text-on-surface-variant block">Technical Skills &amp; Expertise</label>
                        <div id="skills-tag-container" class="flex flex-wrap gap-2 p-3 bg-surface-container-low border border-outline-variant rounded-xl focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20 transition-all">
                            @foreach ($skillTags as $tag)
                                <span class="skill-tag flex items-center gap-1 px-3 py-1 bg-secondary/10 text-secondary border border-secondary/20 rounded-full font-body-sm">
                                    <span class="skill-label">{{ $tag }}</span>
                                    <button type="button" class="skill-remove material-symbols-outlined text-[16px] cursor-pointer border-0 bg-transparent p-0 text-secondary" data-icon="close">close</button>
                                </span>
                            @endforeach
                            <input id="skill-input" class="flex-1 bg-transparent border-none focus:ring-0 text-body-sm p-0 min-w-[120px]" placeholder="Add skill..." type="text"/>
                        </div>
                        <p class="font-body-sm text-[12px] text-on-surface-variant">Press Enter to add a skill. Recommended: Kubernetes, GraphQL, System Design</p>
                        @error('skills_required')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-between items-center pt-8">
                        <button type="button" data-nav-step="1" class="flex items-center gap-2 px-6 py-2.5 rounded-xl border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-all font-title-md text-[14px]">
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_back">arrow_back</span>
                            Back to Basic Info
                        </button>
                        <button type="button" data-nav-step="3" class="flex items-center gap-2 px-8 py-2.5 rounded-xl gradient-primary text-white shadow-lg hover:shadow-indigo-200 transition-all font-title-md text-[14px]">
                            Continue to Screening
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
                        </button>
                    </div>
                </section>

                {{-- Step 3: Screening --}}
                <section data-step="3" class="job-step hidden glass-card p-6 rounded-2xl">
                    <h3 class="font-title-md text-title-md mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary" data-icon="quiz">quiz</span>
                        Screening &amp; Qualifications
                    </h3>
                    <div class="space-y-5">
                        @foreach ([1, 2, 3] as $n)
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="screening_question_{{ $n }}">Screening Question {{ $n }}</label>
                                <textarea name="screening_question_{{ $n }}" id="screening_question_{{ $n }}" rows="2" class="{{ $inputClass }}" placeholder="Optional screening question">{{ $fv('screening_question_'.$n) }}</textarea>
                                @error('screening_question_'.$n)<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="minimum_qualification">Minimum Qualification</label>
                            <textarea name="minimum_qualification" id="minimum_qualification" rows="3" class="{{ $inputClass }}" placeholder="Required qualifications">{{ $fv('minimum_qualification') }}</textarea>
                            @error('minimum_qualification')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="preferred_qualification">Preferred Qualification</label>
                            <textarea name="preferred_qualification" id="preferred_qualification" rows="3" class="{{ $inputClass }}" placeholder="Nice-to-have qualifications">{{ $fv('preferred_qualification') }}</textarea>
                            @error('preferred_qualification')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="work_mode">Work Mode</label>
                                <select name="work_mode" id="work_mode" class="{{ $inputClass }}">
                                    <option value="">Select mode</option>
                                    @foreach (['On-site', 'Hybrid', 'Remote'] as $mode)
                                        <option value="{{ $mode }}" @selected($fv('work_mode') === $mode)>{{ $mode }}</option>
                                    @endforeach
                                </select>
                                @error('work_mode')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="notice_period">Notice Period</label>
                                <input type="text" name="notice_period" id="notice_period" value="{{ $fv('notice_period') }}" class="{{ $inputClass }}" placeholder="e.g. Immediate, 30 days"/>
                                @error('notice_period')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-8">
                        <button type="button" data-nav-step="2" class="flex items-center gap-2 px-6 py-2.5 rounded-xl border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-all font-title-md text-[14px]">
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_back">arrow_back</span>
                            Back to Description
                        </button>
                        <button type="button" data-nav-step="4" class="flex items-center gap-2 px-8 py-2.5 rounded-xl gradient-primary text-white shadow-lg hover:shadow-indigo-200 transition-all font-title-md text-[14px]">
                            Continue to Budget
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
                        </button>
                    </div>
                </section>

                {{-- Step 4: Budget --}}
                <section data-step="4" class="job-step hidden glass-card p-6 rounded-2xl">
                    <h3 class="font-title-md text-title-md mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                        Budget &amp; Visibility
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="font-label-caps text-on-surface-variant block mb-2" for="salary">Salary Display Label</label>
                            <input type="text" name="salary" id="salary" value="{{ $fv('salary') }}" class="{{ $inputClass }}" placeholder="e.g. $120k – $150k per year"/>
                            @error('salary')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="min_salary">Min Salary</label>
                                <input type="number" name="min_salary" id="min_salary" value="{{ $fv('min_salary') }}" step="0.01" min="0" class="{{ $inputClass }}" placeholder="0"/>
                                @error('min_salary')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="max_salary">Max Salary</label>
                                <input type="number" name="max_salary" id="max_salary" value="{{ $fv('max_salary') }}" step="0.01" min="0" class="{{ $inputClass }}" placeholder="0"/>
                                @error('max_salary')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="currency">Currency</label>
                                <select name="currency" id="currency" class="{{ $inputClass }}">
                                    @foreach (['USD', 'EUR', 'GBP', 'INR'] as $cur)
                                        <option value="{{ $cur }}" @selected($fv('currency') === $cur)>{{ $cur }}</option>
                                    @endforeach
                                </select>
                                @error('currency')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="application_deadline">Application Deadline</label>
                                <input type="date" name="application_deadline" id="application_deadline" value="{{ $fv('application_deadline') }}" class="{{ $inputClass }}"/>
                                @error('application_deadline')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-caps text-on-surface-variant block mb-2" for="number_of_openings">Number of Openings</label>
                                <input type="number" name="number_of_openings" id="number_of_openings" value="{{ $fv('number_of_openings') }}" min="1" class="{{ $inputClass }}"/>
                                @error('number_of_openings')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-8">
                        <button type="button" data-nav-step="3" class="flex items-center gap-2 px-6 py-2.5 rounded-xl border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-all font-title-md text-[14px]">
                            <span class="material-symbols-outlined text-[18px]" data-icon="arrow_back">arrow_back</span>
                            Back to Screening
                        </button>
                        <div class="flex gap-3">
                            <button type="submit" id="btn-save-draft-footer" class="px-6 py-2.5 rounded-xl border border-outline-variant text-on-surface hover:bg-surface-container-high transition-all font-title-md text-[14px]">Save as Draft</button>
                            <button type="submit" id="btn-publish-footer" class="flex items-center gap-2 px-8 py-2.5 rounded-xl gradient-primary text-white shadow-lg hover:shadow-indigo-200 transition-all font-title-md text-[14px]">
                                {{ $isEdit ? 'Update Job' : 'Publish Job' }}
                                <span class="material-symbols-outlined text-[18px]" data-icon="check">check</span>
                            </button>
                        </div>
                    </div>
                    @error('status')<p class="mt-2 text-sm text-error">{{ $message }}</p>@enderror
                </section>
            </div>

            <div class="col-span-4 space-y-6">
                <div class="bg-secondary text-white rounded-2xl p-card-padding shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined" data-icon="auto_awesome" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                            <span class="font-label-caps text-white/80">AI Talent Insight</span>
                        </div>
                        <h4 class="font-title-md text-white mb-3">Optimize for Quality</h4>
                        <p class="font-body-sm text-white/80 leading-relaxed">
                            Roles with clearly defined "Key Performance Indicators" in the description see a 40% higher match rate from top-tier talent.
                        </p>
                        <div class="mt-6 p-4 bg-white/10 rounded-xl border border-white/20 backdrop-blur-md">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-body-sm">Market Competitiveness</span>
                                <span class="font-bold">High</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-white w-[85%]"></div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <span class="material-symbols-outlined text-[120px]" data-icon="lightbulb">lightbulb</span>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl">
                    <h4 class="font-title-md text-[16px] mb-4 text-on-surface">Posting Checklist</h4>
                    <ul class="space-y-4">
                        <li id="checklist-basic" class="flex items-center gap-3">
                            <span class="checklist-icon material-symbols-outlined text-outline" data-icon="radio_button_unchecked">radio_button_unchecked</span>
                            <span class="font-body-sm text-on-surface">Basic details complete</span>
                        </li>
                        <li id="checklist-description" class="flex items-center gap-3">
                            <span class="checklist-icon material-symbols-outlined text-outline" data-icon="radio_button_unchecked">radio_button_unchecked</span>
                            <span class="font-body-sm text-on-surface">Description refined</span>
                        </li>
                        <li id="checklist-screening" class="flex items-center gap-3 opacity-50">
                            <span class="checklist-icon material-symbols-outlined text-outline" data-icon="radio_button_unchecked">radio_button_unchecked</span>
                            <span class="font-body-sm text-on-surface">Screening questions added</span>
                        </li>
                        <li id="checklist-budget" class="flex items-center gap-3 opacity-50">
                            <span class="checklist-icon material-symbols-outlined text-outline" data-icon="radio_button_unchecked">radio_button_unchecked</span>
                            <span class="font-body-sm text-on-surface">Budget &amp; Visibility set</span>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl overflow-hidden border border-outline-variant bg-surface-container shadow-sm aspect-video flex items-center justify-center relative group cursor-pointer">
                    <img alt="Modern office space" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCkFB0KbUwrWRTLUfnrj91PqQomMAblGHd855EBSRxI99Du1Ty4THKpcevhIrXjzNyW89X-6JYmsfon8X_K_u97Pqk0cdD86DWo37QblMDaPGa0DgSRCscErtU8d3t3RFb0FbaPNtNW9O1t6OMUj3mv43PmubvrdDA8vZdKQUjUkEOTlUHpwZaUTqHIzb4P5ZfWQc_803Dz9vXYMl8QFX99l6WBJ5Dk_Tl2bGW4DsqYX4-Acm2D61-DW99fV0fJPn_fLecIOhZuviYT"/>
                    <div class="relative z-10 flex flex-col items-center">
                        <span class="material-symbols-outlined text-4xl text-on-surface" data-icon="visibility">visibility</span>
                        <span class="font-label-caps mt-2">Preview Job Page</span>
                    </div>
                </div>
            </div>
        </div>
    </form>

<footer class="py-8 border-t border-outline-variant bg-surface mt-12 -mx-8 px-8">
    <div class="flex flex-col md:flex-row justify-between items-center px-container-margin max-w-7xl mx-auto">
        <div class="flex items-center gap-6 mb-4 md:mb-0">
            <span class="font-title-md text-title-md font-bold text-primary">Elements HR</span>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-80">© 2024 Elements HR Services. All rights reserved.</p>
        </div>
        <div class="flex gap-8">
            <a class="font-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
            <a class="font-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
            <a class="font-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
        </div>
    </div>
</footer>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const initialStep = {{ $initialStep }};
    let currentStep = initialStep;
    const skills = new Set(@json($skillTags));

    const form = document.getElementById('job-post-form');
    const statusInput = document.getElementById('job_status');
    const skillsHidden = document.getElementById('skills_required');
    const stepInput = document.getElementById('form_step');
    const skillInput = document.getElementById('skill-input');
    const skillsContainer = document.getElementById('skills-tag-container');

    function syncSkillsHidden() {
        skillsHidden.value = Array.from(skills).join(',') || '';
    }

    form.addEventListener('submit', function () {
        syncSkillsHidden();
    });

    function renderSkillTag(label) {
        const span = document.createElement('span');
        span.className = 'skill-tag flex items-center gap-1 px-3 py-1 bg-secondary/10 text-secondary border border-secondary/20 rounded-full font-body-sm';
        span.innerHTML = '<span class="skill-label"></span><button type="button" class="skill-remove material-symbols-outlined text-[16px] cursor-pointer border-0 bg-transparent p-0 text-secondary" data-icon="close">close</button>';
        span.querySelector('.skill-label').textContent = label;
        span.querySelector('.skill-remove').addEventListener('click', function () {
            skills.delete(label);
            span.remove();
            syncSkillsHidden();
            updateChecklist();
        });
        skillsContainer.insertBefore(span, skillInput);
    }

    document.querySelectorAll('.skill-tag .skill-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tag = btn.closest('.skill-tag');
            const label = tag.querySelector('.skill-label').textContent.trim();
            skills.delete(label);
            tag.remove();
            syncSkillsHidden();
            updateChecklist();
        });
    });

    skillInput.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ',') return;
        e.preventDefault();
        const value = skillInput.value.trim().replace(/,$/, '');
        if (!value || skills.has(value)) {
            skillInput.value = '';
            return;
        }
        skills.add(value);
        renderSkillTag(value);
        skillInput.value = '';
        syncSkillsHidden();
        updateChecklist();
    });

    syncSkillsHidden();

    function setStepIndicator(step, state) {
        const el = document.querySelector('[data-step-indicator="' + step + '"]');
        if (!el) return;
        const circle = el.querySelector('.step-circle');
        const label = el.querySelector('.step-label');
        el.classList.remove('opacity-50', 'group');
        circle.className = 'step-circle w-10 h-10 rounded-full flex items-center justify-center font-bold';
        label.className = 'step-label font-label-caps';

        if (state === 'completed') {
            circle.classList.add('gradient-primary', 'text-white');
            circle.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check</span>';
            label.classList.add('text-secondary');
        } else if (state === 'active') {
            circle.classList.add('border-2', 'border-secondary', 'bg-secondary-fixed', 'text-secondary');
            circle.textContent = String(step);
            label.classList.add('text-secondary');
        } else {
            el.classList.add('opacity-50');
            circle.classList.add('border-2', 'border-outline-variant', 'text-outline');
            circle.textContent = String(step);
            label.classList.add('text-outline');
        }
    }

    function updateStepper(step) {
        for (let i = 1; i <= 4; i++) {
            let state = 'upcoming';
            if (i < step) state = 'completed';
            else if (i === step) state = 'active';
            setStepIndicator(i, state);
        }
        for (let c = 1; c <= 3; c++) {
            const conn = document.querySelector('[data-step-connector="' + c + '"]');
            if (!conn) continue;
            conn.className = 'flex-1 h-[2px] mx-4 ' + (c < step ? 'bg-secondary' : 'bg-outline-variant');
        }
    }

    function fieldFilled(id) {
        const el = document.getElementById(id);
        return el && String(el.value || '').trim() !== '';
    }

    function updateChecklist() {
        const basicDone = ['title', 'company_name', 'location', 'job_type'].every(fieldFilled);
        const descDone = fieldFilled('description') || skills.size > 0;
        const screeningDone = ['screening_question_1', 'screening_question_2', 'screening_question_3', 'minimum_qualification', 'preferred_qualification', 'work_mode', 'notice_period'].some(fieldFilled);
        const budgetDone = ['salary', 'min_salary', 'max_salary', 'application_deadline'].some(fieldFilled);

        setChecklistItem('checklist-basic', basicDone);
        setChecklistItem('checklist-description', descDone);
        setChecklistItem('checklist-screening', screeningDone);
        setChecklistItem('checklist-budget', budgetDone);
    }

    function setChecklistItem(id, done) {
        const li = document.getElementById(id);
        if (!li) return;
        const icon = li.querySelector('.checklist-icon');
        li.classList.toggle('opacity-50', !done);
        if (done) {
            icon.className = 'checklist-icon material-symbols-outlined text-emerald-500';
            icon.textContent = 'check_circle';
            icon.style.fontVariationSettings = "'FILL' 1";
        } else {
            icon.className = 'checklist-icon material-symbols-outlined text-outline';
            icon.textContent = 'radio_button_unchecked';
            icon.style.fontVariationSettings = '';
        }
    }

    function showStep(n) {
        currentStep = n;
        stepInput.value = String(n);
        document.querySelectorAll('.job-step').forEach(function (section) {
            section.classList.toggle('hidden', parseInt(section.getAttribute('data-step'), 10) !== n);
        });
        updateStepper(n);
        updateChecklist();
    }

    document.querySelectorAll('[data-nav-step]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            showStep(parseInt(btn.getAttribute('data-nav-step'), 10));
        });
    });

    function bindSubmitStatus(buttons, status) {
        buttons.forEach(function (btn) {
            if (!btn) return;
            btn.addEventListener('click', function () {
                statusInput.value = status;
            });
        });
    }

    bindSubmitStatus([
        document.getElementById('btn-save-draft-header'),
        document.getElementById('btn-save-draft-footer'),
    ], 'inactive');

    bindSubmitStatus([
        document.getElementById('btn-publish-header'),
        document.getElementById('btn-publish-footer'),
    ], 'active');

    form.querySelectorAll('input, textarea, select').forEach(function (el) {
        el.addEventListener('input', updateChecklist);
        el.addEventListener('change', updateChecklist);
    });

    showStep(initialStep);
})();
</script>
@endpush
