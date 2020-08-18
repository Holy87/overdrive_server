## Overapi v1.0
Questo piccolo framework serve per scrivere in modo rapido e leggero API restful. Inizialmente sviluppato come parte server-side del gioco Overdrive (www.overdriverpg.it)

#### Requisiti
Attualmente è stato progettato per PHP 7.4 e MySQL su Apache, ma utilizza le PDO quindi per il DB dovrebbe adattarsi a qualsiasi.

#### Istruzioni
Il progetto si divide in Controllers, Models, Services e Repository.
I controller vanno sempre definiti con nome_controller. Contengono i metodi che serviranno come endpoint.

I Model sono gli oggetti che rappresentano i record del DB. Devono estendere la superclasse Entity.
Contengono nativamente la serializzazione json, ma vanno definiti quali attributi rendere parsabili tramite l'attributo $serializable.

I Repository si occupano di fare da middleware tra database e applicazione, eseguendo le query di base. Ogni repository dovrebbe rappresentare una tabella del DB.

I Services sono la parte che si occupa di collegare Controller, Model e Repository. Un flusso esempio sarebbe quello nel quale il controller chiama il metodo del service, questo chiama uno o più repository per ottenere dei dati, quindi li elabora e li restituisce tramite Model come risposta.

##### Configurazioni
Il file config.php contiene i settaggi di base dell'applicazione.
Il file routes.php contiene gli endpoint esposti dall'applicazione.