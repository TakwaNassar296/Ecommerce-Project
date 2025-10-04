<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tenant = Tenant::factory()->create();

        DB::table('gateways')->insert([
            'name' => 'myfatoorah',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
    }
}
