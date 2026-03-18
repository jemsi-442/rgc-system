@extends('layouts.app')

@section('title', 'Waumini - Mfumo wa Kanisa')

@section('page-title', 'Waumini')
@section('page-subtitle', 'Usimamizi wa waumini wa kanisa')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg mb-6 shadow-sm" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg mb-6 shadow-sm" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<!-- Header with Export and Add Buttons -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-lg font-medium text-gray-700 flex items-center">
        <i class="fas fa-users text-primary-500 mr-2"></i> Orodha ya Waumini
        <span class="ml-2 text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $members->total() }} waumini</span>
    </h3>

    <div class="flex items-center space-x-3">
        <!-- Add Member Button -->
        <button onclick="switchToAddTab()"
                class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600 transition-all duration-200 flex items-center">
            <i class="fas fa-plus mr-2"></i> Ongeza Muumini
        </button>

        <!-- Export Button -->
        <button onclick="exportMembers()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center">
            <i class="fas fa-download mr-2"></i> Pakua
        </button>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="mb-6">
    <div class="flex space-x-4 border-b border-gray-200" role="tablist">
        <button id="allMembersTab" class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-t-md focus:outline-none focus:ring-2 focus:ring-primary-300 transition-all duration-200" role="tab" aria-selected="true" aria-controls="membersTableContainer">
            Waumini Wote
        </button>
        <button id="addMemberTab" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-t-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-all duration-200" role="tab" aria-selected="false" aria-controls="addMemberFormContainer">
            Ongeza Muumini
        </button>
    </div>
</div>

