<?php
use yii\helpers\Html;
?>

<div class="pdf-container">
    <h2 style="text-align: center; text-transform: uppercase;">Procedure</h2>

    <?php if (empty($planningRaggruppato)): ?>
        <p style="text-align: center;">Nessun intervento programmato per oggi.</p>
    <?php else: ?>

        <?php 
        $totaleVeicoli = count($planningRaggruppato);
        $contatore = 0;
        ?>
        
        <?php foreach ($planningRaggruppato as $veicoloLabel => $dati): 
            $contatore++;
            // Recuperiamo i nomi aggregati dal controller
            $nomiSquadra = implode(', ', $dati['nomi_personale']);
            // Recuperiamo la lista delle attività
            $tappe = $dati['attivita'];
        ?>
            <div style="margin-bottom: 50px;">
                <table class="header-info">
                    <tr>
                        <td class="bg-grey" style="width: 20%;">Data</td>
                        <td style="width: 80%;"><?= date('d/m/Y', strtotime($data)) ?></td>
                    </tr>
                    <tr>
                        <td class="bg-grey">Autista/i Squadra</td>
                        <td style="font-weight: bold; text-transform: uppercase;">
                            <?= !empty($nomiSquadra) ? Html::encode($nomiSquadra) : 'Nessuno' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-grey">Mezzo e Note</td>
                        <td><?= Html::encode($veicoloLabel) ?></td>
                    </tr>
                </table>

                <table class="table-main">
                    <thead>
                        <tr class="bg-grey">
                            <th style="width: 5%; text-align: center;">Nr.</th>
                            <th style="width: 65%;">Cliente / Luogo / Attività</th>
                            <th style="width: 15%; text-align: center;">Ora arrivo</th>
                            <th style="width: 15%; text-align: center;">Ora fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tappe as $tappa): ?>
                            <tr>
                                <td style="text-align: center; font-weight: bold; font-size: 14px;">
                                    <?= $tappa->giro ?>
                                </td>
                                <td>
                                    <span style="font-weight: bold; text-transform: uppercase;">
                                        <?= $tappa->cliente ? Html::encode($tappa->cliente->Descrizione) : 'N/D' ?>
                                    </span>
                                    <br>
                                    <small><?= Html::encode($tappa->indirizzo) ?></small>
                                    <br>
                                    <span style="font-style: italic; font-size: 11px;">
                                        <?= Html::encode($tappa->descrizione) ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <?= !empty($tappa->ora_inizio) ? substr($tappa->ora_inizio, 0, 5) : 'n.d.' ?>
                                </td>
                                <td style="text-align: center;">
                                    <?= !empty($tappa->ora_fine) ? substr($tappa->ora_fine, 0, 5) : '' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($contatore < $totaleVeicoli): ?>
                <pagebreak />
            <?php endif; ?>

        <?php endforeach; ?>

    <?php endif; ?>
</div>