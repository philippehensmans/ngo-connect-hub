# Navodila za namestitev - ONG Manager v10.0

**Aplikacija za upravljanje projektov za NVO**
Arhitektura: PHP + SQLite
Različica: 10.0.1

---

## 1. Zahteve

### Minimalna konfiguracija strežnika

| Komponenta | Minimalna različica | Opombe |
|------------|---------------------|--------|
| PHP | 7.4+ | Priporočen PHP 8.x |
| Spletni strežnik | Apache 2.4+ | Z aktiviranim mod_rewrite |
| SQLite | 3.x | Vgrajen v PHP |

### Zahtevane PHP razširitve

- `pdo_sqlite` - Za podatkovno bazo
- `mbstring` - Za podporo Unicode
- `json` - Za API-je

> **Preverjanje razširitev:**
> Ustvarite datoteko `phpinfo.php` z `<?php phpinfo(); ?>` za preverjanje nameščenih razširitev.

---

## 2. Prenos

Prenesite datoteko ZIP z naslova:

👉 **https://github.com/philippehensmans/ngo-connect-hub/raw/main/ong-manager-v10.zip**

---

## 3. Namestitev

### Korak 1: Razširitev datotek

Razširite arhiv v mapo vašega spletnega strežnika:

```bash
# Linux/Mac
unzip ong-manager-v10.zip -d /var/www/html/

# Ali prek FTP/SFTP
# Naložite in razširite prek upravitelja datotek
```

### Korak 2: Preimenovanje mape (neobvezno)

```bash
mv /var/www/html/ngo-connect-hub /var/www/html/ong-manager
```

### Korak 3: Dovoljenja

> ⚠️ **Pomembno:** Mapa `data/` mora biti dostopna za pisanje, da se lahko shrani podatkovna baza SQLite.

```bash
# Ustvarite mapo data
mkdir -p /var/www/html/ong-manager/data

# Dodelite dovoljenja za pisanje
chmod 755 /var/www/html/ong-manager
chmod 777 /var/www/html/ong-manager/data
```

### Korak 4: Konfiguracija Apache

Prepričajte se, da je `mod_rewrite` aktiviran:

```bash
# Aktivirajte mod_rewrite
sudo a2enmod rewrite

# Znova zaženite Apache
sudo systemctl restart apache2
```

Preverite, da vaša konfiguracija Apache dovoljuje datoteke `.htaccess`:

```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

---

## 4. Prvi zagon

### Dostop do aplikacije

Odprite brskalnik in pojdite na:

```
http://vaš-strežnik/ong-manager/
```

### Ustvarjanje ekipe

1. Vnesite **ime ekipe** (npr. "Moja NVO")
2. Izberite **geslo**
3. Kliknite **Prijava**

> ✅ **Čestitke!** Aplikacija je nameščena in pripravljena za uporabo.

---

## 5. Struktura datotek

| Mapa/Datoteka | Opis |
|---------------|------|
| `index.php` | Glavna vstopna točka |
| `config/` | Konfiguracija aplikacije |
| `src/` | Izvorna koda (Controllers, Models, Services) |
| `views/` | PHP predloge (uporabniški vmesnik) |
| `public/` | Statične datoteke (JS, slike) |
| `data/` | Podatkovna baza SQLite (samodejno ustvarjena) |

---

## 6. Napredna konfiguracija

### Sprememba poti do podatkovne baze

Uredite `config/config.php`:

```php
return [
    'database' => [
        'path' => __DIR__ . '/../data/ong_manager.db'
    ],
    // ...
];
```

### Aktiviranje načina za odpravljanje napak

V `config/config.php` nastavite:

```php
'app' => [
    'debug' => true,
    // ...
]
```

---

## 7. Varnostno kopiranje

### Ročno varnostno kopiranje

Preprosto kopirajte datoteko podatkovne baze:

```bash
cp /var/www/html/ong-manager/data/ong_manager.db /pot/backup/
```

### Samodejno varnostno kopiranje

Aplikacija samodejno ustvarja dnevne varnostne kopije v `data/backups/`.

Gumb **"Varnostna kopija"** je na voljo tudi v vmesniku.

---

## 8. Odpravljanje težav

| Težava | Rešitev |
|--------|---------|
| Prazna stran | Preverite dnevnike PHP: `tail -f /var/log/apache2/error.log` |
| Napaka 500 | Preverite dovoljenja in mod_rewrite |
| Podatkovna baza ni ustvarjena | Preverite, da je `data/` nastavljen na chmod 777 |
| Napaka SQLite | Preverite, da je razširitev `pdo_sqlite` nameščena |

---

## 9. Podpora

Za vsa vprašanja ali težave:

- 📖 Dokumentacija: `MANUAL_SL.md` vključen v aplikaciji
- ❓ Spletna pomoč: Kliknite ikono **?** v aplikaciji
- 🐙 GitHub: https://github.com/philippehensmans/ngo-connect-hub

---

*ONG Manager v10.0 - Navodila za namestitev*
*© 2024 Philippe Hensmans*
