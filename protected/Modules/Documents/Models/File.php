<?php

namespace App\Modules\Documents\Models;

use T4\Orm\Model;

class File
    extends Model
{

    static protected $schema = [
        'table' => 'documentsfiles',
        'columns' => [
            'file' => ['type' => 'string'],
        ],
        'relations' => [
            'document' => ['type' => self::BELONGS_TO, 'model' => '\App\Modules\Documents\Models\Document'],
        ],
    ];

} 