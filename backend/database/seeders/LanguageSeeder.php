<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\User;
use Faker\Factory as Faker;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy tất cả user IDs hiện có
        $userIds = User::pluck('id')->toArray();
        $randomUserId = function() use ($userIds) {
            return $userIds[array_rand($userIds)];
        };

        // Tạo các bản ghi cụ thể cho Tiếng Việt, Tiếng Anh, Tiếng Pháp
        $languages = [
            "Tiếng Anh",
            "Tiếng Việt",
            "Tiếng Trung Quốc",
            "Tiếng Nhật",
            "Tiếng Hàn Quốc",
            "Tiếng Pháp",
            "Tiếng Đức",
            "Tiếng Tây Ban Nha",
            "Tiếng Bồ Đào Nha",
            "Tiếng Ý",
            "Tiếng Nga",
            "Tiếng Ả Rập",
            "Tiếng Thái",
            "Tiếng Lào",
            "Tiếng Khmer (Campuchia)",
            "Tiếng Myanmar (Burmese)",
            "Tiếng Hindi",
            "Tiếng Urdu",
            "Tiếng Bengali",
            "Tiếng Tamil",
            "Tiếng Telugu",
            "Tiếng Kannada",
            "Tiếng Malayalam",
            "Tiếng Punjabi",
            "Tiếng Gujarati",
            "Tiếng Marathi",
            "Tiếng Oriya",
            "Tiếng Assam",
            "Tiếng Sindhi",
            "Tiếng Kashmiri",
            "Tiếng Konkani",
            "Tiếng Manipuri",
            "Tiếng Mã Lai",
            "Tiếng Indonesia",
            "Tiếng Tagalog (Filipino)",
            "Tiếng Hà Lan",
            "Tiếng Thổ Nhĩ Kỳ",
            "Tiếng Ba Lan",
            "Tiếng Thụy Điển",
            "Tiếng Na Uy",
            "Tiếng Đan Mạch",
            "Tiếng Phần Lan",
            "Tiếng Iceland",
            "Tiếng Hy Lạp",
            "Tiếng Séc",
            "Tiếng Slovakia",
            "Tiếng Hungary",
            "Tiếng Romania",
            "Tiếng Bulgaria",
            "Tiếng Ukraina",
            "Tiếng Belarus",
            "Tiếng Gruzia",
            "Tiếng Armenia",
            "Tiếng Albania",
            "Tiếng Croatia",
            "Tiếng Serbia",
            "Tiếng Bosnia",
            "Tiếng Slovenia",
            "Tiếng Macedonia",
            "Tiếng Latvia",
            "Tiếng Lithuania",
            "Tiếng Estonia",
            "Tiếng Moldova",
            "Tiếng Kazakhstan",
            "Tiếng Uzbekistan",
            "Tiếng Kyrgyz",
            "Tiếng Turkmenistan",
            "Tiếng Tajik",
            "Tiếng Pashto",
            "Tiếng Dari (Ba Tư)",
            "Tiếng Swahili",
            "Tiếng Zulu",
            "Tiếng Xhosa",
            "Tiếng Sotho",
            "Tiếng Tswana",
            "Tiếng Afrikaans",
            "Tiếng Hausa",
            "Tiếng Yoruba",
            "Tiếng Igbo",
            "Tiếng Amharic",
            "Tiếng Somali",
            "Tiếng Malagasy",
            "Tiếng Creole Haiti",
            "Tiếng Maori",
            "Tiếng Tonga",
            "Tiếng Fiji",
            "Tiếng Samoa",
            "Tiếng Tok Pisin",
            "Tiếng Esperanto",
            "Tiếng Basque",
            "Tiếng Catalan",
            "Tiếng Galician",
            "Tiếng Welsh",
            "Tiếng Scots Gaelic",
            "Tiếng Breton",
            "Tiếng Luxembourg",
            "Tiếng Maltese",
            "Tiếng Ladino",
            "Tiếng Aymara",
            "Tiếng Quechua",
            "Tiếng Guarani",
            "Tiếng Mapuche",
            "Tiếng Nahuatl",
            "Tiếng Maya",
            "Tiếng Ainu",
            "Tiếng Tibetan",
            "Tiếng Uyghur",
            "Tiếng Hmong",
            "Tiếng Shan",
            "Tiếng Balinese",
            "Tiếng Sundanese",
            "Tiếng Madurese",
            "Tiếng Aceh",
            "Tiếng Bugis",
            "Tiếng Batak",
            "Tiếng Javanese",
            "Tiếng Balochi",
            "Tiếng Tatar",
            "Tiếng Bashkir",
            "Tiếng Chuvash",
            "Tiếng Udmurt",
            "Tiếng Mari",
            "Tiếng Komi",
            "Tiếng Mordvin",
            "Tiếng Samoan",
            "Tiếng Tuvalu",
            "Tiếng Palauan",
            "Tiếng Chamorro",
        ];
        
        // Chèn các bản ghi vào bảng languages
        foreach ($languages as $language) {
            Language::create([
                'name' => $language,
                'description' => '',
                'status' => 'active',
                'deleted_by' => null,
                'created_by' => 1, // ID của người tạo, giả sử admin
                'updated_by' => 1, // ID của người cập nhật
            ]);
        }
    }
}
