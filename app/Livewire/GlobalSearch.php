<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\MaintenanceTask;
use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $isOpen = false;

    public function openSearch()
    {
        $this->isOpen = true;
        $this->query = '';
    }

    public function render()
    {
        $results = collect();

        if (strlen($this->query) >= 2) {
            // Search Aircraft
            Aircraft::where('tail_number', 'like', "%{$this->query}%")
                ->orWhere('model', 'like', "%{$this->query}%")
                ->take(3)->get()->each(function ($a) use ($results) {
                    $results->push([
                        'type' => 'Aircraft',
                        'title' => $a->tail_number . ' (' . $a->model . ')',
                        'desc' => 'Status: ' . $a->status,
                        'url' => route('aircraft.profile', $a->id),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                        'color' => 'bg-sky-100 text-sky-700'
                    ]);
                });

            // Search Tasks
            MaintenanceTask::where('title', 'like', "%{$this->query}%")
                ->orWhere('description', 'like', "%{$this->query}%")
                ->take(3)->get()->each(function ($t) use ($results) {
                    $results->push([
                        'type' => 'Task',
                        'title' => $t->title,
                        'desc' => 'Priority: ' . $t->priority,
                        'url' => route('maintenance.tasks') . '?search=' . urlencode($t->title),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>',
                        'color' => 'bg-amber-100 text-amber-700'
                    ]);
                });

            // Search Personnel
            User::where('name', 'like', "%{$this->query}%")
                ->orWhere('service_number', 'like', "%{$this->query}%")
                ->take(3)->get()->each(function ($u) use ($results) {
                    $results->push([
                        'type' => 'Personnel',
                        'title' => $u->rank . ' ' . $u->name,
                        'desc' => 'SVC: ' . $u->service_number,
                        'url' => route('personnel.index') . '?search=' . urlencode($u->name),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/>',
                        'color' => 'bg-emerald-100 text-emerald-700'
                    ]);
                });
        }

        return view('livewire.global-search', ['results' => $results]);
    }
}
