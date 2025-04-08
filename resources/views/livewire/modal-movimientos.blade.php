<div>
    <x-filament::modal id="modalMovimientos" closeable>
        <x-slot name="header">
            <h2>{{ $tipo }} Detalles</h2>
        </x-slot>

        <x-filament::table>
            <x-slot name="head">
                <x-filament::th>Tipo</x-filament::th>
                <x-filament::th>Monto</x-filament::th>
                <x-filament::th>Fecha</x-filament::th>
            </x-slot>
            @foreach ($movimientos as $mov)
                <tr>
                    <x-filament::td>{{ $mov->tipo ?? 'Pago Préstamo' }}</x-filament::td>
                    <x-filament::td>{{ number_format($mov->monto ?? $mov->monto_pago, 2) }} USD</x-filament::td>
                    <x-filament::td>{{ $mov->fecha_transaccion ?? $mov->fecha_pago }}</x-filament::td>
                </tr>
            @endforeach
        </x-filament::table>
    </x-filament::modal>
</div>
