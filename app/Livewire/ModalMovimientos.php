<?php

namespace App\Livewire;

use App\Models\Pago;
use App\Models\Transaccion;
use Livewire\Component;

class ModalMovimientos extends Component
{

    public $tipo;
    public $movimientos = [];

    protected $listeners = ['open-modal' => 'cargarDatos'];

    public function cargarDatos($data)
    {
        $this->tipo = $data['tipo'];

        if ($this->tipo === 'Ingresos') {
            $this->movimientos = Transaccion::where('tipo', 'Deposito')->get()
                ->merge(Pago::all()); // Combina depósitos y pagos
        } else {
            $this->movimientos = Transaccion::where('tipo', 'Retiro')->get();
        }
    }
    public function render()
    {
        return view('livewire.modal-movimientos');
    }
}
