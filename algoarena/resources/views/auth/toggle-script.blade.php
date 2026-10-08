@push('scripts')
<script>
document.querySelectorAll('[data-toggle-pw]').forEach(btn => btn.addEventListener('click', () => {
    const input = btn.previousElementSibling;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Sembunyikan' : 'Lihat';
}));
</script>
@endpush
