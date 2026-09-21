 

<?php
use yii\helpers\Html;
use kartik\tree\TreeView;
Html::csrfMetaTags() ;
use app\models\Folders; // Assicurati di includere il modello corretto
$selectedNode = null; //
echo TreeView::widget([
    'query' => Folders::find()->addOrderBy('root, lft'),
    'headingOptions' => ['label' => 'Documenti'],
    'fontAwesome' => true,
    'isAdmin' => true,
    'displayValue' => 0,
    'softDelete' => true,
    'cacheSettings' => [
        'enableCache' => false
    ],
    'iconEditSettings'=> [
        'show' => 'list',
        'listData' => [
            'folder' => 'Folder',
            'file' => 'File',
            'mobile' => 'Phone',
            'bell' => 'Bell',
               'tag' => 'Tag'
        ]
    ],
    'nodeAddlViews' => [
                1 => '',
                2 => '@app/views/Folders/formt',
                3 => '',
                4 => '',
                5 => '',
        ]
  
 
]);
?>


</div>
