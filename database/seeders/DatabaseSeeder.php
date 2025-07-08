<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;
use App\Models\KriteriaJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     UserSeeder::class,
        //     SideJobSeeder::class,
        // ]);

        // Create admin user
        Users::create([
            'nama' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'isAdmin' => 1,
            'password' => bcrypt('admin1234'),
        ]);

        // Create user1, user2, user3
        for ($i = 1; $i <= 3; $i++) {
            Users::create([
                'nama' => 'user' . $i,
                'email' => 'user' . $i . '@example.com',
                'role' => 'user',
                'isAdmin' => 0,
                'password' => bcrypt('user1234'),
            ]);
        }

        // Create owner1, owner2, owner3
        for ($i = 1; $i <= 3; $i++) {
            Users::create([
                'nama' => 'owner' . $i,
                'email' => 'owner' . $i . '@example.com',
                'role' => 'mitra',
                'isAdmin' => 0,
                'password' => bcrypt('owner1234'),
            ]);
        }


        $hobi_offline = [
            "berkebun",
            "memancing",
            "memasak manual",
            "membuat kerajinan tangan",
            "menjahit",
            "memahat kayu",
            "membuat batik",
            "memperbaiki sepeda atau motor",
            "mengelas",
            "membuat kue tradisional",
            "berburu",
            "melukis manual",
            "memotong rambut",
            "membuat sabun",
            "membuat lilin",
            "membangun model miniatur",
            "membuat perhiasan tangan",
            "bersepeda",
            "memperbaiki barang elektronik sederhana",
            "berjalan kaki",
            "membuat sablon manual",
            "mengumpulkan batu atau mineral",
            "memelihara hewan ternak kecil",
            "memancing ikan air tawar",
            "memperbaiki barang rumah tangga",
            "membuat gerabah",
            "memahat batu",
            "memancing dengan alat tradisional",
            "membuat alat musik tradisional",
            "menggambar dengan tangan",
            "memetik buah",
            "mengukir buah atau sayur",
            "memelihara ikan hias",
            "berkemah",
            "membuat lilin aromaterapi",
            "membuat kain tenun",
            "merawat tanaman hias",
            "membuat boneka bahan alami",
            "mengasah pisau",
            "mengolah tanah liat",
            "mengelola kebun bunga",
            "memperbaiki mesin sederhana",
            "menganyam",
            "membuat mainan kayu",
            "membuat topi dari bahan alami",
            "membuat alat rumah tangga dari kayu",
            "membuat lukisan pasir",
            "membuat karya seni daur ulang",
            "berburu jamur liar",
            "membuat makanan fermentasi tradisional"
        ];

        foreach($hobi_offline as $i){
            KriteriaJob::factory()->create([
                'nama' => ucwords($i)
            ]);
        }
        
        // Call the PekerjaanSeeder to create 15 Indonesian onsite daily jobs
        $this->call(PekerjaanSeeder::class);
    }
}
