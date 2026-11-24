@if (session('success'))
    <div class="mb-4 rounded-4 border border-success-subtle bg-success-subtle px-3 py-2 small fw-medium text-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-4 border border-secondary-subtle bg-danger-subtle px-3 py-2 small fw-medium text-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-4 border border-secondary-subtle bg-danger-subtle px-3 py-2 small fw-medium text-danger">{{ $errors->first() }}</div>
@endif
