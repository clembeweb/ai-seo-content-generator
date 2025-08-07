<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_info = sanitize_text_field($_POST['business_info']);
    aigsc_save_business_info($business_info);
    echo '<p>Dati salvati con successo! <a href="' . esc_url(home_url('ai-generate-seo-content/?step=main')) . '">Torna alla pagina principale</a>.</p>';
} else {
    $business_info = aigsc_get_business_info();
?>
<div class="aigsc-business-info">
    <h2>Informazioni Business</h2>
    <form method="post">
        <label for="business_info">Business Info:</label>
        <input type="text" name="business_info" id="business_info" value="<?php echo esc_attr($business_info); ?>" class="regular-text" required>
        <button type="submit" class="button button-primary">Salva</button>
    </form>
</div>
<?php } ?>

<style>
    .aigsc-business-info {
        max-width: 600px;
        margin: 0 auto;
    }

    .aigsc-business-info label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .aigsc-business-info input {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
    }

    .aigsc-business-info .button {
        padding: 10px 20px;
        font-size: 16px;
    }
</style>
