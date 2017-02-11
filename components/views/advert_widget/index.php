
<h3 class="droog">Другие предложения</h3>

<?php
    Pjax::begin([
        'id' => 'listview_pjax',
    ]);

    echo ListView::widget([
        'dataProvider' => $this->trans_dp,
        'itemView' => '_list_listview',
        'layout' => "{items}\n{pager}",
    ]);

    Pjax::end();
?>