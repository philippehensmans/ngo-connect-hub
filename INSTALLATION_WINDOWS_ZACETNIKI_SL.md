# 🖥️ Namestitev ONG Manager na vaš računalnik z Windows

## Vodnik za začetnike - Tehnično znanje ni potrebno!

Ta vodnik vas vodi korak za korakom pri namestitvi aplikacije ONG Manager na vaš računalnik z Windows.

**Predviden čas: 10-15 minut**

---

## 📋 Kaj potrebujete

- Računalnik z Windows 10 ali 11
- Internetna povezava (za prenos)
- Približno 500 MB prostora na disku

---

## Korak 1: Prenos XAMPP

XAMPP je brezplačna programska oprema, ki omogoča zagon spletnih aplikacij na vašem računalniku.

### 1.1 Odprite spletno stran XAMPP

👉 Odprite brskalnik in pojdite na: **https://www.apachefriends.org/download.html**

### 1.2 Prenesite različico za Windows

- Kliknite gumb **"Download"** poleg "XAMPP for Windows"
- Izberite različico **8.2.x** (ali najnovejšo)
- Prenos se začne samodejno
- Počakajte, da se prenos konča (približno 150 MB)

---

## Korak 2: Namestitev XAMPP

### 2.1 Zaženite namestitev

- Pojdite v mapo **Prenosi** (Downloads)
- Dvokliknite datoteko **xampp-windows-x64-8.x.x-installer.exe**

### 2.2 Sledite čarovniku za namestitev

1. **Če Windows zahteva dovoljenje**: Kliknite **"Da"** (Yes)

2. **Zaslon "Setup"**: Kliknite **"Next"**

3. **Zaslon "Select Components"**:
   - Pustite vse privzeto označeno
   - Kliknite **"Next"**

4. **Zaslon "Installation folder"**:
   - Pustite privzeto pot: `C:\xampp`
   - Kliknite **"Next"**

5. **Zaslon "Bitnami for XAMPP"**:
   - Odznačite polje "Learn more about Bitnami..."
   - Kliknite **"Next"**

6. **Zaslon "Ready to Install"**: Kliknite **"Next"**

7. **Počakajte**, da se namestitev konča (1-2 minuti)

8. **Zaslon "Completing"**:
   - Pustite označeno "Do you want to start the Control Panel now?"
   - Kliknite **"Finish"**

---

## Korak 3: Zagon spletnega strežnika

### 3.1 Odpre se nadzorna plošča XAMPP

Vidite okno z več vrsticami: Apache, MySQL, FileZilla itd.

### 3.2 Zaženite Apache

- V vrstici **"Apache"** kliknite gumb **"Start"**
- Besedilo "Apache" postane **zeleno** = ✅ Deluje!

> ⚠️ **Če ne deluje:**
> - Drug program morda uporablja vrata 80 (Skype, IIS...)
> - Zaprite te programe in poskusite znova

---

## Korak 4: Prenos ONG Manager

### 4.1 Prenesite aplikacijo

👉 Kliknite to povezavo: **https://github.com/philippehensmans/ngo-connect-hub/raw/main/ong-manager-v10.zip**

Datoteka ZIP se prenese samodejno.

### 4.2 Razširite datoteko ZIP

1. Pojdite v mapo **Prenosi** (Downloads)
2. Z **desnim klikom** kliknite na datoteko `ong-manager-v10.zip`
3. Kliknite **"Razširi vse..."** (Extract all...)
4. Kliknite **"Razširi"** (Extract)

Pojavi se nova mapa `ong-manager-v10`.

---

## Korak 5: Kopiranje aplikacije v XAMPP

### 5.1 Odprite razširjeno mapo

- Dvokliknite mapo `ong-manager-v10`
- Vidite mapo `ngo-connect-hub`

### 5.2 Kopirajte mapo

1. Z **desnim klikom** kliknite na mapo `ngo-connect-hub`
2. Kliknite **"Kopiraj"** (Copy)

### 5.3 Prilepite v XAMPP

1. Odprite **Raziskovalec datotek** (File Explorer) - rumena ikona mape v opravilni vrstici
2. V naslovno vrstico zgoraj vpišite: `C:\xampp\htdocs`
3. Pritisnite **Enter**
4. Z **desnim klikom** kliknite v okno
5. Kliknite **"Prilepi"** (Paste)

Mapa `ngo-connect-hub` je zdaj v `C:\xampp\htdocs\`

---

## Korak 6: Odpiranje aplikacije 🎉

### 6.1 Odprite brskalnik

Odprite **Chrome**, **Firefox** ali **Edge**.

### 6.2 Dostop do aplikacije

V naslovno vrstico vpišite:

```
http://localhost/ngo-connect-hub/
```

Pritisnite **Enter**.

### 6.3 Prva prijava

1. **Ime ekipe**: Vnesite ime (npr. "Moje združenje")
2. **Geslo**: Izberite geslo
3. Kliknite **"Prijava"**

---

## ✅ Končano!

Čestitke! ONG Manager deluje na vašem računalniku.

**Za naslednjič:**

1. Zaženite **XAMPP Control Panel** (oranžna ikona v meniju Start)
2. Kliknite **"Start"** poleg Apache
3. Odprite brskalnik na naslovu: `http://localhost/ngo-connect-hub/`

---

## 🆘 Pogoste težave

### "Stran se ne prikaže"

- Preverite, da je Apache zagnan (zelen v XAMPP)
- Preverite naslov: `http://localhost/ngo-connect-hub/`

### "Apache se ne zažene"

- Drug program uporablja vrata 80
- Rešitev: V XAMPP kliknite **"Config"** nato **"Apache (httpd.conf)"**
- Poiščite `Listen 80` in zamenjajte z `Listen 8080`
- Shranite in znova zaženite Apache
- Nato uporabite: `http://localhost:8080/ngo-connect-hub/`

### "Pozabil/a sem geslo"

- Izbrišite datoteko `C:\xampp\htdocs\ngo-connect-hub\data\ong_manager.db`
- Osvežite stran za ustvarjanje nove ekipe

---

## 💡 Nasveti

- **Varnostno kopirajte podatke**: Redno kopirajte mapo `data` drugam
- **Posodobitve**: Znova prenesite ZIP in zamenjajte datoteke (razen mape `data`)

---

## 📞 Potrebujete pomoč?

- Preglejte vgrajen priročnik: kliknite **?** v aplikaciji
- GitHub: https://github.com/philippehensmans/ngo-connect-hub

---

*Vodnik ustvarjen za ONG Manager v10.0*
*Zadnja posodobitev: Januar 2025*
