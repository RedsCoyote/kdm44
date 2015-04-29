<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Documents\Models\Category;
use App\Modules\Documents\Models\Document;
use App\Modules\Documents\Models\File;
use T4\Mvc\Controller;
use T4\Core\Exception;

class Documents
    extends Controller
{

    const PAGE_SIZE = 20;

    protected function access($action)
    {
        return !empty($this->app->user) && $this->app->user->hasRole('admin');
    }

    public function actionDefault($page = 1)
    {
        $this->data->itemsTotalCount = Document::countAll();
        $this->data->pageSize = self::PAGE_SIZE;
        $this->data->activePage = $page;

        $this->data->items = Document::findAll([
            'order' => 'published DESC',
            'offset'=> ($page-1)*self::PAGE_SIZE,
            'limit'=> self::PAGE_SIZE
        ]);
    }

    public function actionEdit($id=null)
    {
        $this->app->extensions->ckeditor->init();
        $this->app->extensions->ckfinder->init();

        if (null === $id || 'new' == $id) {
            $this->data->item = new Document();
        } else {
            $this->data->item = Document::findByPK($id);
        }
    }

    public function actionSave($redirect = 0)
    {
        if (!empty($_POST[Document::PK])) {
            $item = Document::findByPK($_POST[Document::PK]);
        } else {
            $item = new Document();
        }
        $item->fill($_POST);
        if ($item->isNew()) {
            $item->published = date('Y-m-d H:i:s', time());
        }
        $item
            ->uploadFiles('files')
            ->save();
        if ($redirect) {
            $this->redirect('/documents/' . $item->getPk() . '.html');
        } else {
            $this->redirect('/admin/documents/');
        }
    }

    public function actionDelete($id)
    {
        $item = Document::findByPK($id);
        $item->delete();
        $this->redirect('/admin/documents/');
    }

    public function actionDeleteFile($id)
    {
        $item = File::findByPK($id);
        if ($item) {
            $item->delete();
            $this->data->result = true;
        } else {
            $this->data->result = false;
        }
    }

    public function actionCategories()
    {
        $this->data->items = Category::findAllTree();
    }

    public function actionEditCategory($id=null)
    {
        if (null === $id || 'new' == $id) {
            $this->data->item = new Category();
        } else {
            $this->data->item = Category::findByPK($id);
        }
    }

    public function actionSaveCategory()
    {
        if (!empty($_POST[Category::PK])) {
            $item = Category::findByPK($_POST[Category::PK]);
        } else {
            $item = new Category();
        }
        $item->fill($_POST);
        $item->save();
        $this->redirect('/admin/documents/categories/');
    }

    public function actionDeleteCategory($id)
    {
        $item = Category::findByPK($id);
        if ($item) {
            $item->delete();
        }
        $this->redirect('/admin/documents/categories/');
    }

    public function actionCategoryUp($id)
    {
        $item = Category::findByPK($id);
        if (empty($item))
            $this->redirect('/admin/documents/categories');
        $sibling = $item->getPrevSibling();
        if (!empty($sibling)) {
            $item->insertBefore($sibling);
        }
        $this->redirect('/admin/documents/categories');
    }

    public function actionCategoryDown($id)
    {
        $item = Category::findByPK($id);
        if (empty($item))
            $this->redirect('/admin/documents/categories');
        $sibling = $item->getNextSibling();
        if (!empty($sibling)) {
            $item->insertAfter($sibling);
        }
        $this->redirect('/admin/documents/categories');
    }

    public function actionCategoryMoveBefore($id, $to)
    {
        try {
            $item = Category::findByPK($id);
            if (empty($item)) {
                throw new Exception('Source element does not exist');
            }
            $destination = Category::findByPK($to);
            if (empty($destination)) {
                throw new Exception('Destination element does not exist');
            }
            $item->insertBefore($destination);
            $this->data->result = true;
        } catch (Exception $e) {
            $this->data->result = false;
            $this->data->error = $e->getMessage();
        }
    }

    public function actionCategoryMoveAfter($id, $to)
    {
        try {
            $item = Category::findByPK($id);
            if (empty($item)) {
                throw new Exception('Source element does not exist');
            }
            $destination = Category::findByPK($to);
            if (empty($destination)) {
                throw new Exception('Destination element does not exist');
            }
            $item->insertAfter($destination);
            $this->data->result = true;
        } catch (Exception $e) {
            $this->data->result = false;
            $this->data->error = $e->getMessage();
        }
    }

}