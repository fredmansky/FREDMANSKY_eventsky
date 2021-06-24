# FREDMANSKY_eventplugin

## Spam Protection
- **Honeypot**: Feld *eventsky* muss gesetzt aber leer sein.
- **JS-Spam-Protection**: Feld *eventHash* muss gesetzt sein. Der Wert kann über die Route */eventsky/hash/{eventId}* abgefragt werden. Werden mehrere EventIds gleichzeitig übergeben, so wird der Hashwert des ersten Events erwartet.