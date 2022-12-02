@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->seeder }};

use App\Models\{{ $config->modelNames->name }};
use Illuminate\Database\Seeder;

class {{ $config->modelNames->plural }}TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {{ $config->modelNames->name }}::factory()->count(1)->create();
    }
}
