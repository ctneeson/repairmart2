<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('manufacturers')->insert([
            ['name' => '-- Other/Unknown --'],
            ['name' => '3M'],
            ['name' => 'Acer'],
            ['name' => 'Aiwa'],
            ['name' => 'Akai'],
            ['name' => 'Alba'],
            ['name' => 'Alcatel'],
            ['name' => 'Amazon'],
            ['name' => 'AMD'],
            ['name' => 'Amstrad'],
            ['name' => 'AOC'],
            ['name' => 'Apple'],
            ['name' => 'Asus'],
            ['name' => 'Atari'],
            ['name' => 'Avaya'],
            ['name' => 'Beko'],
            ['name' => 'BenQ'],
            ['name' => 'Binatone'],
            ['name' => 'Blaupunkt'],
            ['name' => 'Bosch'],
            ['name' => 'Bose'],
            ['name' => 'Braun'],
            ['name' => 'BT'],
            ['name' => 'Bush'],
            ['name' => 'BYD Electronic'],
            ['name' => 'Canon'],
            ['name' => 'Casio'],
            ['name' => 'Cisco'],
            ['name' => 'Clarion'],
            ['name' => 'Daewoo'],
            ['name' => 'Dell'],
            ['name' => 'D-Link'],
            ['name' => 'Dynalite'],
            ['name' => 'Dyson'],
            ['name' => 'Electrolux'],
            ['name' => 'Epson'],
            ['name' => 'Ericcson'],
            ['name' => 'Fitbit'],
            ['name' => 'Fujifilm'],
            ['name' => 'Fujitsu'],
            ['name' => 'Garmin'],
            ['name' => 'Gateway'],
            ['name' => 'Gionee'],
            ['name' => 'Google'],
            ['name' => 'Grundig'],
            ['name' => 'Hewlett-Packard'],
            ['name' => 'Hisense'],
            ['name' => 'Hitachi'],
            ['name' => 'HP'],
            ['name' => 'HTC'],
            ['name' => 'Huawei'],
            ['name' => 'Husqvarna'],
            ['name' => 'Hyundai'],
            ['name' => 'IBM'],
            ['name' => 'Intel'],
            ['name' => 'JBL'],
            ['name' => 'JVC'],
            ['name' => 'Kenwood'],
            ['name' => 'Kingston'],
            ['name' => 'Konica Minolta'],
            ['name' => 'Kyocera'],
            ['name' => 'Lenovo'],
            ['name' => 'LG'],
            ['name' => 'Marconi'],
            ['name' => 'Marshall'],
            ['name' => 'MediaTek'],
            ['name' => 'Micron'],
            ['name' => 'Microsoft'],
            ['name' => 'Miele'],
            ['name' => 'Mitsubishi'],
            ['name' => 'Morphy Richards'],
            ['name' => 'Motorola'],
            ['name' => 'NEC'],
            ['name' => 'Nikon'],
            ['name' => 'Nintendo'],
            ['name' => 'Nokia'],
            ['name' => 'Nvidia'],
            ['name' => 'Olivetti'],
            ['name' => 'Olympus'],
            ['name' => 'OnePlus'],
            ['name' => 'Oppo'],
            ['name' => 'Packard Bell'],
            ['name' => 'Panasonic'],
            ['name' => 'Pentax'],
            ['name' => 'Philips'],
            ['name' => 'Pioneer'],
            ['name' => 'Plantronics'],
            ['name' => 'Polycom'],
            ['name' => 'Pye'],
            ['name' => 'Qualcomm'],
            ['name' => 'RCA'],
            ['name' => 'Realtek'],
            ['name' => 'Ricoh'],
            ['name' => 'Russell Hobbs'],
            ['name' => 'Samsung'],
            ['name' => 'Sandisk'],
            ['name' => 'Sanyo'],
            ['name' => 'Seagate'],
            ['name' => 'Sega'],
            ['name' => 'Sennheiser'],
            ['name' => 'Severin'],
            ['name' => 'Sharp'],
            ['name' => 'Siemens'],
            ['name' => 'Sonos'],
            ['name' => 'Sony'],
            ['name' => 'TDK'],
            ['name' => 'Telefunken'],
            ['name' => 'Texas Instruments'],
            ['name' => 'Thomson'],
            ['name' => 'Thorn'],
            ['name' => 'Toshiba'],
            ['name' => 'TP-Link/intex'],
            ['name' => 'Unisys'],
            ['name' => 'Viewsonic'],
            ['name' => 'Western Digital'],
            ['name' => 'Wipro'],
            ['name' => 'Wortmann'],
            ['name' => 'Xerox'],
            ['name' => 'Xiaomi'],
            ['name' => 'ZTE'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
