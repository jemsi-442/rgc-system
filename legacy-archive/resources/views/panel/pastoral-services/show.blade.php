@extends('layouts.app')

@section('title', 'Taarifa za Huduma - Mfumo wa Kanisa')
@section('page-title', 'Taarifa za Huduma ya Kichungaji')
@section('page-subtitle', 'Angalia maelezo kamili ya ombi la huduma')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Taarifa za Huduma</h1>
            <p class="text-gray-600 mt-2">Angalia maelezo kamili ya ombi la huduma ya kichungaji</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($service->status == 'Inasubiri')
            <a href="{{ route('pastoral-services.edit', $service->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
            @endif
            <a href="{{ route('pastoral-services.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Service Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-hands-praying text-white text-5xl"></i>
                </div>

                <!-- Service Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ $service->service_type }}</h2>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hashtag text-white opacity-80"></i>
                                    <span class="text-lg opacity-90">{{ $service->service_number }}</span>
                                </div>
                                @if($service->status == 'Inasubiri')
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-clock mr-1.5"></i>Inasubiri
                                    </span>
                                @elseif($service->status == 'Imeidhinishwa')
                                    <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1.5"></i>Imeidhinishwa
                                    </span>
                                @elseif($service->status == 'Imekamilika')
                                    <span class="inline-flex items-center px-3 py-1 bg-purple-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-double mr-1.5"></i>Imekamilika
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1.5"></i>Imekataliwa
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Service Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Member Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Muumini</h3>
                        <p class="text-sm text-gray-600">Muumini aliyeomba huduma</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Member Name -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jina la Muumini</p>
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $service->member->first_name }} {{ $service->member->middle_name }} {{ $service->member->last_name }}</p>
                        </div>
                    </div>

                    <!-- Member Number -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Muumini</p>
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $service->member->member_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Huduma</h3>
                        <p class="text-sm text-gray-600">Maelezo ya ombi la huduma</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Type -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Aina ya Huduma</p>
                        <div class="flex items-center">
                            <i class="fas fa-hands-praying text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $service->service_type }}</p>
                        </div>
                    </div>

                    <!-- Preferred Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe Inayopendelewa</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">
                                {{ $service->preferred_date ? \Carbon\Carbon::parse($service->preferred_date)->format('d/m/Y') : 'Haijachaguliwa' }}
                            </p>
                        </div>
                    </div>

                    <!-- Request Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Kuomba</p>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Hali</p>
                        <div class="flex items-center">
                            @if($service->status == 'Inasubiri')
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                <p class="text-base font-medium text-yellow-600">Inasubiri</p>
                            @elseif($service->status == 'Imeidhinishwa')
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-base font-medium text-green-600">Imeidhinishwa</p>
                            @elseif($service->status == 'Imekamilika')
                                <i class="fas fa-check-double text-purple-500 mr-2"></i>
                                <p class="text-base font-medium text-purple-600">Imekamilika</p>
                            @else
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                <p class="text-base font-medium text-red-600">Imekataliwa</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($service->description)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-align-left text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo Zaidi</h3>
                        <p class="text-sm text-gray-600">Maelezo kamili kuhusu ombi hili</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-quote-left text-primary-500 mr-2 mt-1"></i>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $service->description }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Admin Notes Card -->
            @if($service->admin_notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-comment-dots text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maoni ya Msimamizi</h3>
                        <p class="text-sm text-gray-600">Maoni kutoka kwa mchungaji/msimamizi</p>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $service->admin_notes }}</p>
                            @if($service->approver)
                            <p class="text-sm text-blue-600 mt-3">
                                - {{ $service->approver->name }} ({{ \Carbon\Carbon::parse($service->approved_at)->format('d/m/Y H:i') }})
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Actions -->
        <div class="space-y-6">
            <!-- Quick Stats Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Muhtasari</h3>
                        <p class="text-sm text-gray-600">Taarifa za haraka za ombi</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Service Type -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-hands-praying text-purple-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Aina ya Huduma</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $service->service_type }}</span>
                    </div>

                    <!-- Request Date -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Tarehe ya Ombi</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y') }}</span>
                    </div>

                    <!-- Time Since Request -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Muda Tangu Kuomba</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($service->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions Card for Admin/Pastor -->
            @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                @if($service->status == 'Inasubiri')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tasks text-primary-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Vitendo vya Msimamizi</h3>
                            <p class="text-sm text-gray-600">Chagua kitendo kwa ombi hili</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="openModal('approveServiceModal')" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                            <i class="fas fa-check"></i>
                            <span class="font-medium">Idhinisha Huduma</span>
                        </button>
                        <button onclick="openModal('rejectServiceModal')" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700">
                            <i class="fas fa-times"></i>
                            <span class="font-medium">Kataa Huduma</span>
                        </button>
                    </div>
                </div>
                @elseif($service->status == 'Imeidhinishwa')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tasks text-primary-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Vitendo vya Msimamizi</h3>
                            <p class="text-sm text-gray-600">Thibitisha ukamilishaji wa huduma</p>
                        </div>
                    </div>

                    <button onclick="openModal('completeServiceModal')" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700">
                        <i class="fas fa-check-double"></i>
                        <span class="font-medium">Weka Kama Imekamilika</span>
                    </button>
                </div>
                @endif
            @endif

            <!-- Delete Action for Pending -->
            @if($service->status == 'Inasubiri')
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-trash text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hatari</h3>
                        <p class="text-sm text-gray-600">Futa ombi hili kabisa</p>
                    </div>
                </div>

                <form action="{{ route('pastoral-services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta ombi hili?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700">
                        <i class="fas fa-trash"></i>
                        <span class="font-medium">Futa Ombi</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Service Modal -->
@if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
    @if($service->status == 'Inasubiri')
    <div id="approveServiceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
            <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Idhinisha Huduma</h3>
                            <p class="text-sm text-gray-600">{{ $service->service_type }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('approveServiceModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('pastoral-services.approve', $service->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Taarifa za Huduma</p>
                                <p class="text-sm text-blue-700 mt-1">{{ $service->service_type }} - {{ $service->member->first_name }} {{ $service->member->last_name }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="approve_admin_notes" class="block text-gray-700 text-sm font-medium mb-2">Maoni (Si lazima)</label>
                        <textarea id="approve_admin_notes" name="admin_notes" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="Andika maoni yako hapa..."></textarea>
                    </div>
                </div>

                <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('approveServiceModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                        Funga
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        <span>Idhinisha</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Service Modal -->
    <div id="rejectServiceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
            <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Kataa Huduma</h3>
                            <p class="text-sm text-gray-600">{{ $service->service_type }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('rejectServiceModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('pastoral-services.reject', $service->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <p class="text-sm text-yellow-700">
                                Una uhakika unataka kukataa ombi hili la huduma? Hatua hii haiwezi kufutwa.
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="reject_admin_notes" class="block text-gray-700 text-sm font-medium mb-2">Sababu ya Kukataa <span class="text-red-500">*</span></label>
                        <textarea id="reject_admin_notes" name="admin_notes" rows="5" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="Eleza sababu ya kukataa ombi hili..."></textarea>
                    </div>
                </div>

                <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('rejectServiceModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                        Funga
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Kataa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($service->status == 'Imeidhinishwa')
    <!-- Complete Service Modal -->
    <div id="completeServiceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
            <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-check-double text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Kamilisha Huduma</h3>
                            <p class="text-sm text-gray-600">{{ $service->service_type }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('completeServiceModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('pastoral-services.complete', $service->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Thibitisha Ukamilishaji</p>
                                <p class="text-sm text-blue-700 mt-1">Je, huduma hii ya <strong>{{ $service->service_type }}</strong> imekamilika?</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('completeServiceModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                        Funga
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-check-double"></i>
                        <span>Ndiyo, Imekamilika</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endif

<script>
// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.id.includes('Modal')) {
        closeModal(event.target.id);
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['approveServiceModal', 'rejectServiceModal', 'completeServiceModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});
</script>
@endsection
