<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Cache;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Litespeed\LSCache\LSCache;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class Doc extends Model
{


    public $timestamps = false;
    protected $table = 'docs';
    protected $fillable = [
        'id', 'type_id', 'docable_type', 'docable_id', 'created_at',
    ];
    protected $casts = [

        'id' => 'string',
        'type_id' => 'string',
        'docable_id' => 'string',
//        'created_at' => 'timestamp',

    ];

    /**
     * Get all of the owning docable models.
     */
    public function docable()
    {
        return $this->morphTo();
    }

    public static function deleteFile($doc)
    {
        if ($doc->type_id == \Helper::$docsMap['video'])
            $ext = 'mp4';
        else
            $ext = 'jpg';

        if (Storage::exists("public/$doc->type_id/$doc->id.$ext")) {
            Storage::delete("public/$doc->type_id/$doc->id.$ext");
        }
        $doc->delete();
    }

    /**
     * @param $img
     * @param $docable_id
     * @param $docable_type
     * @param $type_id
     */
    static function createImage($img, $docable_id, $docable_type, $type_id, $id = null /*id=edit*/)
    {
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        if ($id == null) {
            $image = Doc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'docable_type' => $docable_type,
                'created_at' => now()
            ]);
            $id = $image->id;
        } else {
            Doc::where('id', $id)->update(['created_at' => now()]);
        }
        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }


        $source = imagecreatefromstring($image_base64);
//        imagetruecolortopalette($source, false, 16);
        $imageSave = imagejpeg($source, storage_path("app/public/$type_id/$id.jpg"), 80);
        imagedestroy($source);
        if ($id != null) {

//            mRequest(asset("storage/$type_id/$id.jpg"), 'GET', [], ['X-LiteSpeed-Purge: *', ('X-LiteSpeed-Purge: ' . asset("storage/$type_id/$id.jpg")), 'Pragma: no-cache', 'Cache-Control: max-age=0, no-cache, no-store']);
            mRequest(asset("storage/$type_id/$id.jpg"), 'GET', [], ['X-LiteSpeed-Purge: private, *', 'Pragma: no-cache', 'Cache-Control: max-age=0, no-cache, no-store']);

            //            clearstatcache();


//            wait for cache refresh

        }
//        file_put_contents(storage_path("app/public/$type_id/$image->id.jpg"), $image_base64);


    }

    /**
     * @param UploadedFile $video
     * @param $docable_id
     * @param $docable_type
     * @param $type_id
     */
    static function createVideo(UploadedFile $video, $docable_id, $docable_type, $type_id, $id = null)
    {


        if ($id == null) {
            $vid = Doc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'docable_type' => $docable_type,
                'created_at' => now()
            ]);
            $id = $vid->id;
        } else {
            Doc::where('id', $id)->update(['created_at' => now()]);
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


    static function createFakeFile(UploadedFile $video, $docable_id, $docable_type, $type_id, $id = null)
    {

        if (!Storage::exists("app/public/$type_id")) {
            Storage::makeDirectory("public/$type_id");
        }

        if ($id == null) {
            $vid = Doc::create([
                'type_id' => $type_id,
                'docable_id' => $docable_id,
                'docable_type' => $docable_type,
                'created_at' => now()
            ]);
            $id = $vid->id;
        } else {
            Doc::where('id', $id)->update(['created_at' => now()]);
        }

        copy($video->path(), (storage_path("app/public/$type_id/") . "$id." . $video->extension()));


    }
}
