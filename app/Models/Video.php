<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class Video extends Model
{
    protected $appends = [];
    public $timestamps = false;
    protected $table = 'videos';
    protected $fillable = [
        'id', 'user_id', 'category_id', 'title', 'description', 'duration', 'views', 'tags', 'active', 'created_at'
    ];
    protected $casts = [

        'active' => 'boolean',
//        'created_at' => 'timestamp',

    ];

    static function createImage($img, $type_id, $id)
    {
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }
        $source = imagecreatefromstring($image_base64);
//        imagetruecolortopalette($source, false, 16);
        $imageSave = imagejpeg($source, storage_path("app/public/$type_id/$id.jpg"), 80);
        imagedestroy($source);
        mRequest(asset("storage/$type_id/$id.jpg"), 'GET', [], ['X-LiteSpeed-Purge: private, *', 'Pragma: no-cache', 'Cache-Control: max-age=0, no-cache, no-store']);

    }

    static function createVideo(UploadedFile $video, $type_id, $id)
    {
        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }
        $video->move(storage_path("app/public/$type_id"), "$id." . $video->extension());
        return;
        try {
            FFMpeg::open($video->path())
                ->export()
                ->inFormat(new \FFMpeg\Format\Video\X264)
//        ->resize(640, 480)
                ->save(storage_path("app/public/$type_id/$doc->id.mp4"));
        } catch (EncodingException $exception) {
            $doc->delete();
        }

        FFMpeg::cleanupTemporaryFiles();
    }

    public static function deleteFile($id, $ext)
    {
        $type = Helper::$docsMap['videos'];

        if (Storage::exists("public/$type/$id.$ext")) {
            Storage::delete("public/$type/$id.$ext");
        }
    }
}
