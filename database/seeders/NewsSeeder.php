<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sliders = [
            [
                'title'       => 'Welcome to Oxford English Center',
                'sub'         => 'Empowering students with world-class English language education since 1994.',
                'image'       => 'assets/oxford/img/banner/1.jpg',
                'thumb'       => 'assets/oxford/img/banner/1.jpg',
                'category_id' => 1,
                'publish'     => 1,
                'pub_date'    => Carbon::now(),
                'user_id'     => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Expert Teachers & Modern Methods',
                'sub'         => 'Our certified instructors use interactive techniques to help you master English fast.',
                'image'       => 'assets/oxford/img/banner/4.jpg',
                'thumb'       => 'assets/oxford/img/banner/4.jpg',
                'category_id' => 1,
                'publish'     => 1,
                'pub_date'    => Carbon::now(),
                'user_id'     => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Programs for All Ages',
                'sub'         => 'From Kids to Adults, we offer specialized courses tailored to your specific needs.',
                'image'       => 'assets/oxford/img/banner/family.jpg',
                'thumb'       => 'assets/oxford/img/banner/family.jpg',
                'category_id' => 1,
                'publish'     => 1,
                'pub_date'    => Carbon::now(),
                'user_id'     => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        foreach ($sliders as $slider) {
            DB::table('news')->insert($slider);
        }

        // Also add some sample news for the Latest News section (Category 2)
        $news = [
            [
                'title'       => 'New Enrollment Season Starts Now!',
                'sub'         => 'Registration is now open for the summer intensive courses. Book your seat today.',
                'image'       => 'assets/oxford/img/banner/Gemini.png',
                'thumb'       => 'assets/oxford/img/banner/Gemini.png',
                'category_id' => 2,
                'publish'     => 1,
                'pub_date'    => Carbon::now(),
                'user_id'     => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Oxford Academy Achievement Awards',
                'sub'         => 'Celebrating our top-performing students in the recent IELTS examinations.',
                'image'       => 'assets/oxford/img/banner/partner.jpg',
                'thumb'       => 'assets/oxford/img/banner/partner.jpg',
                'category_id' => 2,
                'publish'     => 1,
                'pub_date'    => Carbon::now(),
                'user_id'     => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        foreach ($news as $item) {
            DB::table('news')->insert($item);
        }
    }
}
