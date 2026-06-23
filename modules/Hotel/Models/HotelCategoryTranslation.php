<?php
namespace Modules\Hotel\Models;

use App\BaseModel;

class HotelCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_hotel_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}