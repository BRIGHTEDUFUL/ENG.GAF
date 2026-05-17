<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['heading' => 'System Data Backup & Restore'])]
class SystemDataManager extends Component
{
    use WithFileUploads;

    public $importFile;
    public bool $showImportConfirm = false;

    public function mount()
    {
        if (!auth()->user()->hasRole(['admin', 'commander'])) {
            abort(403, 'Access denied.');
        }
    }

    public function exportData()
    {
        // Export all major tables as a JSON array
        $tables = [
            'users',
            'wings',
            'aircraft',
            'maintenance_tasks',
            'maintenance_logs',
            'flight_logs',
            'incidents',
            'daily_aircraft_states',
            'daily_defects',
            'daily_service_remarks',
            'audit_logs',
        ];

        $exportData = [];

        foreach ($tables as $table) {
            $exportData[$table] = DB::table($table)->get()->toArray();
        }

        $json = json_encode([
            'version' => '1.0',
            'timestamp' => now()->toIso8601String(),
            'data' => $exportData
        ], JSON_PRETTY_PRINT);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, 'gaf_ecs_full_backup_' . date('Y-m-d_His') . '.json');
    }

    public function confirmImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:json|max:10240', // max 10MB
        ]);
        
        $this->showImportConfirm = true;
    }

    public function processImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:json|max:10240',
        ]);

        $content = file_get_contents($this->importFile->getRealPath());
        $payload = json_decode($content, true);

        if (!$payload || !isset($payload['data'])) {
            $this->addError('importFile', 'Invalid backup file format.');
            $this->showImportConfirm = false;
            return;
        }

        try {
            // We need to disable foreign key checks to do a full wipe and restore
            Schema::disableForeignKeyConstraints();

            DB::transaction(function () use ($payload) {
                $tables = [
                    'audit_logs',
                    'daily_service_remarks',
                    'daily_defects',
                    'daily_aircraft_states',
                    'incidents',
                    'flight_logs',
                    'maintenance_logs',
                    'maintenance_tasks',
                    'aircraft',
                    'wings',
                    'users',
                ];

                // 1. Wipe existing
                foreach ($tables as $table) {
                    if (isset($payload['data'][$table])) {
                        DB::table($table)->truncate();
                    }
                }

                // 2. Insert new (reverse order for foreign keys, though checks are off)
                $insertOrder = array_reverse($tables);
                foreach ($insertOrder as $table) {
                    if (!empty($payload['data'][$table])) {
                        $chunks = array_chunk($payload['data'][$table], 500);
                        foreach ($chunks as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                }
            });

            Schema::enableForeignKeyConstraints();

            $this->dispatch('notify', type: 'success', message: 'Full system restore completed successfully.');
            $this->reset('importFile', 'showImportConfirm');
            
        } catch (\Exception $e) {
            Schema::enableForeignKeyConstraints();
            $this->addError('importFile', 'Restore failed: ' . $e->getMessage());
            $this->showImportConfirm = false;
        }
    }

    public function render()
    {
        return view('livewire.system-data-manager');
    }
}
