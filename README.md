# Overapi v1.0
Questo piccolo framework serve per scrivere in modo rapido e leggero API restful. Inizialmente sviluppato come parte server-side del gioco Overdrive (www.overdriverpg.it), sei libero di poterlo clonare, forkarlo, modificare per conto tuo o suggerire dei miglioramenti tramite pull request. :+1:

## Requisiti
Attualmente è stato progettato per PHP 7.4 e MySQL su Apache, ma utilizza le PDO quindi per il DB dovrebbe adattarsi a molteplici tipi di database. È stato progettato per adattarsi alla maggior parte degli hosting web semplici (tipo Altervista se sei povero :money_with_wings:).

## Istruzioni
Il progetto si divide in Controllers, Models, Services e Repository. I Controller si occupano di ricevere le chiamate dall'esterno (al momento, solo GET e POST), i quali chiamano i Service che si occupano di ottenere i dati dai Repository sottoforma di Model (o altri dati).

### Controller
I controller vanno sempre definiti con nome_controller. Contengono i metodi che serviranno come endpoint definiti in routes.php.
```php
<?php namespace application\controllers;

class Example_Controller {
    public static function list() {
        return ExampleService::get_data($_GET['query']);
    }   
}
```

### Model
I Model sono gli oggetti che rappresentano i record del DB. Devono estendere la superclasse Entity.
Contengono nativamente la serializzazione json, ma vanno definiti quali attributi rendere parsabili tramite l'attributo $serializable.
```php
<?php namespace application\models;

class ExampleModel extends Entity {
    private array $serializable = ['name', 'age'];
    
    public function getName(): string {
        return $this->get_prop('name');
    }
}
```
L'entità del Model viene inizializzata passando direttamente la riga del resultset come array associativo (vedi esempio su repository).
Con il metodo `get_prop($key)` e `set_prop($key, $value)` è possibile leggere e impostare gli attributi dell'entità.
Nell'array `$serializable` vanno messe le chiavi degli attributi che possono essere parsabili.
```php
$example = new ExampleModel(['name'=>'Francesco','age'=>33, 'id'=>2]);
json_encode($example);
```
Con questa operazione viene restituito un json con gli attributi "name" ed "age", ma non "id" perché non è incluso nell'array `$serializable`.

### Repository
I Repository si occupano di fare da middleware tra database e applicazione, eseguendo le query di base. Ogni repository dev'essere sottoclasse di CommonRepository dovrebbe rappresentare una tabella del DB.
Per ottenere la PDO di connessione, eseguire il comando `self::get_connection()`. Esempi
```php
<?php namespace application\repositories;
use application\models\ExampleModel;
use PDO;

class ExampleRepository extends CommonRepository {
    public static function get_model(int $model_id): ExampleModel {
        $stmt = self::get_connection()->prepare('select * from examples where id = :model_id');
        $stmt->bindParam(':model_id', $model_id);
        $stmt->execute();
        return new ExampleModel($stmt->fetch(PDO::FETCH_ASSOC));
    }   
}
```
Al momento non è implementata alcuna security particolare oltre agli statement del PDO, ma il metodo `safe_string($str)` dovrebbe essere utilizzato quando possibile. 

### Service
I Services sono la parte che si occupa di collegare Controller, Model e Repository. Un flusso esempio sarebbe quello nel quale il controller chiama il metodo del service, questo chiama uno o più repository per ottenere dei dati, quindi li elabora e li restituisce tramite Model come risposta. Nel servizio vanno anche gestite, se necessario, le transazioni al database. Non ci sono esempi particolari da tener presente.
Per ottenere il giocatore attualmente collegato, chiama il metodo `PlayerService::get_logged_player()`, questo restituirà l'oggetto giocatore se loggato, altrimenti null.
Nel caso tu voglia soltanto ottenere l'ID del giocatore, usa il metodo `current_player_id()` che restituirà l'ID del giocatore. Se ti serve solo l'ID questo metodo è consigliato, poiché non richiedendo l'accesso al database, sarà più veloce.

#### Mail Service
Nota di merito particolare a `MailService`. Questo servizio speciale serve per inviare email agli utenti e amministratori. Il comando `send_mail` invia mail generiche, mentre `send_service_mail` la invia agli amministratori (definiti nel database nella tabella settings, parametro *admin_mails*).
Il Mail service è capace anche di caricare template html definiti in application/templates/mails.

