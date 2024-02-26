<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class InventoryModal extends ModalComponent
{
    public function render()
    {
        return view('livewire.inventory-modal');
    }
     public static function modalMaxWidth(): string
    {
        // 'sm'
        // 'md'
        // 'lg'
        // 'xl'
        // '2xl'
        // '3xl'
        // '4xl'
        // '5xl'
        // '6xl'
        // '7xl'
        return 'sm';
    }
}
