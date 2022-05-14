<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogDoc;
use App\Models\Category;
use App\Models\Club;
use App\Models\Coach;
use App\Models\County;
use App\Models\Coupon;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Product;
use App\Models\Province;
use App\Models\Ref;
use App\Models\Setting;
use App\Models\Settings;
use App\Models\Shop;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\File;
use Helper;
use Illuminate\Http\UploadedFile;
use stdClass;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fa_IR');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');


        DB::table('doc-types')->truncate();
        DB::table('doc-types')->insert([
            ['id' => 1, 'name' => 'پروفایل',],
            ['id' => 2, 'name' => 'مجوز فعالیت',],
            ['id' => 3, 'name' => 'محیط باشگاه',],
            ['id' => 4, 'name' => 'محصول',],
            ['id' => 5, 'name' => 'فیلم بازیکن',],
            ['id' => 6, 'name' => 'لوگو فروشگاه',],
            ['id' => 7, 'name' => 'خبر',],
        ]);

        Category::truncate();
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'اخبار ورزشی', 'type_id' => Helper::$categoryType['blog']],
            ['id' => 2, 'name' => 'اخبار فوتبال داخلی', 'type_id' => Helper::$categoryType['blog']],
            ['id' => 3, 'name' => 'اخبار فوتبال خارجی', 'type_id' => Helper::$categoryType['blog']],
        ]);

        Setting::truncate();
        DB::table('settings')->insert([
            ['name' => 'درصد رفرال اول', 'key' => 'ref_1', 'value' => '20'],
            ['name' => 'درصد رفرال دوم', 'key' => 'ref_2', 'value' => '8'],
            ['name' => 'درصد رفرال سوم', 'key' => 'ref_3', 'value' => '6'],
            ['name' => 'درصد رفرال چهارم', 'key' => 'ref_4', 'value' => '4'],
            ['name' => 'درصد رفرال پنجم', 'key' => 'ref_5', 'value' => '2'],

            ['name' => 'قیمت بازیکن ۱ ماه(ت)', 'key' => 'player_1_price', 'value' => '50000'],
            ['name' => 'قیمت بازیکن ۳ ماه(ت)', 'key' => 'player_3_price', 'value' => '135000'],
            ['name' => 'قیمت بازیکن ۶ ماه(ت)', 'key' => 'player_6_price', 'value' => '250000'],
            ['name' => 'قیمت مربی ۱ ماه(ت)', 'key' => 'coach_1_price', 'value' => '100000'],
            ['name' => 'قیمت مربی ۳ ماه(ت)', 'key' => 'coach_3_price', 'value' => '250000'],
            ['name' => 'قیمت مربی ۶ ماه(ت)', 'key' => 'coach_6_price', 'value' => '450000'],
            ['name' => 'قیمت مرکزورزشی ۱ ماه(ت)', 'key' => 'club_1_price', 'value' => '150000'],
            ['name' => 'قیمت مرکزورزشی ۳ ماه(ت)', 'key' => 'club_3_price', 'value' => '380000'],
            ['name' => 'قیمت مرکزورزشی ۶ ماه(ت)', 'key' => 'club_6_price', 'value' => '600000'],
            ['name' => 'قیمت فروشگاه ۱ ماه(ت)', 'key' => 'shop_1_price', 'value' => '150000'],
            ['name' => 'قیمت فروشگاه ۳ ماه(ت)', 'key' => 'shop_3_price', 'value' => '380000'],
            ['name' => 'قیمت فروشگاه ۶ ماه(ت)', 'key' => 'shop_6_price', 'value' => '600000'],
            ['name' => 'قیمت نردبان (ت)', 'key' => 'pin_price', 'value' => '20000'],

        ]);


        foreach (Doc::get() as $doc) {
            Storage::delete("public/$doc->type_id/$doc->id.jpg");
            Storage::delete("public/$doc->type_id/$doc->id.mp4");
            $doc->delete();
        }
        if (Doc::count() == 0)
            Doc::truncate();


        $this->createcoupons($faker);
        $this->createuserrefs($faker);
        $this->createplayers($faker);

        $this->createcoaches($faker);

        $this->createclubs($faker);
        $this->createShops($faker);
        $this->createProducts($faker);
        $this->createBlogs($faker);


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createcoupons($faker)
    {
        Coupon::truncate();
        for ($i = 0; $i < 10; $i++)
            Coupon::create([
                'code' => str_random(8),
                'discount_percent' => $faker->randomElement([10, 13, 24, 25]),
                'created_at' => Carbon::now(),
                'used_at' => null,
                'expires_at' => null,
                'limit_price' => null,
                'user_id' => null
            ]);
    }

    private function createuserrefs($faker)
    {
        User::truncate();
        \App\Models\User::create([
            'name' => 'مجتبی',
            'family' => 'رجبی',
            'username' => 'mojtaba',
            'email' => 'moj2raj2@gmail.com',
            'email_verified' => true,
            'phone' => '09018945844',
            'phone_verified' => true,
            'password' => Hash::make('123123'),
            'score' => 0,
            'sheba' => $faker->numerify("########################"),
            'cart' => $faker->numerify("################"),
            'role' => 'go',
            'active' => true,
            'remember_token' => bin2hex(openssl_random_pseudo_bytes(30)),
            'expires_at' => null,
            'ref_code' => User::makeRefCode(),

        ]);
        \App\Models\User::create([
            'name' => 'داریوش',
            'family' => 'بهشتی',
            'username' => 'darush1355',
            'email' => '',
            'email_verified' => true,
            'phone' => '09351414815',
            'phone_verified' => true,
            'password' => Hash::make('d4815'),
            'score' => 0,
            'sheba' => $faker->numerify("########################"),
            'cart' => $faker->numerify("################"),
            'role' => 'ad',
            'active' => true,
            'remember_token' => bin2hex(openssl_random_pseudo_bytes(30)),
            'expires_at' => null,

        ]);
        for ($i = 0; $i < 30; $i++) {
            User::create([
                'name' => "$faker->firstName",
                'family' => "$faker->lastName",
                'username' => "$faker->username",
                'active' => true,
                'sheba' => $faker->numerify("########################"),
                'cart' => $faker->numerify("################"),
                'phone_verified' => true,
                'phone' => '09' . $faker->numberBetween(124654211, 987898978),
            ]);
        }

        Ref::truncate();
        for ($i = 0; $i < 5; $i++) {
            DB::table('refs')->insert([
                'inviter_id' => $faker->numberBetween(1, 30),
                'invited_id' => $faker->numberBetween(1, 30),
                'invited_purchase_type' => $faker->numberBetween(1, 4),
                'invited_purchase_months' => $faker->randomElement([1, 3, 6]),
            ]);
        }
    }

    private function createBlogs($faker)
    {
        foreach (BlogDoc::get() as $doc) {
            Storage::delete("public/$doc->type_id/$doc->id.jpg");
            $doc->delete();
        }
        if (BlogDoc::count() == 0)
            BlogDoc::truncate();

        Blog::truncate();
        for ($i = 0; $i < 20; $i++) {

            $blog = Blog::create([
                'user_id' => 1,
                'category_id' => $faker->numberBetween(1, 3),
                'title' => $faker->realText($faker->numberBetween(80, 100)),
                'summary' => $faker->realText($faker->numberBetween(80, 200)),
                'content' => json_encode(''),
                'tags' => collect(range(2, $faker->numberBetween(2, 5)))->map(function ($el) use ($faker) {
                    return $faker->realText($faker->numberBetween(20, 40));
                })->join(' '),
                'published_at' => Carbon::now()->addHours($faker->numberBetween(0, 1)),
                'is_draft' => false,
                'active' => true,

            ]);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(50, 70) . '.jpg';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            BlogDoc::createFakeFile($file, $blog->id, Helper::$docsMap['blog']);

            $blocks = [];
            $types = [];
            $k = $faker->numberBetween(2, 4);
            for ($j = 0; $j < $k; $j++)
                $types[] = $faker->randomElement(['image', 'paragraph', 'paragraph', 'paragraph', 'image', 'paragraph', 'table', 'list', 'header',]);
            foreach ($types as $blockType) {
                $tmp = new stdClass();
                $tmp->data = new stdClass();
                $tmp->type = $blockType;
                $tmp->id = $faker->numerify("#######");

                if ($blockType == 'image') {
                    $path = storage_path('app/public/faker/') . $faker->numberBetween(50, 70) . '.jpg';
                    //profile picture
                    $file = new UploadedFile(
                        $path,
                        File::name($path) . '.' . File::extension($path),
                        File::mimeType($path),
                        null,
                        true
                    );
                    $tmp->data->file = new stdClass();
                    $tmp->data->file->url = BlogDoc::createFakeFile($file, $blog->id, Helper::$docsMap['blog']);
                    $tmp->data->file->name = $faker->text($faker->numberBetween(5, 10)) . '.jpg';
                    $tmp->data->file->size = $faker->numerify("#####");
                    $tmp->data->caption = $faker->realText($faker->numberBetween(10, 15));
                    $tmp->data->stretched = $faker->boolean;

                } elseif ($blockType == 'paragraph') {
                    $tmp->data = ['text' => $faker->realText($faker->numberBetween(100, 500))];
                } elseif ($blockType == 'header') {
                    $tmp->data = ['text' => $faker->realText($faker->numberBetween(100, 200)), 'level' => $faker->numberBetween(1, 3)];
                } elseif ($blockType == 'list') {
                    $tmp->data = ['style' => $faker->randomElement(['ordered', 'unordered']), 'items' => [$faker->realText($faker->numberBetween(10, 20)), $faker->realText($faker->numberBetween(10, 20)), $faker->realText($faker->numberBetween(10, 20)),]];
                } elseif ($blockType == 'table') {
                    $tmp->data = ['withHeadings' => $faker->boolean, 'content' =>
                        collect(range(2, $faker->numberBetween(2, 5)))->map(function ($el) use ($faker) {
                            return collect(range(2, $faker->numberBetween(2, 5)))->map(function ($el) use ($faker) {
                                return $faker->text($faker->numberBetween(5, 8));
                            });
                        })->toArray()];
                }
                $blocks[] = $tmp;

            }
            $blog->content = json_encode($blocks);
            $blog->save();


        }
    }

    private function createplayers($faker)
    {
        Player::truncate();

        for ($i = 0; $i < 20; $i++) {
            $p = Province::find($faker->numberBetween(1, 31));
            $player = Player::create([
                'user_id' => 1,
                'province_id' => $p->id,
                'county_id' => County::where('province_id', $p->id)->inRandomOrder()->first()->id,
                'sport_id' => Sport::inRandomOrder()->first()->id,
                'name' => $faker->firstName,
                'family' => $faker->lastName,
                'height' => $faker->numberBetween(150, 190),
                'weight' => $faker->numberBetween(50, 120),
                'born_at' => (new Jalalian($faker->numberBetween(1360, 1395), $faker->numberBetween(1, 12), $faker->numberBetween(1, 29)))->toCarbon(),
                'is_man' => $faker->boolean,
                'active' => true,
                'phone' => '09' . $faker->numberBetween(124654211, 987898978),
                'description' => $faker->realText(490),

            ]);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(1, 14) . '.jpg';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            Doc::createFakeFile($file, $player->id, Helper::$typesMap['players'], Helper::$docsMap['profile']);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(1, 2) . '.mp4';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            Doc::createFakeFile($file, $player->id, Helper::$typesMap['players'], Helper::$docsMap['video']);
        }
    }

    private function createcoaches($faker)
    {
        Coach::truncate();

        for ($i = 0; $i < 20; $i++) {
            $p = Province::find($faker->numberBetween(1, 31));
            $player = Coach::create([
                'user_id' => 1,
                'province_id' => $p->id,
                'county_id' => County::where('province_id', $p->id)->inRandomOrder()->first()->id,
                'sport_id' => Sport::inRandomOrder()->first()->id,
                'name' => $faker->firstName,
                'family' => $faker->lastName,
                'born_at' => (new Jalalian($faker->numberBetween(1360, 1395), $faker->numberBetween(1, 12), $faker->numberBetween(1, 29)))->toCarbon(),
                'is_man' => $faker->boolean,
                'active' => true,
                'phone' => '09' . $faker->numberBetween(124654211, 987898978),
                'description' => $faker->realText(490),

            ]);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(1, 14) . '.jpg';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            Doc::createFakeFile($file, $player->id, Helper::$typesMap['coaches'], Helper::$docsMap['profile']);


        }
    }

    private function createclubs($faker)
    {
        Club::truncate();

        for ($i = 0; $i < 20; $i++) {
            $p = Province::find($faker->numberBetween(1, 31));
            $player = Club::create([
                'user_id' => 1,
                'province_id' => $p->id,
                'county_id' => County::where('province_id', $p->id)->inRandomOrder()->first()->id,
                'times' => json_encode(array_map(function ($e) use ($faker) {
                    return [
                        'id' => Sport::inRandomOrder()->first()->id,
                        'g' => $faker->randomElement([0, 1]),
                        'd' => $faker->numberBetween(0, 7),
                        'fh' => $faker->numberBetween(1, 24),
                        'th' => $faker->numberBetween(1, 24),
                        'fm' => $faker->randomElement([0, 30]),
                        'tm' => $faker->randomElement([0, 30]),
                    ];

                }, range(1, $faker->numberBetween(1, 5)))),
                'name' => $faker->company,
                'active' => true,
                'phone' => '09' . $faker->numberBetween(124654211, 987898978),
                'description' => $faker->realText(200),
                'address' => $faker->address,
                'location' => $faker->numerify("31.#######") . "," . $faker->numerify("56.#######"),
//                'location' => DB::raw("POINT(" . $faker->longitude() . "," . $faker->latitude() . ")"),

            ]);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(30, 36) . '.jpg';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            Doc::createFakeFile($file, $player->id, Helper::$typesMap['clubs'], Helper::$docsMap['license']);

            //club images
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {

                $path = storage_path('app/public/faker/') . $faker->numberBetween(15, 22) . '.jpg';
                $file = new UploadedFile(
                    $path,
                    File::name($path) . '.' . File::extension($path),
                    File::mimeType($path),
                    null,
                    true
                );
                Doc::createFakeFile($file, $player->id, Helper::$typesMap['clubs'], Helper::$docsMap['club']);
            }
        }
    }

    private function createShops($faker)
    {
        Shop::truncate();

        for ($i = 0; $i < 20; $i++) {
            $p = Province::find($faker->numberBetween(1, 31));
            $player = Shop::create([
                'user_id' => 1,
                'province_id' => $p->id,
                'county_id' => County::where('province_id', $p->id)->inRandomOrder()->first()->id,
                'groups' => json_encode(array_rand(Sport::pluck('id')->toArray(), $faker->numberBetween(2, 5))),
                'name' => $faker->company,
                'active' => true,
                'phone' => '09' . $faker->numberBetween(124654211, 987898978),
                'description' => $faker->realText(200),
                'address' => $faker->address,
                'location' => $faker->numerify("31.#######") . "," . $faker->numerify("56.#######"),
            ]);

            $path = storage_path('app/public/faker/') . $faker->numberBetween(30, 36) . '.jpg';
            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );

            Doc::createFakeFile($file, $player->id, Helper::$typesMap['shops'], Helper::$docsMap['logo']);


        }
    }

    private function createProducts($faker)
    {
        Product::truncate();


        for ($i = 0; $i < 60; $i++) {
            $price = $faker->numberBetween(50, 1000);
            $player = Product::create([

                'shop_id' => $faker->numberBetween(1, 5),
                'price' => $price * 1000,
                'group_id' => Sport::inRandomOrder()->first()->id,
                'discount_price' => $faker->numberBetween(1, 5) < 3 ? ($faker->numberBetween(10, $price) * 1000) : $price * 1000,
                'name' => $faker->randomElement(['دمبل', 'توپ', 'لباس ورزشی', 'ساق بند', 'کلاه', 'شلوار ورزشی', 'مکمل whey', 'راکت', 'گرمکن', 'عینک شنا', 'کلاه شنا']),
                'count' => $faker->numberBetween(0, 10),
                'active' => true,
                'tags' => $faker->randomElement(['دمبل', 'توپ', 'فوتبال', 'ورزشی', 'حراج', 'والیبال', 'مکمل', 'مکمل', 'توپ', 'لباس', 'لباس']),
                'description' => $faker->realText(500),
                'created_at' => Carbon::now()

            ]);
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $path = storage_path('app/public/faker/') . $faker->numberBetween(40, 50) . '.jpg';
                //profile picture
                $file = new UploadedFile(
                    $path,
                    File::name($path) . '.' . File::extension($path),
                    File::mimeType($path),
                    null,
                    true

                );

                Doc::createFakeFile($file, $player->id, Helper::$typesMap['products'], Helper::$docsMap['product']);


            }
        }
    }


}
