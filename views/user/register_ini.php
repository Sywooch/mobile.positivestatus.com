<?php
    use yii\helpers\Url;
    use yii\bootstrap\Modal;
?>


<?php
    // Modal form register_email
    Modal::begin([
        'id' => 'register_modal',
        'options' => [
            'style' => 'text-align: center;',
        ],
    ]);
    Modal::end();
    
//    Modal::begin([
//        'id' => 'register_message',
//        'options' => [
//            'style' => 'padding-top:160px;',
//        ],
//    ]);
//    Modal::end();


    // Action /user/register is returning json-object {result, html}
    // Result can have only 2 values - "ok" or "err"
    $modalUrl = Url::to('/user/register');
?>


<?php $this->beginJs(); ?>
    <script>
        $("#register_modal").find(".modal-dialog").addClass("modal-sm");
        $("#register_message").find(".modal-dialog").addClass("modal-lg");

        $.getJSON("<?= $modalUrl ?>", {}, function(response) {
            $("#register_modal").find(".modal-body").html(response.html);
        });

        $("li.registration-link > a").on("click", function() {
            //var acccount_id = $(this).attr("acccount_id");
            var account_id = 1;
            $("#user-account_id").val(account_id);

            $("#user-email").val("");
            var form = $("#register_modal");
            form.find("div.field-user-email").removeClass("has-error");
            form.find("div.help-block").html("");
            form.modal("show");
        });


        $(document).on("submit", "#register_email_form", function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: "<?= $modalUrl ?>",
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                success: function (response) {
                    if (response.result == 'ok') {
                        $("#register_modal").modal("hide");
                        var n = noty({
                            text: response.html,
                            layout: 'top',
                            closeWith: ['click', 'button'],
                            type: 'center',
                            theme: 'relax',
                            callback: {
                                afterClose: function() {window.location.assign("<?= $back_url ?>");},
                            }
                        });
                        $("#noty_center_layout_container").css({'width':'80%', 'left':'10%'}).children().first().css('width', 'auto');
                    } else {
                        $("#register_modal").find(".modal-body").html(response.html);
                    }
                }
            });
        });
    </script>
<?php $this->endJs(); ?>
