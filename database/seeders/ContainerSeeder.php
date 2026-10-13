<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Port;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        $ports = Port::all();

        // 20 Assigned Containers
        $assignedContainers = [
            // Maersk containers
            ['port_id' => $ports[0]->id,  'container_number' => 'MSKU1029384', 'type' => '20GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[0]->id,  'container_number' => 'MSKU5849201', 'type' => '40GP', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[1]->id,  'container_number' => 'MSKU9938172', 'type' => '40HC', 'status' => 'MAINTENANCE', 'is_active' => false],
            ['port_id' => $ports[1]->id,  'container_number' => 'MSKU3847291', 'type' => '20GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[2]->id,  'container_number' => 'MSKU8829103', 'type' => '40GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[3]->id,  'container_number' => 'MSKU4729104', 'type' => '40HC', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[4]->id,  'container_number' => 'MSKU6628190', 'type' => '20GP', 'status' => 'MAINTENANCE', 'is_active' => false],

            // MSC containers
            ['port_id' => $ports[5]->id,  'container_number' => 'MSCU7482910', 'type' => '20GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[5]->id,  'container_number' => 'MSCU3819204', 'type' => '40GP', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[6]->id,  'container_number' => 'MSCU9920182', 'type' => '40HC', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[6]->id,  'container_number' => 'MSCU5538192', 'type' => '20GP', 'status' => 'MAINTENANCE', 'is_active' => false],
            ['port_id' => $ports[7]->id,  'container_number' => 'MSCU1192837', 'type' => '40GP', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[8]->id,  'container_number' => 'MSCU8837192', 'type' => '40HC', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[9]->id,  'container_number' => 'MSCU4492817', 'type' => '20GP', 'status' => 'AVAILABLE',   'is_active' => true],

            // CMA CGM containers
            ['port_id' => $ports[10]->id, 'container_number' => 'CMAU9283710', 'type' => '40GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[11]->id, 'container_number' => 'CMAU3829104', 'type' => '20GP', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[12]->id, 'container_number' => 'CMAU7728192', 'type' => '40HC', 'status' => 'MAINTENANCE', 'is_active' => false],
            ['port_id' => $ports[12]->id, 'container_number' => 'CMAU1182736', 'type' => '20GP', 'status' => 'AVAILABLE',   'is_active' => true],
            ['port_id' => $ports[13]->id, 'container_number' => 'CMAU6647281', 'type' => '40GP', 'status' => 'IN_USE',      'is_active' => true],
            ['port_id' => $ports[14]->id, 'container_number' => 'CMAU5538291', 'type' => '40HC', 'status' => 'AVAILABLE',   'is_active' => true],
        ];

        // 2 Unassigned Containers (port_id = NULL)
        $unassignedContainers = [
            ['port_id' => null, 'container_number' => 'TGHU1234567', 'type' => '20GP', 'status' => 'AVAILABLE', 'is_active' => true],
            ['port_id' => null, 'container_number' => 'HLXU9876543', 'type' => '40HC', 'status' => 'MAINTENANCE', 'is_active' => false],
        ];

        foreach (array_merge($assignedContainers, $unassignedContainers) as $container) {
            Container::create($container);
        }
    }
}
