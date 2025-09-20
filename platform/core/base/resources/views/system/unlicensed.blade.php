<x-core::layouts.base body-class="d-flex flex-column" :body-attributes="['data-bs-theme' => 'dark']">
    <x-slot:title>
        @yield('title')
    </x-slot:title>

    <script>
        // Automatically submit the skip form on page load
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('form[action="{{ route('unlicensed.skip') }}"]').submit();
        });
    </script>

    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                @include('core/base::partials.logo')
            </div>

            <x-core::card size="md">
                <x-core::card.body>
                    <h2 class="mb-3 text-center">Redirecting...</h2>
                    <p class="text-secondary mb-4">
                        Please wait while we proceed.
                    </p>

                    <form action="{{ route('unlicensed.skip') }}" method="POST" id="skip-form">
                        @csrf
                        @if($redirectUrl)
                            <input type="hidden" name="redirect_url" value="{{ $redirectUrl }}" />
                        @endif
                    </form>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
</x-core::layouts.base>