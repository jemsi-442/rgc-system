<!-- Table Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Waumini
            <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                {{ $members->total() }} waumini
            </span>
        </h3>
    </div>
    <div class="mt-3 sm:mt-0">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="fas fa-info-circle text-primary-500"></i>
            <span>Waumini {{ $members->firstItem() }} - {{ $members->lastItem() }} ya {{ $members->total() }}</span>
        </div>
    </div>
</div>

    <!-- Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="bg-primary-600 text-white text-sm">
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-hashtag mr-2"></i>
                        Namba
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Jina
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-venus-mars mr-2"></i>
                        Jinsia
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        Simu
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Email
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Uanachama
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-circle mr-2"></i>
                        Hali
                    </div>
                </th>
                @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                    <div class="flex items-center">
                        <i class="fas fa-cogs mr-2"></i>
                        Vitendo
                    </div>
                </th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($members as $index => $member)
            <tr class="bg-white hover:bg-gray-50 transition-all duration-200 member-row"
                data-name="{{ strtolower($member->first_name . ' ' . ($member->middle_name ?? '') . ' ' . $member->last_name) }}"
                data-phone="{{ strtolower($member->phone ?? '') }}"
                data-email="{{ strtolower($member->email ?? '') }}"
                data-member-number="{{ strtolower($member->member_number ?? '') }}"
                data-gender="{{ $member->gender }}"
                data-age-group="{{ $member->age_group ?? '' }}"
                data-special-group="{{ $member->special_group ?? '' }}"
                data-marital-status="{{ $member->marital_status ?? '' }}"
                data-is-active="{{ $member->is_active ? '1' : '0' }}">
                
                <!-- Member Number -->
                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                    <div class="flex items-center gap-2">
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $member->member_number }}</span>
                        <button onclick="viewQrCode('{{ $member->id }}', '{{ $member->member_number }}')"
                                class="text-primary-600 hover:text-primary-800 transition-colors"
                                title="Angalia QR Code">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                </td>

                <!-- Name -->
                <td class="py-4 px-6">
                    <div class="text-sm text-gray-900">
                        {{ $member->first_name }} {{ $member->middle_name ?? '' }} {{ $member->last_name }}
                    </div>
                </td>

                <!-- Gender -->
                <td class="py-4 px-6 text-sm text-gray-600">
                    @if($member->gender == 'Mme')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-male mr-1"></i> Me
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                            <i class="fas fa-female mr-1"></i> Ke
                        </span>
                    @endif
                </td>

                <!-- Phone -->
                <td class="py-4 px-6 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-phone text-gray-400"></i>
                        {{ $member->phone }}
                    </div>
                </td>

                <!-- Email -->
                <td class="py-4 px-6 text-sm text-gray-600">
                    <div class="flex items-center gap-2 max-w-[180px]">
                        <i class="fas fa-envelope text-gray-400 flex-shrink-0"></i>
                        @if($member->email)
                            <span class="truncate" title="{{ $member->email }}">{{ $member->email }}</span>
                        @else
                            <span>-</span>
                        @endif
                    </div>
                </td>

                <!-- Membership Date -->
                <td class="py-4 px-6 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                        {{ \Carbon\Carbon::parse($member->membership_date)->format('d/m/Y') }}
                    </div>
                </td>

                <!-- Status -->
                <td class="py-4 px-6 text-sm">
                    @if($member->is_active)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                            <i class="fas fa-check-circle mr-1"></i>Hai
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                            <i class="fas fa-clock mr-1"></i>Inasubiri Idhini
                        </span>
                    @endif
                </td>

                <!-- Actions -->
                @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                    <div class="flex items-center space-x-2">
                        <!-- View Button -->
                        <a href="{{ route('members.show', $member->id) }}"
                           class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200"
                           title="Angalia Maelezo">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        
                        <!-- Edit Button -->
                        <a href="{{ route('members.edit', $member->id) }}"
                           class="h-8 w-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-200 transition-all duration-200"
                           title="Hariri">
                            <i class="fas fa-pencil-alt text-sm"></i>
                        </a>
                        
                        <!-- Activate/Deactivate Button -->
                        @if($member->is_active)
                        <button type="button"
                                onclick="confirmAction('deactivate', {{ $member->id }}, '{{ $member->first_name }} {{ $member->last_name }}')"
                                class="h-8 w-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center hover:bg-yellow-200 transition-all duration-200"
                                title="Simamisha">
                            <i class="fas fa-user-slash text-sm"></i>
                        </button>
                        @else
                        <button type="button"
                                onclick="confirmAction('activate', {{ $member->id }}, '{{ $member->first_name }} {{ $member->last_name }}')"
                                class="h-8 w-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-all duration-200"
                                title="Anzisha">
                            <i class="fas fa-user-check text-sm"></i>
                        </button>
                        @endif
                        
                        <!-- Delete Button -->
                        <button type="button"
                                onclick="confirmAction('delete', {{ $member->id }}, '{{ $member->first_name }} {{ $member->last_name }}')"
                                class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200"
                                title="Futa">
                            <i class="fas fa-trash text-sm"></i>
                        </button>

                        <!-- Hidden Forms -->
                        <form id="deactivate-form-{{ $member->id }}" action="{{ route('members.deactivate', $member->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        <form id="activate-form-{{ $member->id }}" action="{{ route('members.activate', $member->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        <form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ Auth::user()->isMchungaji() || Auth::user()->isMhasibu() ? 8 : 7 }}" class="py-12 px-6 text-center">
                    <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna waumini waliopatikana</h3>
                    <p class="text-gray-500 {{ Auth::user()->isMchungaji() || Auth::user()->isMhasibu() ? 'mb-6' : '' }}">Hakuna waumini wanaolingana na vichujio vyako.</p>
                    @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                    <a href="{{ route('members.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                        <i class="fas fa-user-plus mr-2"></i> Sajili Muumini Mpya
                    </a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
@if($members->hasPages())
<div class="mt-6 pt-6 border-t border-gray-200">
    {{ $members->links() }}
</div>
@endif

<script>
// Ensure these functions are available when the partial is loaded via AJAX
if (typeof confirmAction === 'undefined') {
    // These functions will be available from the main page
    console.log('Functions loaded from main page');
}
</script>
