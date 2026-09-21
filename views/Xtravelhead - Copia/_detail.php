<?php

    use kartik\export\ExportMenu;
    use kartik\grid\GridView;
$this->registerCss('
    .full-screen-container {
        width: 100%;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        margin: 10px; /* Aggiungiamo margine per separare i blocchi */
    }

    @media (min-width: 768px) {
        .full-screen-container {
            width: calc(33.33% - 20px); /* Calcoliamo la larghezza per fare 3 colonne in una riga */
        }
    }

    .AGE-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .AGE-info {
        margin-bottom: 15px;
    }

    .AGE-info-label {
        font-weight: bold;
    }

    .AGE-info-value {
        margin-left: 10px;
    }

    .AGE-actions {
        margin-top: 20px;
    }

    .AGE-actions .btn {
        margin-right: 10px;
    }

    .full-width {
        width: 100% !important;
        max-width: none !important;
    }
        .chart-container {
    width: 300px;
    height: 200px;
}
    .card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.custom-card {
    width: 50%;
min-height: 500px;
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .custom-card {
        width: 100%;
    }
table.table-fit {
  width: auto !important;
  table-layout: auto !important;
}
table.table-fit thead th,
table.table-fit tbody td,
table.table-fit tfoot th,
table.table-fit tfoot td {
  width: auto !important;
}
  table td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table-total {
    background-color: #007bff;
}

');

?>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Tappa <?php echo $citta; ?></h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Commessa</TH>
                    <TH>CLiente</TH>
                    <TH>Data Inizio</TH>
                    <TH>Data Fine</TH>
                    <TH>Totale</TH>
                    <TH>Tassa</TH>
                    <TH>Fee</TH>
                    <TH>Totale Generale</TH>
                </thead>
                <?php
                $tottassa = 0;
                $totfee = 0;
                $tottalecard = 0;
                $totgen=0;
                foreach ($totale as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['x_scdesc'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['descli'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['startdate']));
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['enddate']));
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo round($row['totale'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo round($row['tassa'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo round($row['fee'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo round($row['totalegenerale'], 2);
                    echo '</td>';
                    echo '</tr>';
                    $tottassa = $tottassa + $row['tassa'];
                    $totfee = $totfee + $row['fee'];
                    $tottalecard = $tottalecard + $row['totale'];
                    $totgen=$totgen+$row['totalegenerale'];
                }
                echo '<TR>';
                echo '<td scope="col" colspan=4 >';
                echo '<strong>TOTALE GENERALE</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€' . $tottalecard . '</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€' . $tottassa . '</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€' . $totfee . '</strong>';
                echo '</td>';
    echo '<td scope="col">';
    echo '<strong>€' . $totgen . '</strong>';
    echo '</td>';
                echo '</tr>'

                ?>

            </table>
        </div>
    </div>


    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Pagamenti</h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Data</TH>
                    <TH>Importo</TH>
                    <TH>Residuo</TH>
 
                </thead>
                <?php
                $i=1;
                $tempdapagare=0;
                $tpag=0;
                foreach ($pagamenti as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['data_pg']));
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo  round($row['xpagato'],2);
                    echo '</td>';
                    echo '<td scope="col">';
                    $tpag=$tpag+ round($row['xpagato'], 2);
                    if($i==1){
                    echo '€' .  $totgen-round($row['xpagato'], 2);
                    $tempdapagare = $totgen - round($row['xpagato'], 2);
                    }else{
                        echo '€' .  $tempdapagare - round($row['xpagato'], 2);
                        $tempdapagare = $tempdapagare - round($row['xpagato'], 2);
                    }
                    echo '</td>';
                    echo '</tr>';
                    $i++;
                }
    echo '<TR bgcolor="#007bff">';
    echo '<td><strong>Totali</strong>';
    echo '</td>';
    echo '<td>';
    echo '<strong>€ '.$tpag;
    echo '</strong></td>';
    echo '<td><strong>';
    echo '€ '. $tempdapagare;
    echo '</strong></td>';
    echo '</tr>';

                                    ?>
            </table>
        </div>
    </div>




</div>


<br>Dettaglio<br>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Commessa</th>
        <th>Cliente</th>
        <th>Cognome - Nome</th>
        <th>RUOLO</th>
        <th>PARTY</th>
        <th>STRUTTURA</th>
        <th>DATA IN</th>
        <th>DATA OUT</th>
        <th>TOT.NOTTI</th>
        <th>Notti Tax</th>
        <th>CONFERMA</th>
        <th>TIPOLOGIA</th>
        <th>COSTO Notte</th>
        <th>CITY TAX</th>
        <th>TOT COST</th>
        <th>TOT CITY</th>
        <th>TAX PARK</th>
        <th>EXTRAS</th>
        <th>TOTALE Complessivo</th>
        <th>7%</th>
        <th>FEE</th>
        <th>IVA</th>

    </thead>
    <?php
    $tqta = 0;
    $ttotale = 0;
    $ttax = 0;
    $tfee = 0;
    $tiva = 0;
    $prev_scdesc = '';
    $prev_descli = '';
    $prev_struttura = '';

    foreach ($dettaglio as $value) {

        // Check if x_scdesc, descli, or struttura has changed
        if ($prev_scdesc != '' && ($prev_scdesc != $value['x_scdesc'] || $prev_descli != $value['descli'] || $prev_struttura != $value['struttura'])) {
            // Print the totals for the previous group
            echo '<tr bgcolor="#007bff">';
            echo '<td colspan="8" ><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
            echo '<td><strong>' . $tqta . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Notti Tax"
            echo '<td></td>'; // Colonna vuota per "Conferma"
            echo '<td></td>'; // Colonna vuota per "Tipologia"
            echo '<td></td>'; // Colonna vuota per "Costo Notte"
            echo '<td></td>'; // Colonna vuota per "City Tax"
            echo '<td><strong>€' . round($ttotale, 2) . '</strong></td>';
            echo '<td><strong>€' . round($ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Tax Park"
            echo '<td></td>'; // Colonna vuota per "Extras"
            echo '<td><strong>€' . round($ttotale + $ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "7%"
            echo '<td><strong>€' . round($tfee, 2) . '</strong></td>';
            echo '<td><strong>€' . round($tiva, 2) . '</strong></td>';
            // echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
            echo '</tr>';

            // Reset totals
            $tqta = 0;
            $ttotale = 0;
            $ttax = 0;
            $tfee = 0;
            $tiva = 0;
        }

        // Print the current row
        echo '<tr>';
        echo '<td scope="col">' . $value['x_scdesc'] . '</td>';
        echo '<td scope="col">' . $value['descli'] . '</td>';
        echo '<td scope="col">' . $value['guest'] . '</td>';
        echo '<td scope="col">' . $value['ruolo'] . '</td>';
        echo '<td scope="col">' . $value['party'] . '</td>';
        echo '<td scope="col">' . $value['struttura'] . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_in'])) . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_out'])) . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col"> si</td>';
        echo '<td scope="col"> ' . $value['cd_Ar'] . '</td>';
        echo '<td scope="col"> €' . round($value['prezzo'], 2) . '</td>';
        echo '<td scope="col"> €' . round($value['tax_unit'], 2) . '</td>';
        echo '<td scope="col"> €' . round($value['totale'], 2) . '</td>';
        echo '<td scope="col"> €' . round($value['tax'], 2) . '</td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col"> €' . (round($value['totale'], 2) + round($value['tax'], 2)) . '</td>';
        echo '<td scope="col">' . round($value['fee_perc'], 2) . '% </td>';
        echo '<td scope="col"> €' . round($value['fee'], 2) . ' </td>';
        echo '<td scope="col"> €' . round($value['iva'], 2) . ' </td>';
        echo '</tr>';

        // Accumulate totals
        $tqta += $value['qta'];
        $ttotale += round($value['totale'], 2);
        $ttax += round($value['tax'], 2);
        $tfee += round($value['fee'], 2);
        $tiva += round($value['iva'], 2);

        // Update the previous values
        $prev_scdesc = $value['x_scdesc'];
        $prev_descli = $value['descli'];
        $prev_struttura = $value['struttura'];
    }

    // Print the totals for the last group
    if ($prev_scdesc != '') {
        echo '<tr class="table-total">';
        echo '<td colspan="8"><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
        echo '<td><strong>' . $tqta . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "Notti Tax"
        echo '<td></td>'; // Colonna vuota per "Conferma"
        echo '<td></td>'; // Colonna vuota per "Tipologia"
        echo '<td></td>'; // Colonna vuota per "Costo Notte"
        echo '<td></td>'; // Colonna vuota per "City Tax"
        echo '<td><strong>€' . round($ttotale, 2) . '</strong></td>';
        echo '<td><strong>€' . round($ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "Tax Park"
        echo '<td></td>'; // Colonna vuota per "Extras"
        echo '<td><strong>€' . round($ttotale + $ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "7%"
        echo '<td><strong>€' . round($tfee, 2) . '</strong></td>';
        echo '<td><strong>€' . round($tiva, 2) . '</strong></td>';
        //  echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
        echo '</tr>';
    }
    ?>
</table>

