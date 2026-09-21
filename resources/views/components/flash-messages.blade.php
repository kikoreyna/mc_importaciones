@if (session('success'))
    <div class="mb-5 rounded-xl border border-emerald-300 bg-emerald-100 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-sm font-medium text-red-800">{{ $errors->first() }}</div>
@endif
