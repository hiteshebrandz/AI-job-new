<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 space-y-8">
<section class="glass-card rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3 mb-6">
<div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined" data-icon="person">person</span>
</div>
<div>
<h3 class="font-title-md text-title-md">Candidate Alerts</h3>
<p class="font-body-sm text-on-surface-variant">Settings for your job applications and matches</p>
</div>
</div>
<div class="space-y-6">
@foreach ([
    ['id' => 'notify_applications', 'label' => 'Application status updates', 'desc' => 'When recruiters change your application status'],
    ['id' => 'notify_matches', 'label' => 'New job matches', 'desc' => 'When AI finds roles that match your profile'],
    ['id' => 'notify_interviews', 'label' => 'Interview reminders', 'desc' => 'Upcoming interview and schedule changes'],
] as $toggle)
<div class="flex items-center justify-between gap-4 py-2 border-b border-outline-variant last:border-0">
<div>
<p class="font-body-md text-primary font-semibold">{{ $toggle['label'] }}</p>
<p class="font-body-sm text-on-surface-variant">{{ $toggle['desc'] }}</p>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input type="checkbox" id="{{ $toggle['id'] }}" class="sr-only toggle-checkbox" checked/>
<div class="toggle-label w-11 h-6 bg-outline-variant rounded-full transition-colors">
<div class="toggle-dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform"></div>
</div>
</label>
</div>
@endforeach
</div>
</section>

@if (auth()->user()->isHr())
<section class="glass-card rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3 mb-6">
<div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
<span class="material-symbols-outlined" data-icon="business_center">business_center</span>
</div>
<div>
<h3 class="font-title-md text-title-md">Recruiter Alerts</h3>
<p class="font-body-sm text-on-surface-variant">Settings for your hiring pipeline</p>
</div>
</div>
<div class="space-y-6">
@foreach ([
    ['id' => 'notify_new_applicants', 'label' => 'New applicants', 'desc' => 'When a candidate applies to your jobs'],
    ['id' => 'notify_pipeline', 'label' => 'Pipeline updates', 'desc' => 'Shortlist, interview, and hire activity'],
] as $toggle)
<div class="flex items-center justify-between gap-4 py-2 border-b border-outline-variant last:border-0">
<div>
<p class="font-body-md text-primary font-semibold">{{ $toggle['label'] }}</p>
<p class="font-body-sm text-on-surface-variant">{{ $toggle['desc'] }}</p>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input type="checkbox" id="{{ $toggle['id'] }}" class="sr-only toggle-checkbox" checked/>
<div class="toggle-label w-11 h-6 bg-outline-variant rounded-full transition-colors relative">
<div class="toggle-dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform"></div>
</div>
</label>
</div>
@endforeach
</div>
</section>
@endif

<button type="button" class="px-8 py-3 bg-secondary text-white rounded-xl font-title-md hover:opacity-90 transition-opacity">Save Preferences</button>
</div>

<aside class="lg:col-span-4">
<div class="glass-card rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] sticky top-24">
<h3 class="font-title-md text-title-md text-primary mb-4">Stay in the loop</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Turn on application alerts to get notified when recruiters update your status — including shortlist and interview invites.</p>
<div class="flex items-start gap-3 p-4 rounded-xl bg-secondary/5 border border-secondary/20">
<span class="material-symbols-outlined text-secondary">info</span>
<p class="font-body-sm text-on-surface-variant">Status notifications appear in your header bell icon.</p>
</div>
</div>
</aside>
</div>
