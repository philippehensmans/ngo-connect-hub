# NGO Manager - Uporabniški priročnik (Slovenska verzija)

## Kazalo

1. [Uvod](#uvod)
2. [Začetek](#začetek)
3. [Projekti](#projekti)
4. [Naloge](#naloge)
5. [Mejniki](#mejniki)
6. [Skupine](#skupine)
7. [Pogledi](#pogledi)
8. [AI Asistent](#ai-asistent)
9. [Nastavitve](#nastavitve)
10. [Izvoz in uvoz](#izvoz-in-uvoz)

---

## Uvod

NGO Manager je celovita aplikacija za upravljanje projektov, zasnovana posebej za nevladne organizacije. Omogoča sledenje nalogam, mejnikom, skupinam in projektom z intuitivnim vmesnikom.

### Glavne funkcionalnosti

- ✅ Upravljanje več projektov
- 📋 Sledenje nalogam z odvisnostmi
- 📍 Mejniki za organizacijo projektnih faz
- 👥 Skupine za organizacijo ekipe
- 📊 Več pogledov: Nadzorna plošča, Seznam, Kanban, Gantt, Koledar
- 🤖 AI Asistent za načrtovanje projektov
- 📤 Izvoz v Excel in iCalendar
- 🌍 Večjezičnost (Francoščina, Angleščina, Španščina, Slovenščina)

---

## Začetek

### Prijava

1. Odprite aplikacijo v spletnem brskalniku
2. Vnesite svoje poverilnice
3. Kliknite "Connexion" (Prijava)

### Menjava jezika

V zgornjem desnem kotu lahko izberete jezik:
- **FR** - Francoščina
- **EN** - Angleščina
- **ES** - Španščina
- **SL** - Slovenščina

---

## Projekti

### Ustvarjanje novega projekta

1. Kliknite gumb **"Nouveau"** (Novo) v vrhnjo vrstico
2. Izberite **"Nouveau Projet"** (Nov projekt)
3. Izpolnite obrazec:
   - **Ime projekta**: Ime vašega projekta
   - **Opis**: Kratek opis projekta
4. Kliknite **"Enregistrer"** (Shrani)

### Izbira projekta

- V levi stranski vrstici kliknite na ime projekta
- Aktivni projekt je označen z modro barvo

### Urejanje projekta

1. Kliknite na **ikono svinčnika** (✏️) poleg imena projekta
2. Spremenite želene informacije
3. Kliknite **"Enregistrer"** (Shrani)

### Brisanje projekta

1. Kliknite na **ikono koša** (🗑️) poleg imena projekta
2. Potrdite brisanje

---

## Naloge

### Ustvarjanje naloge

**Metoda 1: Prek menija "Nouveau"**
1. Kliknite **"Nouveau"** → **"Nouvelle Tâche"** (Nova naloga)

**Metoda 2: V pogledu Kanban**
1. Kliknite **"+ Ajouter"** (+ Dodaj) v stolpcu

### Podatki o nalogi

- **Naslov**: Ime naloge
- **Opis**: Podroben opis naloge
- **Odgovorna oseba**: Član ekipe, zadolžen za nalogo
- **Status**:
  - 🔵 **À faire** (Za narediti)
  - 🟡 **En cours** (V teku)
  - 🟢 **Terminé** (Končano)
- **Prioriteta**: Nizka, Srednja, Visoka
- **Datum začetka**: Datum začetka naloge
- **Datum konca**: Rok naloge
- **Mejnik**: Mejnik, povezan z nalogo
- **Skupina**: Delovna skupina
- **Odvisnosti**: Naloge, ki jih je treba dokončati pred to nalogo
- **Povezava**: URL za dodatne dokumente

### Odvisnosti med nalogami

Odvisnosti omogočajo definiranje, da ena naloga ne more biti začeta pred koncem druge.

**Dodajanje odvisnosti:**
1. V obrazcu naloge izberite odvisne naloge
2. Sistem bo prikazal opozorilo ⚠️, če so datumi v konfliktu

### Urejanje naloge

- Kliknite **ikono svinčnika** (✏️) poleg naloge
- Ali kliknite na nalogo v pogledu Gantt

---

## Mejniki

Mejniki predstavljajo ključne faze vašega projekta (npr. "Začetek projekta", "Vmesno poročilo", "Zaključek").

### Ustvarjanje mejnika

1. Kliknite **"Nouveau"** → **"Nouveau Jalon"** (Nov mejnik)
2. Izpolnite:
   - **Ime**: Ime mejnika
   - **Datum**: Ciljni datum
   - **Status**: Aktiven / Arhiviran
   - **Odvisno od mejnika**: Mejnik, od katerega je ta odvisen
3. Kliknite **"Enregistrer"** (Shrani)

### Odvisnosti med mejniki

Mejniki lahko so odvisni drug od drugega. To omogoča hierarhično strukturiranje projektnih faz.

**Primer:**
- Mejnik "Nabava opreme" je odvisen od mejnika "Odobritev proračuna"

### Pogled Mejniki

V zavihku **"Jalons"** (Mejniki) lahko:
- Vidite vse mejnike projekta
- **Razvrščate** po imenu ali datumu (gumbi 📝 Ime, 📅 Datum)
- Urejate ali brišete mejnike

---

## Skupine

Skupine omogočajo organizacijo vaše ekipe po tematikah ali delovnih ekipah.

### Ustvarjanje skupine

1. Kliknite **"Nouveau"** → **"Nouveau Groupe"** (Nova skupina)
2. Izpolnite:
   - **Ime skupine**: Npr. "Logistika", "Komunikacija"
   - **Opis**: Kakšne so odgovornosti te skupine
   - **Člani**: Izberite člane ekipe
3. Kliknite **"Enregistrer"** (Shrani)

### Pogled Skupine

V zavihku **"Groupes"** (Skupine):
- Vidite vse skupine s člani
- Lahko urejate ali brišete skupine
- Vidite število nalog po skupini

---

## Pogledi

Aplikacija ponuja več pogledov za vizualizacijo vaših projektov:

### 1. Tableau de Bord (Nadzorna plošča)

**Pregled:**
- 📊 Statistika: Skupno število nalog, končane naloge, napredek
- 📈 Grafikon: Naloge po statusu
- 📊 Grafikon: Naloge po projektu
- 📅 Prihajajoče naloge ta teden
- 👥 Naloge po odgovornih osebah

### 2. Vue Globale (Globalni pogled)

Prikazuje vse naloge vseh projektov v enem mestu. Uporabno za pregled celotne organizacije.

### 3. Liste (Seznam)

**Dve možnosti:**

**S mejniki (privzeto):**
- Naloge so združene po mejnikih
- Prikazuje napredek vsakega mejnika
- Hierarhična struktura z odvisnostmi

**Brez mejnikov:**
1. Kliknite gumb **"Seznam brez mejnikov"**
2. Možnosti razvrščanja:
   - 📅 **Datum**: Po datumu konca
   - 📝 **Ime**: Po abecednem vrstnem redu
   - 👤 **Odgovorna oseba**: Po odgovorni osebi
3. Kliknite ponovno za spreminjanje med naraščajočim/padajočim vrstnim redom (↑/↓)

### 4. Kanban

Klasičen Kanban pogled s tremi stolpci:
- **Za narediti** (À faire)
- **V teku** (En cours)
- **Končano** (Terminé)

**Uporaba:**
- Povlecite in spustite naloge med stolpci
- Kliknite **"+ Dodaj"** za dodajanje naloge v stolpec

### 5. Gantt

Časovnica projekta s stolpci:
- Vodoravne črte za naloge
- Diamanti (◆) za mejnike
- Puščice za odvisnosti

**Funkcionalnosti:**
- Povlecite naloge za spreminjanje datumov
- Kliknite nalogo za urejanje
- Gumbi za prikaz: Dan, Teden, Mesec
- Gumb **"📅 Aujourd'hui"** (Danes) za vrnitev na trenutni datum

**Navigacija:**
- Vodoravni premik: Kolešček miške, povleci-spusti ali drsni trak
- Prilagodi velikost: Zoomirajte s tipkovnico ali gumbi

### 6. Calendrier (Koledar)

Pogled koledarja z nalogami in mejniki.

**Ikone:**
- 📅 Naloge
- 📍 Mejniki (zelena barva)

**Funkcionalnosti:**
- Kliknite datum za dodajanje naloge
- Kliknite dogodek za podrobnosti
- Povlecite dogodek za spreminjanje datuma

### 7. Jalons (Mejniki)

Seznam vseh mejnikov s podrobnostmi:
- Ime in datum mejnika
- Odvisnosti med mejniki (🔗 ikona)
- Naloge, povezane z mejnikom

**Razvrščanje:**
- 📝 **Ime**: Abecedno
- 📅 **Datum**: Kronološko
- Kliknite ponovno za obrnitev vrstnega reda

### 8. Assistant (AI Asistent)

Pomočnik za načrtovanje projekta. Poglejte naslednjo razdelek.

---

## AI Asistent

AI Asistent vam pomaga strukturirati vaš projekt s postavljanjem vprašanj.

### Uporaba asistenta

1. Pojdite na zavihek **"Assistant"**
2. Kliknite **"Nouvelle conversation"** (Nova konverzacija)
3. Sledite navodilom:

**Koraki:**
1. **Tip projekta**: Izberite kategorijo
   - Humanitarna akcija
   - Okolje in podnebje
   - Izobraževanje
   - Zdravje
   - Lokalni razvoj
   - Zagovorništvo
   - Drugo (prilagojen projekt)

2. **Ime projekta**: Vnesite ime

3. **Opis**: Opišite cilje in kontekst

4. **Trajanje**: Npr. "6 mesecev", "1 leto"

5. **Mejniki**: Asistent predlaga mejnike glede na tip projekta
   - Odgovorite "OK" za sprejem
   - Ali predlagajte svoje mejnike (ločene z vejicami)

6. **Skupine**: Predlogi delovnih skupin
   - Odgovorite "OK" ali predlagajte svoje

7. **Rezultati**: Pričakovani rezultati projekta
   - Odgovorite "OK" ali predlagajte svoje

8. **Povzetek**: Asistent prikaže povzetek
   - Odgovorite "Da, generiraj strukturo" za potrditev
   - Ali "Spremeni" za spremembe

9. **Generiranje**: Kliknite **"Générer la structure"** (Generiraj strukturo)

Sistem bo samodejno ustvaril:
- ✅ Skupine
- ✅ Mejnike
- ✅ Osnovno strukturo nalog

### Načini asistenta

- **💡 Brezplačni način (Pravila)**: Uporablja vnaprej določena pravila
- **🤖 API način**: Uporablja AI API (če je konfiguriran)

---

## Nastavitve

Kliknite ikono **zobnika** (⚙️) v vrhnjo vrstico za dostop do nastavitev.

### Splošne nastavitve

- **Ime organizacije**: Ime vaše organizacije
- **Geslo**: Spremenite svoje geslo

### Upravljanje ekipe

**Dodajanje člana:**
1. Vnesite ime, priimek in e-pošto
2. Kliknite **"Ajouter membre"** (Dodaj člana)

**Brisanje člana:**
- Kliknite **ikono koša** (🗑️) poleg člana

### Predloge projektov

Shranite strukture projektov kot predloge za ponovno uporabo.

**Ustvarjanje predloge:**
1. Izberite projekt
2. Kliknite **"Sauvegarder comme modèle"** (Shrani kot predlogo)
3. Vnesite ime predloge

**Uporaba predloge:**
1. Kliknite **"Utiliser ce modèle"** (Uporabi to predlogo)
2. Vnesite ime novega projekta

### AI Konfiguracija

Če želite uporabiti zunanji AI API:
1. Omogočite **"Utiliser une API IA externe"** (Uporabi zunanji AI API)
2. Izberite ponudnika (Claude, OpenAI, Azure)
3. Vnesite API ključ
4. Izberite model (opcijsko)

---

## Izvoz in uvoz

### Izvoz v Excel

1. Kliknite ikono **Excel** (📊) v vrhnjo vrstico
2. Datoteka se bo prenesla z vsemi nalogami projekta

**Vsebina:**
- Seznam vseh nalog
- Odgovorne osebe, statusi, datumi
- Odvisnosti in povezave

### Izvoz v iCalendar (.ics)

Kliknite ikono **koledarja** (📅) v vrhnjo vrstico:

**Možnosti:**
- **Izvozi ta projekt**: Samo naloge trenutnega projekta
- **Izvozi vse projekte**: Vse naloge vseh projektov

**Uporaba:**
- Uvozite datoteko .ics v Google Calendar, Outlook itd.
- Sinhronizirajte z vašimi osebnimi koledarji

### Uvoz projekta

1. Kliknite ikono **uvoz** (📥) v vrhnjo vrstico
2. Izberite JSON datoteko (izvoženo iz NGO Manager)
3. Kliknite **"Importer"** (Uvozi)

Projekt bo uvožen z:
- ✅ Vsemi nalogami
- ✅ Mejniki
- ✅ Skupinami
- ✅ Odvisnostmi

### Varnostna kopija baze podatkov

Kliknite **"Télécharger base de données"** (Prenesi bazo podatkov) za prenos celotne baze.

**Uporaba:**
- Varnostne kopije
- Migracija na drug strežnik
- Arhiviranje

---

## Namigi in triki

### ⚡ Bližnjice

- **Dvojni klik** na nalogo v Gantt: Hitro urejanje
- **Povleci-spusti** v Kanban: Hitro spreminjanje statusa
- **Klik na mejnik** v pogledu Seznam: Prikaz/skrij naloge

### 🎨 Vizualne oznake

- ⚠️ **Opozorilo**: Konflikt datumov z odvisnostmi
- 🔗 **Povezava**: Naloga ima odvisnosti
- 📍 **Mejnik**: Mejnik v koledarju
- ◆ **Diamant**: Mejnik v Gantt

### 📊 Sledenje napredku

- Nadzorna plošča prikazuje **% napredka** projekta
- Mejniki prikazujejo **% dokončanih nalog**
- Barve pomagajo hitro prepoznati status

### 🔍 Najboljše prakse

1. **Definirajte mejnike** pred dodajanjem nalog
2. **Uporabite skupine** za organizacijo ekipe
3. **Nastavite odvisnosti** za realistično načrtovanje
4. **Redno posodabljajte statuse** nalog
5. **Uporabite AI asistenta** za nova načrtovanja projektov
6. **Izvozite redno** za varnostne kopije

---

## Podpora

Za težave ali vprašanja:
- Preverite to priročnik
- Kontaktirajte skrbnika sistema
- Glejte GitHub repozitorij za posodobitve

---

## Različice in posodobitve

**Trenutna verzija:** 10.0

### Nedavne funkcionalnosti

- ✅ Traduction complète slovène
- ✅ Odvisnosti mejnikov
- ✅ Seznam brez mejnikov z razvrščanjem
- ✅ AI Asistent za načrtovanje
- ✅ Večjezična podpora (4 jeziki)
- ✅ Razširjeni izvoz (Excel, iCalendar)
- ✅ Predloge projektov

---

**Prijetno uporabo NGO Manager!** 🎉
