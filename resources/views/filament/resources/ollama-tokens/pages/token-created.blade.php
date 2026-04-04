<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg bg-success-50 dark:bg-success-400/10 p-6">
            <div class="flex items-center gap-3 mb-4">
                <x-filament::icon
                    icon="heroicon-o-check-circle"
                    class="h-6 w-6 text-success-600 dark:text-success-400"
                />
                <h2 class="text-lg font-semibold text-success-900 dark:text-success-100">
                    Token erfolgreich erstellt
                </h2>
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-success-200 dark:border-success-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dein Token:
                    </label>
                    <div class="flex items-center gap-2">
                        <code id="token-value" class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-mono break-all">
                            {{ $token }}
                        </code>
                        <x-filament::button
                            color="gray"
                            icon="heroicon-o-clipboard"
                            onclick="copyToken()"
                            id="copy-button"
                        >
                            Kopieren
                        </x-filament::button>
                    </div>
                </div>

                <div class="bg-warning-50 dark:bg-warning-400/10 rounded-lg p-4 border border-warning-200 dark:border-warning-700">
                    <div class="flex items-start gap-2">
                        <x-filament::icon
                            icon="heroicon-o-exclamation-triangle"
                            class="h-5 w-5 text-warning-600 dark:text-warning-400 mt-0.5 flex-shrink-0"
                        />
                        <div class="text-sm text-warning-800 dark:text-warning-200">
                            <p class="font-semibold mb-1">Wichtig: Speichere diesen Token jetzt!</p>
                            <p>Du kannst diesen Token nur jetzt sehen. Er wird aus Sicherheitsgründen gehasht gespeichert und kann später nicht mehr angezeigt werden.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <x-filament::button
                tag="a"
                :href="$this->getResource()::getUrl('index')"
                color="gray"
            >
                Zurück zur Übersicht
            </x-filament::button>
        </div>
    </div>

    <script>
        function copyToken() {
            const tokenValue = document.getElementById('token-value').textContent.trim();
            const button = document.getElementById('copy-button');

            navigator.clipboard.writeText(tokenValue).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="ml-1">Kopiert!</span>';

                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</x-filament-panels::page>
