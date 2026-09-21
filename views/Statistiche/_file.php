<?php
use yii\helpers\Html;
use yii\helpers\Url;
 use yii\bootstrap4\Modal;
// Recupera il codice agente
// 🔍 Controlla se è passato un parametro "agente" nell'URL
$cd_agente = Yii::$app->request->get('agente', null);

// Se non è passato, usa quello dell'utente loggato
if ($cd_agente === null) {
    $cd_agente = Yii::$app->user->identity->cd_agente;
}

// Recupera tutti i record
$records = \app\models\Agentifiles::find()
    ->where(['cd_agente' => $cd_agente])
    ->orderBy(['cartella_padre' => SORT_ASC, 'cartella' => SORT_ASC])
    ->asArray()
    ->all();

Modal::begin([
    'id' => 'uploadModal',
    'title' => '<h4>Carica nuovo file</h4>',
    'size' => Modal::SIZE_LARGE,
]);

// Qui il contenuto sarà caricato dinamicamente via Ajax
echo '<div id="modalContent"></div>';

Modal::end();
 


/**
 * Costruisce la gerarchia corretta
 */
function buildTree(array $elements, $parent = null): array {
    $tree = [];

    // 1️⃣ Trova tutte le cartelle il cui cartella_padre corrisponde al parent
    foreach ($elements as $el) {
        if ($el['estenzione'] === 'dir' && $el['cartella_padre'] == $parent) {
            $cartella = $el['cartella'];

            // 2️⃣ Inserisci la cartella nel tree se non già presente
            if (!isset($tree[$cartella])) {
                $tree[$cartella] = [
                    'type' => 'folder',
                    'data' => $el,
                    'children' => buildTree($elements, $cartella),
                    'files' => []
                ];
            }

            // 3️⃣ Aggiungi eventuali file contenuti in questa cartella
            foreach ($elements as $f) {
                if (
                    $f['estenzione'] !== 'dir' &&
                    $f['cartella'] == $cartella &&
                    $f['cartella_padre'] == $parent
                ) {
                    $tree[$cartella]['files'][] = $f;
                }
            }
        }
    }

    return array_values($tree);
}


function buildTreeWithFiles(array $elements, $parent = null): array {
    $tree = [];

    foreach ($elements as $el) {
        if ($el['estenzione'] === 'dir' && $el['cartella_padre'] == $parent) {
            $cartella = $el['cartella'];

            // Ricorsione per figli
            $children = buildTreeWithFiles($elements, $cartella);

            // Trova file diretti di questa cartella
            $files = [];
            foreach ($elements as $f) {
                if ($f['estenzione'] !== 'dir' && $f['cartella'] == $cartella && $f['cartella_padre'] == $parent) {
                    $files[] = $f;
                }
            }

            // Inserisci la cartella solo se ha file o sotto-cartelle non vuote
            if (!empty($files) || !empty($children)) {
                $tree[$cartella] = [
                    'type' => 'folder',
                    'data' => $el,
                    'children' => $children,
                    'files' => $files
                ];
            }
        }
    }

    return array_values($tree);
}






$tree = buildTreeWithFiles($records);

/**
 * Stampa ricorsiva (cartelle e file)
 */
