<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pekerjaan>
 */
class PekerjaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobData = [
            'Tukang Bangunan Harian' => 'Mencari pekerja harian untuk membantu proyek konstruksi. Pengalaman tidak wajib, yang penting rajin dan bisa bekerja keras.',
            'Pembantu Rumah Tangga' => 'Dibutuhkan pembantu rumah tangga untuk membersihkan rumah, mencuci, dan memasak. Jam kerja fleksibel.',
            'Pengasuh Anak' => 'Mencari pengasuh anak yang sabar dan berpengalaman. Mengurus anak usia 2-5 tahun.',
            'Sopir Angkot' => 'Dibutuhkan sopir angkot untuk rute dalam kota. Harus memiliki SIM B1 dan mengenal jalan dengan baik.',
            'Tukang Kebun' => 'Mencari tukang kebun untuk merawat taman rumah. Pengalaman berkebun minimal 1 tahun.',
            'Asisten Toko' => 'Dibutuhkan asisten toko untuk melayani pembeli dan mengatur barang dagangan.',
            'Kuli Panggul' => 'Mencari kuli panggul untuk membantu bongkar muat barang di pasar.',
            'Tukang Las' => 'Dibutuhkan tukang las berpengalaman untuk proyek pagar besi.',
            'Tukang Cat' => 'Mencari tukang cat untuk mengecat rumah 2 lantai. Bahan sudah disediakan.',
            'Penjaga Warung' => 'Dibutuhkan penjaga warung malam untuk shift malam. Lokasi strategis.',
            'Kurir Motor' => 'Mencari kurir motor untuk pengiriman barang dalam kota. Motor sendiri.',
            'Cleaning Service' => 'Dibutuhkan cleaning service untuk gedung perkantoran. Jam kerja pagi.',
            'Satpam' => 'Mencari satpam untuk menjaga kompleks perumahan. Shift malam tersedia.',
            'Tukang Pijat' => 'Dibutuhkan tukang pijat tradisional untuk layanan panggilan.',
            'Helper Konstruksi' => 'Mencari helper konstruksi untuk membantu tukang utama.'
        ];

        $addresses = [
            'Jl. Sudirman No. 123, Jakarta Pusat',
            'Jl. Thamrin No. 45, Jakarta Pusat', 
            'Jl. Gatot Subroto No. 67, Jakarta Selatan',
            'Jl. Ahmad Yani No. 89, Bandung',
            'Jl. Malioboro No. 12, Yogyakarta',
            'Jl. Pemuda No. 34, Semarang',
            'Jl. Gajah Mada No. 56, Surabaya',
            'Jl. Diponegoro No. 78, Solo',
            'Jl. Pahlawan No. 90, Medan',
            'Jl. Veteran No. 11, Makassar',
            'Jl. Merdeka No. 22, Palembang',
            'Jl. Asia Afrika No. 33, Bandung',
            'Jl. Pancasila No. 44, Denpasar',
            'Jl. Proklamasi No. 55, Bogor',
            'Jl. Kartini No. 66, Tangerang'
        ];

        $coordinates = [
            '-6.2088, 106.8456', // Jakarta
            '-6.9175, 107.6191', // Bandung
            '-7.7956, 110.3695', // Yogyakarta
            '-6.9932, 110.4203', // Semarang
            '-7.2575, 112.7521'  // Surabaya
        ];

        // Pick a random job
        $jobName = array_rand($jobData);
        $description = $jobData[$jobName];
        
        $address = $this->faker->randomElement($addresses);
        $coordinate = $this->faker->randomElement($coordinates);
        $coords = explode(', ', $coordinate);
        
        // Get mitra user IDs (5, 6, 7)
        $mitraUserIds = [5, 6, 7];

        return [
            'nama' => $jobName,
            'deskripsi' => $description,
            'alamat' => $address,
            'koordinat' => $coordinate,
            'min_gaji' => $this->faker->numberBetween(100000, 300000),
            'max_gaji' => $this->faker->numberBetween(300000, 500000),
            'max_pekerja' => $this->faker->numberBetween(1, 5),
            'jumlah_pelamar_diterima' => 0, // Always 0 as no one is assigned yet
            'is_active' => 1,
            'kriteria' => $this->faker->word(),
            'status' => 'Open',
            'petunjuk_alamat' => 'Dekat dengan ' . $this->faker->randomElement(['mall', 'pasar', 'stasiun', 'terminal', 'sekolah']),
            'latitude' => $coords[0],
            'longitude' => $coords[1],
            'start_job' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_job' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'deadline_job' => $this->faker->dateTimeBetween('now', '+3 days'),
            'foto_job' => null,
            'pembuat' => $this->faker->randomElement($mitraUserIds),
        ];
    }
}
