  <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    //echo '<th>id_agenda</th>';
                    echo '<th width="70%">file</th>';
                    echo '<th width="30%">';
                    echo '</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    //yii::error( ($model->xid_testa ));
                    //yii::warning($model->filesall);
                    foreach ($model->filesall as $value) {
                        // echo '<tr>';

                        if ($value['entita'] = 'Xtravelrow') {
                            echo '<tr>';
                            echo '<td>';

                            echo Html::a(
                                $value['nomefile'],
                                [
                                    'allfiles/genfile',
                                    'id' => $value['id'],
                                    'file' => str_replace(' ', '_', $value['nomefile'])
                                ]
                            );

                            echo '</td>';

                            echo '<td>';

                            if ($value['origine'] == 'S') {
                                //echo 'usrld';

                                echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                                Yii::$app->fontawesome->name(
                                    'user',
                                    'solid'
                                )->fill('#003865');
                            } else {

                                echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                                Yii::$app->fontawesome->name(
                                    'server',
                                    'solid'
                                )->fill('#003865');
                            }

                            echo '</td>';

                            echo '</tr>';
                        }
                    }
                    echo '<tr id="files-new-parcel-block" style="display: none;">';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    ?>
