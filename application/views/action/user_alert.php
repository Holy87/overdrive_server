<?php
/** @var \application\models\BoardMessage  $message */
$message = $_SESSION['message'];
$action = $_SESSION['action'];
?>
<div class="container">
    <input type="hidden" id="message_id" value="<?php echo $message->get_id() ?>"/>
    <input type="hidden" id="author_id" value="<?php echo $message->get_author_id() ?>"/>
    <h2>Segnalazione</h2>
    <p>Intraprendi azione sul messaggio di <?php echo $message->get_author()->get_name() ?></p>
    <h5>Messaggio:</h5>
    <p><?php echo $message->get_message() ?></p>
    <hr>
    <div id="button-group" >
        <button id="accept" class="btn btn-secondary">Non far nulla</button>
        <button id="delete" class="btn btn-warning">Elimina</button>
        <?php if ($message->has_author()) { ?>
            <button id="ban" class="btn btn-danger">Banna utente</div>
        <?php } ?>
    </div>
</div>