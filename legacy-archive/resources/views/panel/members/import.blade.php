@extends('layouts.app')

@section('title', 'Ingiza Waumini Kwa Wingi - Mfumo wa Kanisa')
@section('page-title', 'Ingiza Waumini Kwa Wingi')
@section('page-subtitle', 'Pakia faili la Excel au CSV kuongeza waumini wengi kwa pamoja')

@section('content')
<!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Rudi Nyuma
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Ingiza Waumini Kwa Wingi</h2>
            <p class="text-gray-600">Pakia faili la Excel au CSV kuongeza waumini wengi kwa pamoja</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                    <div>
                        <p class="font-semibold mb-2">Makosa yaliyopatikana:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Instructions Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Maelekezo
            </h3>
            <ol class="list-decimal list-inside space-y-2 text-blue-800">
                <li>Pakua template ya Excel kwa kubofya kitufe cha chini</li>
                <li>Jaza taarifa za waumini katika template</li>
                <li>Hakikisha unafuata muundo sahihi wa tarehe (YYYY-MM-DD)</li>
                <li>Zingatia sehemu zilizo na alama ya * (nyota) - ni lazima</li>
                <li>Hifadhi faili kama Excel (.xlsx) au CSV</li>
                <li>Pakia faili kwa kutumia fomu iliyo hapa chini</li>
            </ol>

            <div class="mt-4 p-4 bg-white rounded-lg">
                <p class="text-sm font-semibold text-gray-700 mb-2">Taarifa muhimu:</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-check text-green-600 mr-1"></i> Namba ya Muumini (Member Number) itatengenezwa moja kwa moja</li>
                    <li><i class="fas fa-check text-green-600 mr-1"></i> Namba ya Bahasha (Envelope Number) itatengenezwa moja kwa moja</li>
                    <li><i class="fas fa-check text-green-600 mr-1"></i> Waumini wote wataongezwa kama "Hai" (Active)</li>
                </ul>
            </div>
        </div>

        <!-- Download Template Button -->
        <div class="mb-6 flex justify-center">
            <a href="{{ route('members.import.template') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200 shadow-md">
                <i class="fas fa-download mr-2"></i>
                Pakua Template ya Excel
            </a>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chagua Faili la Kuingiza <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition duration-200">
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                           class="hidden"
                           onchange="updateFileName(this)">
                    <label for="file" class="cursor-pointer">
                        <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt text-4xl" style="color: #360958;"></i>
                        </div>
                        <p class="text-gray-700 font-medium mb-1">
                            Bofya hapa kuchagua faili
                        </p>
                        <p class="text-sm text-gray-500">
                            Excel (.xlsx, .xls) au CSV faili
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Ukubwa wa juu: 10MB
                        </p>
                    </label>
                    <div id="file-name" class="mt-4 text-sm font-medium text-primary-600 hidden">
                        <!-- File name will appear here -->
                    </div>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-3 text-white rounded-lg transition duration-200 shadow-md" style="background-color: #360958;">
                    <i class="fas fa-upload mr-2"></i>
                    Ingiza Waumini
                </button>
                <a href="{{ route('members.index') }}" class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition duration-200 shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Ghairi
                </a>
            </div>
        </form>

        <!-- Sample Data Format -->
        <div class="mt-8 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Mfano wa Muundo wa Data (Template ina safu 24)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Jina la Kwanza*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Jina la Kati</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Jina la Ukoo*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Tarehe Kuzaliwa*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Jinsia*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Simu*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Jumuiya*</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Hali ya Ndoa*</th>
                        </tr>
                    </thead>
                    <tbody class="bg-green-50 divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap font-medium">Yohana</td>
                            <td class="px-3 py-2 whitespace-nowrap">Petro</td>
                            <td class="px-3 py-2 whitespace-nowrap font-medium">Mwakasege</td>
                            <td class="px-3 py-2 whitespace-nowrap">1985-06-15</td>
                            <td class="px-3 py-2 whitespace-nowrap">Mme</td>
                            <td class="px-3 py-2 whitespace-nowrap">0712345678</td>
                            <td class="px-3 py-2 whitespace-nowrap">Jumuiya ya Kwanza</td>
                            <td class="px-3 py-2 whitespace-nowrap">Ameoa/Ameolewa</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-2">* Taarifa hizi ni lazima. Template ya Excel ina safu zote 24 zinazohusiana na fomu ya usajili.</p>

            <!-- All fields list -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-semibold text-gray-700 mb-2">Safu zote kwenye Template (24):</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs text-gray-600">
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Jina la Kwanza *</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Jina la Kati</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Jina la Ukoo *</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Tarehe Kuzaliwa *</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Jinsia *</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Namba NIDA</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Simu *</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Barua Pepe</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Anwani</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Namba Nyumba</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Namba Block</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Jiji</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Mkoa</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Jumuiya *</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Tarehe Ubatizo</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Tarehe Kipaimara</span>
                    <span><i class="fas fa-check text-green-600 mr-1"></i>Hali ya Ndoa *</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Kundi Maalum</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Kazi/Ajira</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Mzee wa Kanisa</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Jina la Mwenzi</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Simu ya Mwenzi</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Jina la Jirani</span>
                    <span><i class="fas fa-check text-gray-400 mr-1"></i>Simu ya Jirani</span>
                </div>
            </div>
        </div>
    </div>

<script>
function updateFileName(input) {
    const fileNameDiv = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        fileNameDiv.textContent = 'Faili: ' + input.files[0].name;
        fileNameDiv.classList.remove('hidden');
    } else {
        fileNameDiv.textContent = '';
        fileNameDiv.classList.add('hidden');
    }
}
</script>
@endsection