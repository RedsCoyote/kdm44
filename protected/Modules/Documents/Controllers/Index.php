<?php

namespace App\Modules\Documents\Controllers;

use App\Modules\Documents\Models\Document;
use App\Modules\Documents\Models\Category;
use T4\Http\E404Exception;
use T4\Mvc\Controller;

class Index
    extends Controller
{

    const DEFAULT_DOCS_COUNT = 20;

    public function actionDefault($count=self::DEFAULT_DOCS_COUNT)
    {
        $this->data->categories = Category::findAllTree();
        $this->data->items = Document::findAll(
            [
                'order' => 'published DESC',
                'limit' => $count,
            ]
        );
    }

    public function actionDocument($id)
    {
        $this->data->item = Document::findByPK($id);
        if (empty($this->data->item))
        {
            throw new E404Exception;
        }
        $this->data->similar = Document::findAllByColumn(
            '__category_id', $this->data->item->category->getPk(),
            [
                'order' => 'published DESC',
                'limit' => 5,
                'where' => 't1.__id <> ' . $this->data->item->getPk(),
            ]
        );
        $this->view->meta->title = $this->data->item->title;
    }

    public function actionDocumentsByCategory($id, $count=self::DEFAULT_DOCS_COUNT, $color='default')
    {
        $this->data->category = Category::findByPK($id);
        if (empty($this->data->category)) {
            throw new E404Exception;
        }

        $this->data->page = $this->app->request->get->page ?: 1;
        $this->data->total = Document::countAllByColumn('__category_id', $this->data->category->getPk());
        $this->data->size = $count;

        $this->data->items = Document::findAllByColumn(
            '__category_id',
            $this->data->category->getPk(),
            [
                'order' => 'published DESC',
                'offset' => ($this->data->page-1)*$count,
                'limit' => $count,
            ]
        );

        $this->data->color = $color;

        $this->view->meta->title = $this->data->topic->title;
    }

}