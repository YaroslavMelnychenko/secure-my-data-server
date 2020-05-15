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
        $securedPlainDatas = factory(SecuredData::class, 5)->create();
        $securedAttachments = factory(SecuredData::class, 5)->states('attachment')->create();
    }
}
