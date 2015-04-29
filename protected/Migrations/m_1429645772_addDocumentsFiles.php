<?php

namespace App\Migrations;

use T4\Orm\Migration;

class m_1429645772_addDocumentsFiles
    extends Migration
{

    public function up()
    {
        $this->createTable('documentsfiles', [
            '__document_id' => ['type' => 'link'],
            'file' => ['type' => 'string'],
        ]);
    }

    public function down()
    {
        $this->dropTable('documentsfiles');
    }

}