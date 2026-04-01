# Simple Health Check Booking

## Beskrivning

Detta är ett WordPress-plugin för att boka tid för vaccination med en enkel hälsokontroll.

Användaren går igenom ett formulär i tre steg:

1. Väljer vaccination, mottagning, datum och tid
2. Svarar på några hälsorelaterade frågor
3. Fyller i sina kontaktuppgifter

Baserat på svaren:

- Godkänd -> bokningen registreras
- Ej godkänd -> användaren uppmanas att kontakta mottagningen

Efter en lyckad bokning visas en bekräftelse direkt på sidan.

---

## Installation

1. Ladda upp plugin-mappen till `/wp-content/plugins/`
2. Aktivera pluginet i WordPress admin
3. Lägg in shortcoden nedan på en sida:

```
[health_check_booking]
```

---

## Funktioner

- Bokningsformulär i tre steg
- Validering av alla obligatoriska fält
- Kontroll av datum (kan inte vara i det förflutna)
- Dynamisk tid baserat på vald mottagning
- Hälsokontroll som avgör om bokning kan genomföras
- Bekräftelsevy efter bokning
- E-post skickas vid registrerad bokning
- Admin-sida för att hantera bokningar

---

## Admin (hantering av bokningar)

I WordPress admin finns sidan:

**Verktyg -> Hälsokontroll Bokningar**

Där kan man:

- Se alla bokningar
- Filtrera på mottagning
- Söka på namn eller e-post
- Se status (Pending / Genomförd)
- Ändra status på en bokning

---

## Hur data lagras

- Bokningar sparas som en Custom Post Type: `health_booking`
- All information lagras som post meta (tex namn, email, datum, status)

---

## E-post

Pluginet använder WordPress inbyggda funktion `wp_mail()` för att skicka e-post.

För att e-post ska fungera korrekt krävs att WordPress-installationen har en fungerande mail-konfiguration (t.ex. via SMTP-plugin eller serverinställningar).

---

## Begränsningar / Förbättringar

- Ingen möjlighet att redigera eller ta bort bokningar via admin, endast visning och statusändring
- Efter genomförd bokning visas en bekräftelse istället för formuläret via URL, för att göra en ny bokning behöver sidan laddas om
- Ingen avancerad hantering av tillgängliga tider (statisk logik)
- Enkel adminvy utan pagination
- Ingen avancerad design eller animationer
- E-post är beroende av WordPress konfiguration
- Ingen hantering av dubbelbokningar (samma tid kan bokas flera gånger)
- Begränsad tillgänglighet (accessibility kan förbättras)

---

## Sammanfattning

Pluginet är byggt för att vara enkelt att använda och tydligt i sitt flöde, både för användare och administratör.

Fokus har legat på:

- tydlig struktur
- fungerande validering
- enkel administration
- bra användarupplevelse
