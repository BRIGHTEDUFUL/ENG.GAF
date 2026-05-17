<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Incident;
use App\Models\MaintenanceTask;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Core users — one per role
        // ----------------------------------------------------------------
        $admin = User::firstOrCreate(['email' => 'admin@airforce.mil'], [
            'name'     => 'System Administrator',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'rank'     => 'General',
        ]);

        $commander = User::firstOrCreate(['email' => 'commander@airforce.mil'], [
            'name'     => 'Wing Commander',
            'password' => Hash::make('password'),
            'role'     => 'commander',
            'rank'     => 'Colonel',
        ]);

        $supervisor = User::firstOrCreate(['email' => 'supervisor@airforce.mil'], [
            'name'     => 'Maintenance Supervisor',
            'password' => Hash::make('password'),
            'role'     => 'supervisor',
            'rank'     => 'Major',
        ]);

        $engineer = User::firstOrCreate(['email' => 'engineer@airforce.mil'], [
            'name'     => 'Lead Engineer',
            'password' => Hash::make('password'),
            'role'     => 'engineer',
            'rank'     => 'Captain',
        ]);

        $auditor = User::firstOrCreate(['email' => 'auditor@airforce.mil'], [
            'name'     => 'Compliance Auditor',
            'password' => Hash::make('password'),
            'role'     => 'auditor',
            'rank'     => 'Lieutenant',
        ]);

        // ----------------------------------------------------------------
        // 2. Wings
        // ----------------------------------------------------------------
        if (Wing::count() === 0) {
            $wing1 = Wing::create([
                'name'          => '1st Fighter Wing',
                'code'          => '1FW',
                'base_location' => 'Langley Air Force Base',
                'commander_id'  => $commander->id,
                'status'        => 'active',
                'established_date' => '1947-01-13',
                'description'   => 'Premier air superiority wing.',
            ]);

            $wing2 = Wing::create([
                'name'          => '2nd Bomb Wing',
                'code'          => '2BW',
                'base_location' => 'Barksdale Air Force Base',
                'commander_id'  => null,
                'status'        => 'active',
                'established_date' => '1963-03-01',
            ]);

            // Assign commander and engineer to wing 1
            $commander->update(['wing_id' => $wing1->id]);
            $engineer->update(['wing_id' => $wing1->id]);
            $supervisor->update(['wing_id' => $wing1->id]);
        } else {
            $wing1 = Wing::first();
            $wing2 = Wing::skip(1)->first() ?? $wing1;
        }

        // ----------------------------------------------------------------
        // 3. Aircraft
        // ----------------------------------------------------------------
        if (Aircraft::count() === 0) {
            $aircraft = collect([
                ['tail_number' => 'AF-0001-FA', 'model' => 'F-22 Raptor',          'manufacturer' => 'Lockheed Martin', 'status' => 'active',      'wing_id' => $wing1->id],
                ['tail_number' => 'AF-0002-FA', 'model' => 'F-16 Fighting Falcon', 'manufacturer' => 'General Dynamics','status' => 'active',      'wing_id' => $wing1->id],
                ['tail_number' => 'AF-0003-FA', 'model' => 'F-35 Lightning II',    'manufacturer' => 'Lockheed Martin', 'status' => 'maintenance', 'wing_id' => $wing1->id],
                ['tail_number' => 'AF-0004-BW', 'model' => 'B-52 Stratofortress',  'manufacturer' => 'Boeing',          'status' => 'active',      'wing_id' => $wing2->id],
                ['tail_number' => 'AF-0005-BW', 'model' => 'C-130 Hercules',       'manufacturer' => 'Lockheed Martin', 'status' => 'grounded',    'wing_id' => $wing2->id],
            ])->map(fn($data) => Aircraft::create(array_merge($data, [
                'year_manufactured'     => rand(1990, 2020),
                'total_flight_hours'    => rand(100, 8000),
                'last_maintenance_date' => now()->subDays(rand(10, 180))->format('Y-m-d'),
            ])));

            $aircraft1 = $aircraft->first();
            $aircraft3 = $aircraft->get(2);
        } else {
            $aircraft1 = Aircraft::first();
            $aircraft3 = Aircraft::skip(2)->first() ?? $aircraft1;
        }

        // ----------------------------------------------------------------
        // 4. Maintenance Tasks
        // ----------------------------------------------------------------
        if (MaintenanceTask::count() === 0) {
            MaintenanceTask::create([
                'title'       => 'Engine inspection and oil change',
                'description' => 'Full engine inspection per maintenance schedule.',
                'aircraft_id' => $aircraft1->id,
                'assigned_to' => $engineer->id,
                'created_by'  => $supervisor->id,
                'priority'    => 'high',
                'status'      => 'in-progress',
                'due_date'    => now()->addDays(3)->format('Y-m-d'),
            ]);

            MaintenanceTask::create([
                'title'       => 'Avionics calibration',
                'description' => 'Recalibrate navigation and targeting systems.',
                'aircraft_id' => $aircraft1->id,
                'assigned_to' => $engineer->id,
                'created_by'  => $supervisor->id,
                'priority'    => 'critical',
                'status'      => 'pending',
                'due_date'    => now()->addDays(1)->format('Y-m-d'),
            ]);

            MaintenanceTask::create([
                'title'       => 'Landing gear hydraulic check',
                'aircraft_id' => $aircraft3->id,
                'assigned_to' => $engineer->id,
                'created_by'  => $admin->id,
                'priority'    => 'medium',
                'status'      => 'pending',
                'due_date'    => now()->addDays(7)->format('Y-m-d'),
            ]);
        }

        // ----------------------------------------------------------------
        // 5. Incidents
        // ----------------------------------------------------------------
        if (Incident::count() === 0) {
            Incident::create([
                'title'         => 'Bird strike on F-22 during training',
                'description'   => 'Aircraft sustained minor damage from bird strike at 5,000ft.',
                'aircraft_id'   => $aircraft1->id,
                'reported_by'   => $commander->id,
                'severity'      => 'medium',
                'status'        => 'under-investigation',
                'incident_date' => now()->subDays(5),
            ]);

            Incident::create([
                'title'         => 'Hydraulic system failure',
                'description'   => 'Critical hydraulic failure detected during pre-flight check.',
                'aircraft_id'   => $aircraft3->id,
                'reported_by'   => $supervisor->id,
                'severity'      => 'critical',
                'status'        => 'open',
                'incident_date' => now()->subDays(2),
            ]);
        }

        // ----------------------------------------------------------------
        // 6. Personnel records (extend existing)
        // ----------------------------------------------------------------
        if (User::count() <= 5) {
            User::factory(20)->create();
        }

        $this->command->info('✅ Air Force Engineering System seeded successfully.');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  Admin:      admin@airforce.mil      / password');
        $this->command->info('  Commander:  commander@airforce.mil  / password');
        $this->command->info('  Supervisor: supervisor@airforce.mil / password');
        $this->command->info('  Engineer:   engineer@airforce.mil   / password');
        $this->command->info('  Auditor:    auditor@airforce.mil    / password');
    }
}
