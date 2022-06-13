<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tournament extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tournaments';
    protected $fillable = [
        'id', 'sport_id', 'name', 'started_at', 'updated_at', 'active'
    ];
    protected $casts = [
        'active' => 'boolean',
        'updated_at' => 'timestamp',
        'started_at' => 'timestamp',
        'sport_id' => 'string',

    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    static function createImage($img, $id)
    {
        if ($img == null) return;

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $type_id = \Helper::$docsMap['tournament'];
        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }


        $source = imagecreatefromstring($image_base64);
//        imagetruecolortopalette($source, false, 16);
        $imageSave = imagejpeg($source, storage_path("app/public/$type_id/$id.jpg"), 80);
        imagedestroy($source);

//        file_put_contents(storage_path("app/public/$type_id/$image->id.jpg"), $image_base64);


    }
}
