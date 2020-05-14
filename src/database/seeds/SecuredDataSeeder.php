<?php

use Illuminate\Database\Seeder;

use App\Models\SecuredData;

class SecuredDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $securedDatas = factory(SecuredData::class, 10)->create();
    }
}