function renderTree(array $nodes, $level = 0)
{
    if (empty($nodes)) return;

    echo '<ul class="list-unstyled">';
    foreach ($nodes as $node) {
        $data = $node['data'];
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
        $hasChildren = !empty($node['children']);
        $hasFiles = !empty($node['files']);

        echo '<li class="folder-item">';
        echo $indent;

        // 📁 Cartella
       // echo '<span class="toggle-icon text-primary me-1" data-open="1">➖</span>';
echo '<span class="toggle-icon text-primary me-1" data-open="0">➕</span>';
echo '<span class="folder-icon text-warning me-1">📁</span>';
        echo '<span class="folder-name fw-bold">' . Html::encode($data['cartella']) . '</span>';

        // 👇 Contenuti della cartella
        if ($hasChildren || $hasFiles) {
         //   echo '<div class="children" style="margin-left:20px;">';
echo '<div class="children" style="margin-left:20px; display:none;">';
            // 📄 FILE
            if ($hasFiles) {
                echo '<ul class="list-unstyled ms-3">';
foreach ($node['files'] as $file) {
    $hasDetails = !empty($file['nota']) || !empty($file['data_scadenza']);

    echo '<li class="file-item mb-2">';
    echo '<div class="file-header d-flex align-items-center">';

    // Icona file + nome
    echo '<span class="me-1">📄</span>';
    echo '<strong>' . Html::encode($file['nome_file']) . '</strong>';
    echo ' (' . Html::encode($file['estenzione']) . ')';

    // Link download
    echo Html::a(' ⬇️', ['statistiche/download', 'id' => $file['id']], [
        'class' => 'text-decoration-none ms-2',
        'target' => '_blank',
        'title' => 'Scarica file'
    ]);

    // Se ci sono dettagli, mostra icona toggle
    if ($hasDetails) {
        echo '<span class="toggle-file ms-2 text-primary" data-open="0" style="cursor:pointer;">🔽</span>';
    }

    echo '</div>';

    // Dettaglio nascosto
    if ($hasDetails) {
     echo '<div class="file-details mt-1 ms-4" style="display:none;">';
    echo '<table class="table table-sm table-bordered" style="width:40%; table-layout:fixed;">';
        if (!empty($file['nota'])) {
            echo '<tr><th style="width:120px;">Note</th>
             <td style="word-wrap:break-word; white-space:normal;">
                    ' . nl2br(htmlspecialchars($file['nota'])) . '
                </td></tr>';
        }
        if (!empty($file['data_scadenza'])) {
            echo '<tr><th>Data Scadenza</th><td>' . 
            Yii::$app->formatter->asDate($file['data_scadenza'], 'php:d/m/Y') . 
            '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    echo '</li>';
}


                echo '</ul>';
            }

            // 📁 SOTTOCARTELLE
            renderTree($node['children'], $level + 1);
            echo '</div>';
        }

        echo '</li>';
    }
    echo '</ul>';
}
?>
<?php
$script = <<< JS
$('#openUploadModal').on('click', function(e){
    e.preventDefault();
    var url = $(this).attr('href');
    $('#modalContent').html('Caricamento in corso...');
    $('#uploadModal').modal('show')
        .find('#modalContent')
        .load(url);
});
JS;
$this->registerJs($script);
?>



<!-- 🔍 Ricerca + Pulsanti -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" id="treeSearch" class="form-control w-50" placeholder="Cerca cartella o file...">
    <div>
        <button id="expandAll" class="btn btn-sm btn-outline-success">Espandi tutto</button>
        <button id="collapseAll" class="btn btn-sm btn-outline-secondary">Collassa tutto</button>
    </div>
</div>
<p>
    <?php 
    if ($lvl = Yii::$app->user->identity ->level >=80){} // livello utente)
    
    echo  Html::a('📁 Carica nuovo file', ['statistiche/upload-file'], [
        'class' => 'btn btn-primary',
        'id' => 'openUploadModal',
        'data-bs-toggle' => 'modal', // Bootstrap 5
        'data-bs-target' => '#uploadModal'
    ]) ;
    
    ?>
</p>


<!-- 🌲 Struttura ad albero -->
<div id="treeContainer" class="border rounded p-3"  >
    <?php renderTree($tree); ?>
</div>

<?php
$js = <<<JS
// 🌲 Toggle su icona ➕/➖ e anche sul nome cartella
function toggleFolder(element) {
    const li = element.closest('li');
    const childDiv = li.querySelector(':scope > .children');
    const toggleIcon = li.querySelector(':scope > .toggle-icon');
    const folderIcon = li.querySelector(':scope > .folder-icon');

    if (childDiv) {
        const isOpen = toggleIcon.dataset.open === '1';
        childDiv.style.display = isOpen ? 'none' : 'block';
        toggleIcon.textContent = isOpen ? '➕' : '➖';
        folderIcon.textContent = isOpen ? '📁' : '📂';
        toggleIcon.dataset.open = isOpen ? '0' : '1';
    }
}

// Evento click su ➕ / ➖
document.querySelectorAll('.toggle-icon').forEach(icon => {
    icon.style.cursor = 'pointer';
    icon.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleFolder(this);
    });
});

// Evento click sul nome della cartella 📁
document.querySelectorAll('.folder-name').forEach(name => {
    name.style.cursor = 'pointer';
    name.addEventListener('click', function(e) {
        e.stopPropagation();
        const li = this.closest('li');
        const toggleIcon = li.querySelector(':scope > .toggle-icon');
        if (toggleIcon) toggleFolder(toggleIcon);
    });
});


// 📂 Espandi tutto
document.getElementById('expandAll').addEventListener('click', function() {
    document.querySelectorAll('.children').forEach(div => div.style.display = 'block');
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.textContent = '➖';
        icon.dataset.open = '1';
    });
    document.querySelectorAll('.folder-icon').forEach(icon => icon.textContent = '📂');
});

// 📁 Collassa tutto
document.getElementById('collapseAll').addEventListener('click', function() {
    document.querySelectorAll('.children').forEach(div => div.style.display = 'none');
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.textContent = '➕';
        icon.dataset.open = '0';
    });
    document.querySelectorAll('.folder-icon').forEach(icon => icon.textContent = '📁');
});

// 🔎 Ricerca live
document.getElementById('treeSearch').addEventListener('keyup', function() {
    const search = this.value.toLowerCase();
    const items = document.querySelectorAll('#treeContainer li');
    items.forEach(function(li) {
        const text = li.textContent.toLowerCase();
        li.style.display = (search === '' || text.includes(search)) ? '' : 'none';
    });
});


// 🔽 Toggle dettagli file
document.querySelectorAll('.toggle-file').forEach(btn => {
    btn.addEventListener('click', function() {
        const parent = this.closest('.file-item');
        const details = parent.querySelector('.file-details');
        const isOpen = this.dataset.open === '1';
        details.style.display = isOpen ? 'none' : 'block';
        this.textContent = isOpen ? '🔽' : '🔼';
        this.dataset.open = isOpen ? '0' : '1';
    });
});



JS;

$this->registerJs($js);
?>

<style>
/* 📁 Cartelle */
.folder-item {
    line-height: 2em;            /* più spazio verticale */
    white-space: nowrap;
}

.folder-icon {
    font-size: 1.6em;            /* dimensione icona cartella */
    vertical-align: middle;
}

.folder-name {
    font-size: 1.2em;            /* testo più grande */
    font-weight: 600;            /* leggermente più marcato */
    color: #0d6efd;              /* blu Bootstrap */
    cursor: pointer;
}

/* 📄 File */
.file-item {
    margin-left: 25px;
    font-size: 1em;
}

.file-item .file-header strong {
    font-size: 1.05em;
}

/* ➕ / ➖ Toggle */
.toggle-icon {
    font-size: 1.4em;            /* ingrandisci simboli + e - */
    cursor: pointer;
    user-select: none;
}

/* 🌳 Struttura principale */
#treeContainer {
    width: 100%;
    height: calc(100vh - 150px);
    overflow: auto;
    font-family: "Segoe UI", Roboto, sans-serif;
}

/*style="max-height: 80vh; overflow-y:auto; width:200%;"*/
</style>
