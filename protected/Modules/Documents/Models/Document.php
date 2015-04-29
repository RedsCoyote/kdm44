<?php

namespace App\Modules\Documents\Models;

use T4\Core\Collection;
use T4\Core\Exception;
use T4\Fs\Helpers;
use T4\Http\Uploader;
use T4\Mvc\Application;
use T4\Orm\Model;

class Document
    extends Model
{

    static protected $schema = [
        'table' => 'documents',
        'columns' => ['title' => ['type' => 'string', 'length' => 1024,],
            'published' => ['type' => 'date',],
            'text' => ['type' => 'text', 'length' => 'big',],
        ],
        'relations' => [
            'category' => ['type'=>self::BELONGS_TO, 'model'=>'App\Modules\Documents\Models\Category'],
            'files' => ['type' => self::HAS_MANY, 'model' => '\App\Modules\Documents\Models\File'],
        ],
    ];


    public function uploadFiles($formFieldName)
    {
        $request = Application::getInstance()->request;
        if (!$request->existsFilesData() || !$request->isUploadedArray($formFieldName))
            return $this;

        $uploader = new Uploader($formFieldName);
        $uploader->setPath('/public/documents/docs/files');
        foreach ($uploader() as $uploadedFilePath) {
            if (false !== $uploadedFilePath)
                $this->files->append(new File(['file' => $uploadedFilePath]));
        }
        return $this;
    }

    public function beforeDelete()
    {
        $this->deleteFiles();
        return parent::beforeDelete();
    }


    public function deleteFiles()
    {
        if (!empty($this->files)) {
            try {
                $this->files = new Collection();
                foreach ($this->files as $file) {
                    Helpers::removeFile(ROOT_PATH_PUBLIC . $file->file);
                }
            } catch (\T4\Fs\Exception $e) {
                return false;
            }
        }
        return true;
    }

} 