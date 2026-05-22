<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilePasswordRequest;
use App\Http\Requests\UpdateProfilePhotoRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Candidate;
use App\Models\User;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load('candidate');

        $routePrefix = match ($user->role) {
            User::ROLE_HR    => 'hr.profile',
            User::ROLE_ADMIN => 'admin.profile',
            default          => 'user.profile',
        };

        $layout = $this->layoutFor($user);
        $viewName = match ($layout) {
            'employer' => 'pages.profile.employer',
            'admin'    => 'pages.profile.admin',
            default    => 'pages.profile.candidate',
        };

        return view($viewName, [
            'user'       => $user,
            'candidate'  => $user->candidate,
            'activeNav'  => 'profile',
            'profileUrl' => AuthRedirect::profileRouteFor($user),
            'routes'     => [
                'update'      => route($routePrefix . '.update'),
                'password'    => route($routePrefix . '.password'),
                'photo'       => route($routePrefix . '.photo'),
                'removePhoto' => route($routePrefix . '.photo.remove'),
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        if ($user->isUser()) {
            $this->syncCandidateProfile($user, $request->only([
                'full_name', 'location', 'current_title', 'experience_years', 'summary',
            ]));
        }

        return redirect()
            ->to(AuthRedirect::profileRouteFor($user))
            ->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()
            ->to(AuthRedirect::profileRouteFor($user))
            ->with('status', 'Password changed successfully.');
    }

    public function updatePhoto(UpdateProfilePhotoRequest $request): RedirectResponse
    {
        $user = $request->user();
        $file = $request->file('photo');
        $disk = 'public';
        $dir  = 'avatars/' . date('Y/m');
        $name = $user->id . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        if ($user->profile_photo_path) {
            Storage::disk($disk)->delete($user->profile_photo_path);
        }

        $path = $file->storeAs($dir, $name, $disk);

        $user->update(['profile_photo_path' => $path]);

        return redirect()
            ->to(AuthRedirect::profileRouteFor($user))
            ->with('status', 'Profile photo updated.');
    }

    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        return redirect()
            ->to(AuthRedirect::profileRouteFor($user))
            ->with('status', 'Profile photo removed.');
    }

    private function layoutFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_HR    => 'employer',
            User::ROLE_ADMIN => 'admin',
            default          => 'candidate',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncCandidateProfile(User $user, array $data): void
    {
        $candidate = $user->candidate;

        if (! $candidate) {
            $candidate = Candidate::create([
                'user_id'        => $user->id,
                'candidate_code' => Candidate::generateCode(),
                'full_name'      => $data['full_name'] ?: $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
            ]);
        }

        $updates = array_filter([
            'full_name'        => $data['full_name'] ?? null,
            'email'            => $user->email,
            'phone'            => $user->phone,
            'location'         => $data['location'] ?? null,
            'current_title'    => $data['current_title'] ?? null,
            'experience_years' => isset($data['experience_years']) ? (int) $data['experience_years'] : null,
            'summary'          => $data['summary'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        if ($updates !== []) {
            $candidate->update($updates);
        }
    }
}
