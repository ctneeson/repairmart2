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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category', 255);
            $table->string('subcategory', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('products')->insert([
            ['category' => 'Arts, Crafts & Sewing', 'subcategory' => 'Printing Presses & Accessories'],
            ['category' => 'Arts, Crafts & Sewing', 'subcategory' => 'Sewing Machines'],
            ['category' => 'Arts, Crafts & Sewing', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Audio Headphones & Accessories'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Blu-ray Players & Recorders'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Cassette Players & Recorders'],
            ['category' => 'Audio-Visual', 'subcategory' => 'CB & Two-Way Radios'],
            ['category' => 'Audio-Visual', 'subcategory' => 'CD Players'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Compact Radios & Stereos'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Digital Voice Recorders'],
            ['category' => 'Audio-Visual', 'subcategory' => 'DVD Players & Recorders'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Home Theater Systems'],
            ['category' => 'Audio-Visual', 'subcategory' => 'MP3 & MP4 Players'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Radios'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Satellite Television Products'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Speakers & Audio Systems'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Streaming Media Players'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Televisions'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Turntables & Accessories'],
            ['category' => 'Audio-Visual', 'subcategory' => 'TV-DVD Combinations'],
            ['category' => 'Audio-Visual', 'subcategory' => 'VCRs'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Video Projectors'],
            ['category' => 'Audio-Visual', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Beauty & Personal Care', 'subcategory' => 'Personal Care Products'],
            ['category' => 'Beauty & Personal Care', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Camera & Photo Accessories'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Digital Cameras'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Film Cameras'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Photo Printers & Scanners'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Quadcopters & Accessories'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Video Cameras'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Video Surveillance Equipment'],
            ['category' => 'Camera & Photo', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Cell Phones & Accessories', 'subcategory' => 'Cell Phone Accessories'],
            ['category' => 'Cell Phones & Accessories', 'subcategory' => 'Cell Phones'],
            ['category' => 'Cell Phones & Accessories', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Audio Devices'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Game Hardware'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Keyboards, Mice & Accessories'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Monitors'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Servers'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Computer Tablets'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Data Storage'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Desktop Computers'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Laptop Computers'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'USB Gadgets'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Webcams'],
            ['category' => 'Computers & Accessories', 'subcategory' => 'Other-Misc.'],
            ['category' => 'eBook Readers & Accessories', 'subcategory' => 'eBook Readers'],
            ['category' => 'eBook Readers & Accessories', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Dehumidifiers'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Home Air Purifiers'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Household Fans'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Humidifiers'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Indoor Space Heaters'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Room Air Conditioners'],
            ['category' => 'Heating, Cooling & Air Quality', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Home Electronics', 'subcategory' => 'Dishwashers'],
            ['category' => 'Home Electronics', 'subcategory' => 'Doorbells'],
            ['category' => 'Home Electronics', 'subcategory' => 'Electric Cookers'],
            ['category' => 'Home Electronics', 'subcategory' => 'Home Automation Devices'],
            ['category' => 'Home Electronics', 'subcategory' => 'Ironing Products'],
            ['category' => 'Home Electronics', 'subcategory' => 'Laundry Appliances'],
            ['category' => 'Home Electronics', 'subcategory' => 'Refrigerators, Freezers & Ice Makers'],
            ['category' => 'Home Electronics', 'subcategory' => 'Room Air Conditioners & Accessories'],
            ['category' => 'Home Electronics', 'subcategory' => 'Safety & Security Devices'],
            ['category' => 'Home Electronics', 'subcategory' => 'Vacuum Cleaners & Steam Cleaners'],
            ['category' => 'Home Electronics', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Industrial & Scientific', 'subcategory' => '3D Printers'],
            ['category' => 'Industrial & Scientific', 'subcategory' => '3D Scanners'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Cutting Tools'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Digital Signage Equipment'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Electronic Components'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Filtration'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Food Service Equipment & Supplies'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Hydraulics, Pneumatics & Plumbing'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Industrial Power & Hand Tools'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Lab Instruments & Equipment'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Occupational Health & Safety Products'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Security & Surveillance Equipment'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Testing, Measurement & Inspection Devices'],
            ['category' => 'Industrial & Scientific', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Kitchen Appliances', 'subcategory' => 'Coffee, Tea & Espresso'],
            ['category' => 'Kitchen Appliances', 'subcategory' => 'Electric Knives'],
            ['category' => 'Kitchen Appliances', 'subcategory' => 'Kitchen Small Appliances'],
            ['category' => 'Kitchen Appliances', 'subcategory' => 'Kitchen Utensils & Gadgets'],
            ['category' => 'Kitchen Appliances', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Electronic Drums'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Electronic Music, DJ & Karaoke'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Music Recording Equipment'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Musical Instrument Accessories'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Musical Instrument Amplifiers & Effects'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Musical Instrument Keyboards & MIDI'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Recording Microphones & Accessories'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Stage & Sound Equipment'],
            ['category' => 'Musical Instruments', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Office Electronics', 'subcategory' => 'Fax Machines'],
            ['category' => 'Office Electronics', 'subcategory' => 'Point-of-Sale (POS) Equipment'],
            ['category' => 'Office Electronics', 'subcategory' => 'Printers, Scanners, Copiers'],
            ['category' => 'Office Electronics', 'subcategory' => 'Telephones'],
            ['category' => 'Office Electronics', 'subcategory' => 'Video Projectors & Accessories'],
            ['category' => 'Office Electronics', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Patio, Lawn & Garden', 'subcategory' => 'Outdoor Kitchen Appliances'],
            ['category' => 'Patio, Lawn & Garden', 'subcategory' => 'Outdoor Power & Lawn Equipment'],
            ['category' => 'Patio, Lawn & Garden', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Robotics', 'subcategory' => 'Unmanned Aerial Vehicles (UAVs)'],
            ['category' => 'Robotics', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Camping & Hiking Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Cycling Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Exercise & Fitness Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Golf Accessories'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Hunting & Fishing'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Leisure & Games Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Other Sports Types'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Outdoor Recreation Accessories'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Sports Accessories'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Team Sports Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Water Sports'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Winter Sports Equipment'],
            ['category' => 'Sports & Outdoors', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Tools', 'subcategory' => 'Electrical Tools & Hardware'],
            ['category' => 'Tools', 'subcategory' => 'Power Tools'],
            ['category' => 'Tools', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Toys & Games', 'subcategory' => 'Electronic Toys'],
            ['category' => 'Toys & Games', 'subcategory' => 'Remote & App Controlled Vehicles & Parts'],
            ['category' => 'Toys & Games', 'subcategory' => 'Video Game Consoles & Accessories'],
            ['category' => 'Toys & Games', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Vehicle Electronics', 'subcategory' => 'Car Audio & Video Accessories'],
            ['category' => 'Vehicle Electronics', 'subcategory' => 'Car Electronics'],
            ['category' => 'Vehicle Electronics', 'subcategory' => 'Marine Electronics'],
            ['category' => 'Vehicle Electronics', 'subcategory' => 'Vehicle GPS Units & Equipment'],
            ['category' => 'Vehicle Electronics', 'subcategory' => 'Other-Misc.'],
            ['category' => 'Wearable Technology', 'subcategory' => 'Smart Glasses'],
            ['category' => 'Wearable Technology', 'subcategory' => 'Smartwatches & Smart Rings'],
            ['category' => 'Wearable Technology', 'subcategory' => 'Other-Misc.'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
