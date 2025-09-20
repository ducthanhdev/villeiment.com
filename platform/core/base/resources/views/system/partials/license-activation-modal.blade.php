<x-core::modal
    id="quick-activation-license-modal"
    title="License Status"
    size="md"
>
    <p>Your license is already activated!</p>
    <x-core::modal.footer>
        <x-core::button
            type="button"
            color="secondary"
            data-bs-dismiss="modal"
        >
            Close
        </x-core::button>
    </x-core::modal.footer>
</x-core::modal>

@if (Request::ajax())
    <script src="{{ asset('vendor/core/core/base/js/license-activation.js') }}"></script>
@else
    @push('footer')
        <script src="{{ asset('vendor/core/core/base/js/license-activation.js') }}"></script>
    @endpush
@endif
