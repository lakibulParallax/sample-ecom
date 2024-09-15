<?php /** @noinspection PhpUnused */

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * App\Models\FileManager
 *
 * @property int $id
 * @property string|null $origin_type
 * @property int|null $origin_id
 * @property string|null $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $origin
 * @method static Builder|FileManager newModelQuery()
 * @method static Builder|FileManager newQuery()
 * @method static Builder|FileManager query()
 * @method static Builder|FileManager whereCreatedAt($value)
 * @method static Builder|FileManager whereId($value)
 * @method static Builder|FileManager whereOriginId($value)
 * @method static Builder|FileManager whereOriginType($value)
 * @method static Builder|FileManager whereUpdatedAt($value)
 * @method static Builder|FileManager whereUrl($value)
 * @mixin Eloquent
 */
class FileManager extends Model
{
    use HasFactory;

    protected $casts = [
        'origin_id' => 'integer',
    ];

    public function origin(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphMany
    {
        return $this->morphMany(User::class, 'user');
    }

    public function makeUpload($to, $file): ?string
    {
        try {
            $file_name = time() . Str::uuid();
            $extension = $file->extension();
            $file_name .= '.' . $extension;
            $path = $to;
            if ($extension === 'pdf') {
                $path = Storage::disk(config('app.storage_driver'))->put($path, $file);
            } elseif ($extension === 'xlsx') {
                $path = Storage::disk(config('app.storage_driver'))->put($path, $file);
            } elseif ($extension === 'doc') {
                $path = Storage::disk(config('app.storage_driver'))->put($path, $file);
            } elseif ($extension === 'docx') {
                $path = Storage::disk(config('app.storage_driver'))->put($path, $file);
            } else {
                $path .= '/' . $file_name;
                $file = Image::make($file->getRealPath());
                $file->orientate();
                $file->stream();
                Storage::disk(config('app.storage_driver'))->put($path, $file);
            }

            return $path;
        } catch (Exception $exception) {
            return "";
        }
    }

    public function upload($to, $file): FileManager
    {
        $path = $this->makeUpload($to, $file);
        $file_manager = new self();
        if ($path) {
            $file_manager->url = $path;
            $file_manager->save();
        } else {
            $file_manager->id = 0;
        }
        return $file_manager;
    }

    public function uploadUpdate($to, $file): FileManager
    {
        $path = $this->makeUpload($to, $file);
        if ($path) {
            $this->remove();

            $this->url = $path;
            $this->save();

            return $this;
        }

        $file_manager = new self();
        $file_manager->id = 0;
        return $file_manager;
    }

    public function getUrlAttribute(): ?string
    {
        if (config('app.storage_driver') === 's3') {
            return Storage::disk(config('app.storage_driver'))->url($this->attributes['url']);
        }
        return asset('application/public/storage/' . $this->attributes['url']);
    }

    public function remove(): void
    {
        Storage::disk(config('app.storage_driver'))->delete($this->attributes['url']);
    }
}