## Configurazioni
Il file config.php contiene i settaggi di base dell'applicazione.
Il file routes.php contiene gli endpoint esposti dall'applicazione. Esempio:
```
'example' => [
   'get' => [
     'examples: list'
   ]
 ]
```
Con questa configurazione ho abilitato l'endpoint `.../api/example/examples` a chiamare il metodo `list` di `Example_Controller` quando viene effettuata una GET.
Se vuoi che un endpoint sia disponibile solo se l'utente è collegato, metti un asterisco prima del nome del metodo in routes.
```
'example' => [
   'get' => [
     '*examples: list'
   ]
 ]
```
in questo modo se qualcuno tenta di accedere alla risorsa senza prima aver fatto il login, riceverà -1 come risposta.

### Rest API
Le richieste vanno fatte in questo modo: nomedomin.io/api/controller/metodo?altri=parametri?opzionali
Se aggiunti alle routes, l'applicazione andrà a chiamare il metodo del controller.
Specificando anche il tipo file (ad esempio, nomedomin.io/api/books/list.json) verrà automaticamente convertito in json.
Al momento i formati supportati sono json, xml (da migliorare), txt, html, css.

### Autenticazione e sicurezza.
La sicurezza va trattata seriamente. Per ogni azione che necessita un giocatore valido, è necessario il suo ID (pubblico) e un codice segreto chiamato _game_token_.
Quando un nuovo giocatore viene creato, il client genera un game_token casuale e lo passa al servizio. Il servizio crea il giocatore e restituisce l'ID del giocatore
creato come risultato.
Autenticarsi per ID migliora le prestazioni, visto che la ricerca tramite stringa lunga centinaia di caratteri è decisamente più lenta.
Il token non è salvato in chiaro, ma codificato tramite algoritmo sha3-256, che genera una stringa lunga 128 caratteri.
Per codificare una stringa o una password, usa il metodo `password_encode($str)`.
L'applicativo supporta un tipo di autenticazione basato su ID giocatore e token di autenticazione, ma se hai bisogno, puoi
fare ciò che vuoi, anche creare username e password.

## Build & Test
Per scaricare tutte le librerie ed eseguire i test, esegui lo script local_build.bat (da Windows) o local_build.sh (da Unix).
Scaricherà Composer e tutti i componenti necessari, quindi eseguirà gli Unit Test.

## Licenza
L'applicativo è rilasciato sotto licenza MIT https://opensource.org/licenses/MIT

## Lista attività
Qui sotto sono elencati i traguardi raggiunti e i progetti futuri
- [x] Configurazione routes
- [x] supporto serializzazione json
- [ ] supporto serializzazione xml
- [x] Servizio Mail
- [ ] Inizializzazione e migrazione database da riga di comando
- [ ] Test
- [ ] Registrazione utente (username, password)
- [ ] Collegamento utente-partita
- [x] Implementazione sicurezza *sha3-256*
- [x] Procedure in transazione
- [ ] Supporto a *PUT*, *PATCH*, *DELETE*
- [x] Supporto sessioni (per web)
- [ ] Pagina di amministrazione

## FAQ
**D: Posso scaricare l'applicativo e usarlo per il mio progetto?**
R: Sì, ma le funzioni sono personalizzate per Overdrive. Potresti Dover modificare qualcosa per adattarlo al tuo progetto.

**D: Posso usarlo anche per progetti che non riguardano i giochi?**
R: Sì, certamente!

**D: Di cosa ho bisogno per far funzionare questo servizio?**
R: Basta un hosting web che supporti Apache e PHP 7.4. Al momento la maggior parte dei servizi di hosting supporta fino a 7.3, ma confido che presto tutti aggiorneranno a 7.4. Se vuoi sperimentare sul tuo computer, ti consiglio di installare [XAMPP](https://www.apachefriends.org/it/index.html) per avere server, PHP e database in locale in modo rapido.
Oltre a questo, ovviamente il tuo gioco deve poter inviare richieste GET e POST. Se usi RPG Maker VX o VX Ace, prova il mio script [Modulo di Supporto Generale](http://www.rpg2s.net/forum/index.php/topic/17338-%E2%9A%99%EF%B8%8F-modulo-di-supporto-di-holy87/).

**D: Come inizializzo il progetto?**
R: Al momento non c'è una procedura automatica. Ti basta però aprire il file database/base.sql ed eseguirlo come un'unica, grande query dopo aver creato un database.

**D: Ho dei miglioramenti da proporre. Come posso contribuire?**
R: Su Github, avvia una pull request o apri una nuova issue.
