# StageMatch API

Een REST API in vanilla PHP die studenten en bedrijven koppelt op basis van skills. Een student kiest zijn skills, een bedrijf maakt vacatures met de skills die het zoekt. De API berekent hoeveel procent van de gevraagde skills de student heeft en welke skills nog ontbreken.

**Live demo:** https://stagematch.alwaysdata.net

## Installatie

1. Zet het project in `C:\xampp\htdocs\stagematch-api`.
2. Importeer `database/stagematch_api.sql` in phpMyAdmin.
3. Voer `composer dump-autoload` uit in de projectmap.
4. Open http://localhost/stagematch-api/demo/ in de browser.

Staat de database ergens anders? Kopieer dan `config.example.php` naar `config.php` en vul je eigen gegevens in.

### Testaccounts

| Rol     | E-mail                | Wachtwoord   |
|---------|-----------------------|--------------|
| Student | `jan@stagematch.test` | `wachtwoord` |
| Bedrijf | `info@pixelwerk.test` | `wachtwoord` |

## Authenticatie

Log in met `POST /login`. Je krijgt een token terug, die stuur je mee bij elk beveiligd request:

```
Authorization: Bearer <token>
```

| Methode | Endpoint    | Body                                                   | Beschrijving                  |
|---------|-------------|--------------------------------------------------------|-------------------------------|
| POST    | `/register` | `name`, `email`, `password`, `role` (`student`/`company`) | Account aanmaken              |
| POST    | `/login`    | `email`, `password`                                    | Inloggen, geeft een token     |
| POST    | `/logout`   | –                                                      | Maakt de token ongeldig       |
| GET     | `/me`       | –                                                      | Laat zien wie er is ingelogd  |

## Endpoints

### Openbaar (zonder inloggen)

| Methode | Endpoint                 | Beschrijving               |
|---------|--------------------------|----------------------------|
| GET     | `/skills`                | Alle skills                |
| GET     | `/skills/{id}`           | Eén skill                  |
| GET     | `/students/{id}/skills`  | Skills van een student     |
| GET     | `/vacancies`             | Alle vacatures             |
| GET     | `/vacancies/{id}`        | Eén vacature               |

### Student

| Methode | Endpoint                         | Body                    | Beschrijving                              |
|---------|----------------------------------|-------------------------|-------------------------------------------|
| GET     | `/me/skills`                     | –                       | Eigen skills                              |
| POST    | `/me/skills`                     | `skill_id`              | Skill toevoegen                           |
| DELETE  | `/me/skills/{id}`                | –                       | Skill verwijderen                         |
| GET     | `/matches`                       | –                       | Vacatures gesorteerd op match             |
| POST    | `/vacancies/{id}/applications`   | `motivation` (optioneel)| Solliciteren                              |
| GET     | `/me/applications`               | –                       | Status van eigen sollicitaties            |

### Bedrijf

| Methode     | Endpoint                         | Body                                                  | Beschrijving                                  |
|-------------|----------------------------------|-------------------------------------------------------|-----------------------------------------------|
| GET         | `/me/vacancies`                  | –                                                     | Eigen vacatures                               |
| POST        | `/vacancies`                     | `title`, `description`, `location`, `skills` (lijst met ids) | Vacature aanmaken                      |
| PUT / PATCH | `/vacancies/{id}`                | zelfde velden                                         | Vacature aanpassen (alleen eigen vacatures)   |
| DELETE      | `/vacancies/{id}`                | –                                                     | Vacature verwijderen (alleen eigen vacatures) |
| GET         | `/me/applications`               | –                                                     | Alle sollicitaties, met match per student     |
| GET         | `/vacancies/{id}/applications`   | –                                                     | Sollicitaties op één vacature, met match      |
| PATCH       | `/applications/{id}`             | `status` (`pending`/`accepted`/`rejected`)            | Status van een sollicitatie aanpassen         |

## Matchpercentage

```
match = (gevraagde skills die de student heeft / aantal gevraagde skills) × 100
```

- Een vacature zonder gevraagde skills is altijd 100%.
- Bij een gelijk percentage komt de vacature met de minste ontbrekende skills eerst.

## Responses

Elke response is JSON met de velden `api_version`, `api_name`, `count`, `status` en `status_message`.

- **Gelukt:** het resultaat staat in `data`.
- **Fout:** er staat een `error` met een `message`.
- **Verkeerde invoer (422):** in `error.details` staat per veld wat er mis is.

### Statuscodes

| Code | Betekenis                                          |
|------|----------------------------------------------------|
| 400  | Ongeldige JSON of ongeldig id                      |
| 401  | Niet ingelogd                                      |
| 403  | Verkeerde rol of niet de eigenaar                  |
| 404  | Bestaat niet                                       |
| 405  | Methode niet toegestaan                            |
| 409  | Skill heb je al, of je hebt al gesolliciteerd      |
| 422  | Verkeerde invoer (zie `error.details`)             |

## Postman

In de map `postman` staat een collectie met alle requests. Voer eerst **Login student** en **Login bedrijf** uit, dan worden de tokens automatisch opgeslagen.
