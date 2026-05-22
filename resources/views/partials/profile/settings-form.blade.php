@php
    $photoUrl = $user->profilePhotoUrl();
    $routes = $routes ?? [];
@endphp

@if (session('status'))
    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-8 animate-fade-in">
    <span class="badge-violet text-[11px]">Account</span>
    <h2 class="text-[28px] font-extrabold mt-2" style="color: var(--text-primary);">Account Settings</h2>
    <p class="text-sm mt-1" style="color: var(--text-muted);">View and update your account details, photo, and password.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Photo card --}}
    <div class="glass-card p-6 rounded-2xl lg:col-span-1">
        <h3 class="text-sm font-bold uppercase tracking-widest mb-4" style="color: var(--text-muted);">Profile Photo</h3>
        <div class="flex flex-col items-center text-center">
            <div class="w-28 h-28 rounded-2xl overflow-hidden flex items-center justify-center mb-4 ring-2 ring-secondary/30
                {{ $photoUrl ? '' : 'gradient-violet' }}">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Profile photo" class="w-full h-full object-cover" id="profile-photo-preview">
                @else
                    <span class="text-white font-bold text-3xl" id="profile-photo-initials">{{ $user->initials() }}</span>
                @endif
            </div>
            <p class="text-sm font-semibold truncate w-full" style="color: var(--text-primary);">{{ $user->name }}</p>
            <p class="text-xs mt-0.5" style="color: var(--text-muted);">{{ $user->roleLabel() }}</p>
            <p class="text-xs mt-1 truncate w-full" style="color: var(--text-muted);">{{ $user->email }}</p>

            <form action="{{ $routes['photo'] ?? '#' }}" method="POST" enctype="multipart/form-data" class="w-full mt-5 space-y-3">
                @csrf
                <label class="block w-full cursor-pointer border border-dashed border-outline-variant hover:border-secondary rounded-xl py-3 px-4 text-xs transition-colors">
                    <span class="material-symbols-outlined text-[18px] align-middle mr-1">upload</span>
                    Choose photo (JPG, PNG, max 2MB)
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
            @if ($photoUrl)
                <form action="{{ $routes['removePhoto'] ?? '#' }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-xs py-2 rounded-lg border border-outline-variant hover:border-red-500/50 hover:text-red-400 transition-colors">
                        Remove photo
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        {{-- Account details --}}
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest mb-5" style="color: var(--text-muted);">Account Details</h3>
            <form action="{{ $routes['update'] ?? '#' }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Full name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Role</label>
                        <input type="text" value="{{ $user->roleLabel() }}" disabled
                            class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-2.5 text-sm opacity-70 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Member since</label>
                        <input type="text" value="{{ $user->created_at?->format('M j, Y') }}" disabled
                            class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-2.5 text-sm opacity-70 cursor-not-allowed">
                    </div>
                    @if ($user->isUser() && $candidate?->candidate_code)
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Candidate ID</label>
                            <input type="text" value="{{ $candidate->candidate_code }}" disabled
                                class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-2.5 text-sm opacity-70 cursor-not-allowed">
                        </div>
                    @endif
                </div>

                @if ($user->isUser())
                    <div class="border-t border-outline-variant pt-5 mt-2">
                        <h4 class="text-sm font-semibold mb-4" style="color: var(--text-primary);">Candidate profile</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Display name on resume</label>
                                <input type="text" name="full_name" value="{{ old('full_name', $candidate?->full_name) }}"
                                    class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Current title</label>
                                <input type="text" name="current_title" value="{{ old('current_title', $candidate?->current_title) }}"
                                    class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Location</label>
                                <input type="text" name="location" value="{{ old('location', $candidate?->location) }}"
                                    class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Years of experience</label>
                                <input type="number" name="experience_years" min="0" max="50" value="{{ old('experience_years', $candidate?->experience_years) }}"
                                    class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Professional summary</label>
                                <textarea name="summary" rows="3"
                                    class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">{{ old('summary', $candidate?->summary) }}</textarea>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest mb-5" style="color: var(--text-muted);">Change Password</h3>
            <form action="{{ $routes['password'] ?? '#' }}" method="POST" class="space-y-4 max-w-md">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Current password</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                        class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">New password</label>
                    <input type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-muted);">Confirm new password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl border border-outline-variant bg-surface px-4 py-2.5 text-sm focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <button type="submit" class="border border-outline-variant px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Update password
                </button>
            </form>
        </div>

        @if ($user->isUser())
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('user.settings.notifications') }}" class="text-sm flex items-center gap-2 text-secondary hover:underline">
                    <span class="material-symbols-outlined text-[18px]">notifications</span> Notification settings
                </a>
                <a href="{{ route('user.resume.upload') }}" class="text-sm flex items-center gap-2 text-secondary hover:underline">
                    <span class="material-symbols-outlined text-[18px]">description</span> Resume upload
                </a>
            </div>
        @elseif ($user->isHr())
            <a href="{{ route('hr.settings.notifications') }}" class="text-sm flex items-center gap-2 text-secondary hover:underline">
                <span class="material-symbols-outlined text-[18px]">notifications</span> Notification settings
            </a>
        @endif
    </div>
</div>
