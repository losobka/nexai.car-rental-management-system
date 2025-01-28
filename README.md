# NEXAI car rental management system
MVP bazy wypożyczalni samochodów, stworzone przy użycu **Symfony**, **API Platform** oraz **AngularJS**, na potrzeby udziału w rekrutacji do firmy **NEXAI**. Tworzone po godzinach etatowej pracy.

## Usage
```shell
# if make is not provided then use `docker compose up --remove-orphans --timestamps --build --detach` command
make start

# to open SPA
make open-app # http://localhost:8000/

# to open API Swagger UI documentation
make open-docs # http://localhost:8000/docs
```

## FAQ
### Dlaczego API Platform zamiast NelmioApiDocBundle?
Ponieważ używając **NelmioApiDocBundle** wygenerowałbym tylko **Swagger**-a, korzystając z **API Platform** pominąłem generowanie m.in. kontrolerów dla podstawowych operacji (dla customowych musiałem *StateProvidery* i *StateProcessory* stworzyć) - zgodnie z instrukcją:
> Jeśli masz wrażenie, że brakuje Ci jakiś informacji w poleceniu to działaj według swojej intuicji i uznania :)

Możliwie szybkie dostarczcenie *MVP* było dla mnie priorytetem (i domniemanym celem zadania).

### Mam błąd "502 Bad gateway" pod adresem http://localhost:8000/docs
Błąd wysępuje sporadycznie, kontener **nginx** nie może określić poprawnego adresu IP kontenera **php-fpm**

```shell
# tymczasowe rozwiązanie
docker compose restart php-fpm
docker compose restart nginx
```

### Czy tylko frontend waliduje dane z formularzy?
Nie, walidacją zajmuje się również *backend* - w przypadku obejścia walidacji na *frontendzie*, odpowiednie komunikaty zostaną wyrenderowane.

```angular17html
<div ng-if="violations.hasOwnProperty('vin')" ng-messages="violations.vin">
    <div ng-message-default ng-repeat="violation in violations.vin">{{violation}}</div>
</div>
```

### Dlaczego nie można edytować pola Car.vin?
Ponieważ **VIN** jest przypisany do konkretnego pojazdu, to typowy niezmiennik.

### Co z przyszłościowym wykorzystywaniem danych adresowych w innych modułach?
Podczas tworzenia i aktualizacji **Rental** na podstawie wartości **billingAddress** jest tworzone **App\Entity\Address** (tabela *address_book*)

## License
MIT