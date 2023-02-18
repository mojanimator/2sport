<?php

namespace App\Models;

use Carbon\Carbon;
use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class BlogDoc extends Model
{


    public $timestamps = false;
    protected $table = 'blog-docs';
    protected $fillable = [
        'id', 'type_id', 'docable_id', 'created_at',
    ];


    public static function deleteFile($doc)
    {

        $ext = 'jpg';
        $type_id = \Helper::$docsMap['blog'];

        if (Storage::exists("public/$type_id/$doc->id.$ext")) {
            Storage::delete("public/$type_id/$doc->id.$ext");
        }
        $doc->delete();
    }

    /**
     * @param $img
     * @param $docable_id
     * @param $docable_type
     * @param $type_id
     */
    static function createImage($img, $docable_id, $type_id, $id = null /*id=edit*/)
    {
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        if ($id == null) {
            $image = BlogDoc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'created_at' => now()
            ]);
            $id = $image->id;
        } else {
            BlogDoc::where('id', $id)->update(['created_at' => now()]);
        }
        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }


        $source = imagecreatefromstring($image_base64);
//        imagetruecolortopalette($source, false, 16);
        $imageSave = imagejpeg($source, storage_path("app/public/$type_id/$id.jpg"), 80);
        imagedestroy($source);
        return "/storage/$type_id/$id.jpg";
//        file_put_contents(storage_path("app/public/$type_id/$image->id.jpg"), $image_base64);


    }

    /**
     * @param UploadedFile $video
     * @param $docable_id
     * @param $docable_type
     * @param $type_id
     */
    static function createVideo(UploadedFile $video, $docable_id,   $type_id, $id = null)
    {


        if ($id == null) {
            $vid = BlogDoc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'created_at' => now()
            ]);
            $id = $vid->id;
        } else {
            BlogDoc::where('id', $id)->update(['created_at' => now()]);
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


    static function createFakeFile(UploadedFile $video, $docable_id, $type_id, $id = null)
    {

        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }

        if ($id == null) {
            $vid = BlogDoc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'created_at' => now()
            ]);
            $id = $vid->id;
        } else {
            BlogDoc::where('id', $id)->update(['created_at' => now()]);
        }

        copy($video->path(), (storage_path("app/public/$type_id/") . "$id." . $video->extension()));
        return "/storage/$type_id/$id.jpg";

    }
}
