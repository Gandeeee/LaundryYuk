<?php

namespace App\Livewire\Admin;

use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class DriverIndex extends Component
{
    // properti untuk menampung input form modal
    public $name, $phone, $driver_id;
    public $is_available = true; // default aktif

    // properti untuk mode edit
    public $isEditMode = false;

    #[Layout('components.layouts.app')]
    #[Title('Manajemen Driver - LaundryYuk')]
    public function render()
    {
        // ambil semua data driver dari database, urutkan terbaru
        return view('livewire.admin.driver-index', [
            'drivers' => Driver::latest()->get()
        ]);
    }

    // reset form saat modal ditutup/dibuka
    public function resetInput()
    {
        $this->name = '';
        $this->phone = '';
        $this->is_available = true;
        $this->driver_id = null;
        $this->isEditMode = false;
    }

    // fungsi simpan driver baru
    public function store()
    {
        // validasi input
        $this->validate([
            'name' => 'required|min:3',
            'phone' => 'required|numeric|min:10',
        ]);

        Driver::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'is_available' => $this->is_available,
        ]);

        // notifikasi sederhana (opsional: bisa pakai sweetalert nanti)
        session()->flash('success', 'Driver berhasil ditambahkan.');
        
        // tutup modal (nanti kita handle di view via js)
        $this->dispatch('close-modal'); 
        $this->resetInput();
    }

    // fungsi ambil data untuk diedit
    public function edit($id)
    {
        $driver = Driver::find($id);
        $this->driver_id = $id;
        $this->name = $driver->name;
        $this->phone = $driver->phone;
        $this->is_available = $driver->is_available;
        $this->isEditMode = true;
    }

    // fungsi update data driver
    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'phone' => 'required|numeric|min:10',
        ]);

        if ($this->driver_id) {
            $driver = Driver::find($this->driver_id);
            $driver->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'is_available' => $this->is_available,
            ]);
            
            session()->flash('success', 'Data driver diperbarui.');
            $this->dispatch('close-modal');
            $this->resetInput();
        }
    }

    // fungsi hapus driver
    public function delete($id)
    {
        Driver::find($id)->delete();
        session()->flash('success', 'Driver berhasil dihapus.');
    }
    
    // fungsi toggle status ketersediaan (switch button)
    public function toggleStatus($id)
    {
        $driver = Driver::find($id);
        $driver->is_available = !$driver->is_available;
        $driver->save();
    }
}