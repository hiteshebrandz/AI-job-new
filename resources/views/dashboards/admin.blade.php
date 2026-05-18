@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('body-class', 'bg-background text-on-background font-body-md min-h-screen')

@section('page-css', 'admin_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@section('content')
<main class="min-h-screen p-container-margin max-w-[1440px] mx-auto">
    <header class="flex justify-between items-center mb-10 py-8">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-primary">Super Admin Dashboard</h1>
            <p class="font-body-md text-on-surface-variant">Platform overview and account management</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.analytics') }}" class="px-4 py-2 text-secondary font-label-caps hover:underline">Analytics UI</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 border border-outline-variant rounded-lg font-label-caps hover:bg-surface-container-high transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <div class="grid md:grid-cols-2 gap-6 mb-10">
        <div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant">
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2">Total Users</p>
            <p class="font-headline-lg text-headline-lg text-primary">{{ $totalUsers }}</p>
        </div>
        <div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant">
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2">Total HR Accounts</p>
            <p class="font-headline-lg text-headline-lg text-primary">{{ $totalHr }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-card-padding border-b border-outline-variant">
                <h2 class="font-title-md text-title-md text-primary">All Users</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">Name</th>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($users as $account)
                            <tr class="hover:bg-surface-container-low/50">
                                <td class="px-6 py-4 font-body-md">{{ $account->name }}</td>
                                <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $account->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center font-body-sm text-on-surface-variant">No users registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-card-padding border-b border-outline-variant">
                <h2 class="font-title-md text-title-md text-primary">All HR Accounts</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">Name</th>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($hrs as $account)
                            <tr class="hover:bg-surface-container-low/50">
                                <td class="px-6 py-4 font-body-md">{{ $account->name }}</td>
                                <td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $account->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center font-body-sm text-on-surface-variant">No HR accounts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>
@endsection
