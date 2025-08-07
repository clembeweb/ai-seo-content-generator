# AI Generate SEO Content

Plugin WordPress che genera descrizioni e meta tag SEO tramite OpenAI.

## Setup
1. `composer install`
2. Configurare l'API Key OpenAI: `Settings → AI SEO` o definire `OPENAI_API_KEY` in `wp-config.php`.
3. Attivare il plugin da **Plugin → Installed Plugins**.

## Flusso d'uso
- Tab **Business Info**: inserisci le informazioni generali dell'azienda.
- Tab **URL List**: aggiungi gli URL da processare (uno per riga).
- Tab **SEO Results**: seleziona gli URL e premi **Generate** per ottenere i contenuti AI, esportabili in CSV.

## Endpoint REST
- `POST /wp-json/aigsc/v1/generate` – genera contenuto.
- `POST /wp-json/aigsc/v1/delete` – elimina risultati.
- `GET  /wp-json/aigsc/v1/export` – esporta CSV.

## Esempio chiamata AI
```bash
curl -H "X-WP-Nonce: <nonce>" -X POST \
  -d 'type=description&url=https://example.com' \
  https://your-site.test/wp-json/aigsc/v1/generate
```

## Test unitario (PHPUnit)
```php
class SeoGeneratorTest extends \PHPUnit\Framework\TestCase {
    public function test_prompt_building() {
        $gen = new \AiGSC\SeoGenerator( $GLOBALS['wpdb'] );
        $method = new \ReflectionMethod( $gen, 'buildPrompt' );
        $method->setAccessible( true );
        $prompt = $method->invoke( $gen, 'meta', 'https://a.com', 'biz' );
        $this->assertStringContainsString( 'meta description', $prompt );
    }
}
```

## Troubleshooting
- Verifica che l'API key sia valida.
- Controlla i log di PHP per eventuali errori di rete o rate limit.