<!-- Members Table Container -->
<div id="membersTableContainer" class="block">
    @fragment('membersTable')
    <!-- Filter Section -->
    <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4" id="filterSection">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-medium text-gray-700 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Tafuta Waumini
            </h3>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchMember" class="pl-10 w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white shadow-sm text-gray-900 placeholder-gray-500" placeholder="Tafuta kwa jina, namba ya simu..." value="{{ $search ?? '' }}">
                </div>
                <select id="statusFilter" class="bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 py-2 px-3 text-sm w-full sm:w-48">
                    <option value="">Hali Zote</option>
                    <option value="active" {{ $request->input('status') == 'active' ? 'selected' : '' }}>Anaitikia</option>
                    <option value="inactive" {{ $request->input('status') == 'inactive' ? 'selected' : '' }}>Haitikii</option>
                    <option value="transferred" {{ $request->input('status') == 'transferred' ? 'selected' : '' }}>Uhamisho</option>
                </select>
                <select id="genderFilter" class="bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 py-2 px-3 text-sm w-full sm:w-48">
                    <option value="">Jinsia Zote</option>
                    <option value="male" {{ $request->input('gender') == 'male' ? 'selected' : '' }}>Mwanaume</option>
                    <option value="female" {{ $request->input('gender') == 'female' ? 'selected' : '' }}>Mwanamke</option>
                </select>
                <button onclick="clearFilters()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Futa Uchaguzi
                </button>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-50 to-primary-100 text-gray-700 text-sm">
                        <th class="py-3.5 px-6 text-left font-semibold">Jina Kamili</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Namba ya Simu</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Jinsia</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Umri</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Kikundi</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Hali</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="membersTableBody" class="divide-y divide-gray-100">
                    @foreach($members as $member)
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-red-100 text-red-800',
                                'transferred' => 'bg-gray-100 text-gray-800'
                            ];
                            $statusColor = $statusColors[$member->status ?? 'active'] ?? 'bg-gray-100 text-gray-800';
                            
                            $genderColors = [
                                'male' => 'bg-blue-100 text-blue-800',
                                'female' => 'bg-pink-100 text-pink-800'
                            ];
                            $genderColor = $genderColors[$member->gender ?? 'male'] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <tr class="bg-white hover:bg-gray-50 transition-all duration-200 member-row"
                            data-name="{{ strtolower($member->full_name) }}"
                            data-phone="{{ strtolower($member->phone) }}"
                            data-gender="{{ strtolower($member->gender) }}"
                            data-status="{{ strtolower($member->status) }}"
                            data-group="{{ strtolower($member->group) }}">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-medium text-primary-800">{{ substr($member->full_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $member->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->email ?? 'Hakuna barua pepe' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-900 font-mono">{{ $member->phone }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $genderColor }}">
                                    {{ $member->gender == 'male' ? 'Mwanaume' : 'Mwanamke' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-700">{{ $member->age ?? 'N/A' }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-800">
                                    {{ $member->group ?? 'Hakuna Kikundi' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($member->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span> Anaitikia
                                    </span>
                                @elseif($member->status == 'inactive')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span> Haitikii
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-2 h-2 bg-gray-500 rounded-full mr-1.5"></span> Uhamisho
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewMemberDetails('{{ $member->id }}')" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-md hover:bg-blue-50 transition-all duration-200" title="Angalia Maelezo">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button onclick="editMember('{{ $member->id }}')" class="text-primary-600 hover:text-primary-800 p-1.5 rounded-md hover:bg-primary-50 transition-all duration-200" title="Hariri Muumini">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="toggleStatus('{{ $member->id }}', '{{ $member->status }}')" class="text-gray-600 hover:text-gray-800 p-1.5 rounded-md hover:bg-gray-50 transition-all duration-200" title="{{ $member->status === 'active' ? 'Zima' : 'Washa' }} Muumini">
                                        <i class="fas {{ $member->status === 'active' ? 'fa-power-off' : 'fa-play' }} text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4" id="paginationContainer">
            {{ $members->links() }}
        </div>
    </div>
    @endfragment
</div>

<!-- Add Member Form -->
<div id="addMemberFormContainer" class="hidden">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Ongeza Muumini Mpya</h3>
        <form id="addMemberForm" action="{{ route('members.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Taarifa Binafsi</h4>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Jina Kamili *</label>
                        <input type="text" name="full_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Barua Pepe</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Namba ya Simu *</label>
                        <input type="text" name="phone" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Jinsia *</label>
                        <select name="gender" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                            <option value="">Chagua Jinsia</option>
                            <option value="male">Mwanaume</option>
                            <option value="female">Mwanamke</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Tarehe ya Kuzaliwa</label>
                        <input type="date" name="date_of_birth" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                    </div>
                </div>

                <!-- Church Information -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Taarifa za Kanisa</h4>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Kikundi/Jumuiya</label>
                        <select name="group" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                            <option value="">Chagua Kikundi</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->name }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Idara ya Huduma</label>
                        <select name="department" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                            <option value="">Chagua Idara</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Tarehe ya Kujiunga</label>
                        <input type="date" name="join_date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Hali *</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                            <option value="active">Anaitikia</option>
                            <option value="inactive">Haitikii</option>
                            <option value="transferred">Uhamisho</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4 col-span-1 md:col-span-2">
                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Taarifa Zaidi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Anwani</label>
                            <input type="text" name="address" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Kazi</label>
                            <input type="text" name="occupation" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Namba ya Kitambulisho</label>
                            <input type="text" name="id_number" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Aina ya Kitambulisho</label>
                            <select name="id_type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                                <option value="">Chagua Aina</option>
                                <option value="nida">NIDA</option>
                                <option value="passport">Pasipoti</option>
                                <option value="driving_license">Leseni ya Udereva</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Maelezo/Maoni</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" placeholder="Andika maelezo yoyote ya ziada..."></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="switchToAllTab()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Ghairi</button>
                <button type="submit" id="addMemberSubmit" class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600">
                    <i class="fas fa-save mr-2"></i> Hifadhi Muumini
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Status Confirm Modal -->
<div id="statusConfirmModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Thibitisha Mabadiliko</h3>
                        <p class="text-sm text-gray-600">Badilisha hali ya muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('statusConfirmModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl"></i>
            </div>
            <p id="statusConfirmMessage" class="text-center text-gray-700 mb-2 text-lg font-medium"></p>
            <p class="text-center text-gray-500 text-sm">Kitendo hiki kitabadilisha hali ya muumini mara moja.</p>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
            <button onclick="closeModal('statusConfirmModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <button id="confirmStatusAction" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all duration-200">
                Thibitisha Mabadiliko
            </button>
        </div>
    </div>
</div>

<!-- View Member Modal -->
<div id="viewMemberModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-eye text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo ya Muumini</h3>
                        <p class="text-sm text-gray-600">Taarifa kamili za muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('viewMemberModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div id="viewMemberContent"></div>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end">
            <button onclick="closeModal('viewMemberModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div id="editMemberModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-edit text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hariri Muumini</h3>
                        <p class="text-sm text-gray-600">Badilisha taarifa za muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('editMemberModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div id="editMemberContent"></div>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editMemberModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <button type="submit" form="editMemberForm" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Hifadhi Mabadiliko</span>
            </button>
        </div>
    </div>
</div>
<script>
// Tab switching functionality
function switchToAddTab() {
    document.getElementById('allMembersTab').classList.remove('bg-primary-500', 'text-white');
    document.getElementById('allMembersTab').classList.add('bg-gray-100', 'text-gray-700');
    document.getElementById('addMemberTab').classList.remove('bg-gray-100', 'text-gray-700');
    document.getElementById('addMemberTab').classList.add('bg-primary-500', 'text-white');
    
    document.getElementById('membersTableContainer').classList.add('hidden');
    document.getElementById('addMemberFormContainer').classList.remove('hidden');
}

function switchToAllTab() {
    document.getElementById('addMemberTab').classList.remove('bg-primary-500', 'text-white');
    document.getElementById('addMemberTab').classList.add('bg-gray-100', 'text-gray-700');
    document.getElementById('allMembersTab').classList.remove('bg-gray-100', 'text-gray-700');
    document.getElementById('allMembersTab').classList.add('bg-primary-500', 'text-white');
    
    document.getElementById('addMemberFormContainer').classList.add('hidden');
    document.getElementById('membersTableContainer').classList.remove('hidden');
}

// Tab click event listeners
document.getElementById('allMembersTab').addEventListener('click', switchToAllTab);
document.getElementById('addMemberTab').addEventListener('click', switchToAddTab);

// Filter functionality
let filterTimeout;

function filterMembers() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        const search = document.getElementById('searchMember').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const gender = document.getElementById('genderFilter').value;
        
        const rows = document.querySelectorAll('.member-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const phone = row.getAttribute('data-phone');
            const rowGender = row.getAttribute('data-gender');
            const rowStatus = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(search) || phone.includes(search);
            const matchesStatus = !status || rowStatus === status;
            const matchesGender = !gender || rowGender === gender;
            
            if (matchesSearch && matchesStatus && matchesGender) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }, 300);
}

// Event listeners for filters
document.getElementById('searchMember').addEventListener('input', filterMembers);
document.getElementById('statusFilter').addEventListener('change', filterMembers);
document.getElementById('genderFilter').addEventListener('change', filterMembers);

// Clear filters
function clearFilters() {
    document.getElementById('searchMember').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('genderFilter').value = '';
    filterMembers();
}

// Modal functionality
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
});

// Status toggle functionality
let currentMemberId = null;
let currentStatus = null;

function toggleStatus(memberId, status) {
    currentMemberId = memberId;
    currentStatus = status;
    
    const message = status === 'active' 
        ? 'Unahakika unataka kumzima muumini huyu? Hata hivyo, anaweza kuwekwa tena kwenye hali ya kuitikia wakati wowote.'
        : 'Unahakika unataka kumwamsha muumini huyu? Hii itamweka kwenye hali ya kuitikia.';
    
    document.getElementById('statusConfirmMessage').textContent = message;
    openModal('statusConfirmModal');
}

// Confirm status change
document.getElementById('confirmStatusAction').addEventListener('click', function() {
    if (currentMemberId) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        fetch(`/members/${currentMemberId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                showError('Hitilafu imetokea wakati wa kubadilisha hali ya muumini.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Hitilafu imetokea wakati wa kubadilisha hali ya muumini.');
        })
        .finally(() => {
            closeModal('statusConfirmModal');
        });
    }
});

// View member details
function viewMemberDetails(memberId) {
    // Redirect to member show page
    window.location.href = '/panel/members/' + memberId;
}

// Edit member
function editMember(memberId) {
    // Redirect to member edit page
    window.location.href = '/panel/members/' + memberId + '/edit';
}

// Export members
function exportMembers() {
    const search = document.getElementById('searchMember').value;
    const status = document.getElementById('statusFilter').value;
    const gender = document.getElementById('genderFilter').value;

    let url = '/panel/members/export?';
    const params = new URLSearchParams();

    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (gender) params.append('gender', gender);

    showInfo('Orodha ya waumini inapakuliwa...', 'Inapakua');

    // Redirect to export
    window.location.href = url + params.toString();
}

// Form submission handling
document.getElementById('addMemberForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('addMemberSubmit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inahifadhi...';
});

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.onclick) {
                this.classList.add('transform', 'scale-95');
                setTimeout(() => {
                    this.classList.remove('transform', 'scale-95');
                }, 150);
            }
        });
    });
    
    // Add hover effects for table rows
    const tableRows = document.querySelectorAll('.member-row');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        openModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
    
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchMember').focus();
    }
});

// Responsive adjustments
function handleResize() {
    const modals = document.querySelectorAll('.modal-content');
    const isMobile = window.innerWidth < 768;
    
    modals.forEach(modal => {
        if (isMobile) {
            modal.classList.add('w-11/12');
            modal.classList.remove('max-w-md', 'max-w-4xl');
        }
    });
}

window.addEventListener('resize', handleResize);
handleResize(); // Initial call
</script>
@endsection