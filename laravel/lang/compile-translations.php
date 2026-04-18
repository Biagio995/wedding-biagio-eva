<?php

/**
 * Generates lang/it.json, lang/el.json, and lang/de.json from English source keys used in __('…').
 * Run: php lang/compile-translations.php
 */
$rows = [
    ['Actions', 'Azioni', 'Ενέργειες', 'Aktionen'],
    ['Additional details', 'Dettagli aggiuntivi', 'Πρόσθετες λεπτομέρειες', 'Weitere Details'],
    ['Admin access is not configured.', 'Accesso amministratore non configurato.', 'Η πρόσβαση διαχειριστή δεν έχει ρυθμιστεί.', 'Admin-Zugang ist nicht konfiguriert.'],
    ['Admin login', 'Accesso amministratore', 'Σύνδεση διαχειριστή', 'Admin-Anmeldung'],
    ['All', 'Tutti', 'Όλα', 'Alle'],
    ['All dates', 'Tutte le date', 'Όλες οι ημερομηνίες', 'Alle Daten'],
    ['Anonymous', 'Anonimo', 'Ανώνυμο', 'Anonym'],
    ['Apply', 'Applica', 'Εφαρμογή', 'Anwenden'],
    ['Approve', 'Approva', 'Έγκριση', 'Freigeben'],
    ['Approve or delete uploads. Deleted files are removed from the server.', 'Approva o elimina gli invii. I file eliminati vengono rimossi dal server.', 'Έγκριση ή διαγραφή μεταφορτώσεων. Τα διαγραμμένα αρχεία αφαιρούνται από τον διακομιστή.', 'Uploads genehmigen oder löschen. Gelöschte Dateien werden vom Server entfernt.'],
    ['Approved', 'Approvate', 'Εγκεκριμένα', 'Freigegeben'],
    ['Attendance', 'Partecipazione', 'Συμμετοχή', 'Teilnahme'],
    ['Attending', 'Partecipano', 'Θα παρευρεθούν', 'Zugesagt'],
    ['Attending (invitations)', 'Partecipano (inviti)', 'Συμμετοχή (προσκλήσεις)', 'Zugesagt (Einladungen)'],
    ['Awaiting reply', 'In attesa di risposta', 'Αναμονή απάντησης', 'Antwort ausstehend'],
    ['Camera or gallery — multi-select', 'Fotocamera o galleria — selezione multipla', 'Κάμερα ή συλλογή — πολλαπλή επιλογή', 'Kamera oder Galerie — Mehrfachauswahl'],
    ['Choose a file.', 'Scegli un file.', 'Επιλέξτε αρχείο.', 'Wählen Sie eine Datei.'],
    ['Church', 'Chiesa', 'Εκκλησία', 'Kirche'],
    ['Countdown', 'Conto alla rovescia', 'Αντίστροφη μέτρηση', 'Countdown'],
    ['Create an invitation record. A personal link is generated automatically.', 'Crea un invito. Viene generato automaticamente un link personale.', 'Δημιουργήστε πρόσκληση. Ένας προσωπικός σύνδεσμος δημιουργείται αυτόματα.', 'Legen Sie eine Einladung an. Ein persönlicher Link wird automatisch erstellt.'],
    ['Create guest', 'Crea ospite', 'Δημιουργία καλεσμένου', 'Gast anlegen'],
    ['CSV file', 'File CSV', 'Αρχείο CSV', 'CSV-Datei'],
    ['Days', 'Giorni', 'Ημέρες', 'Tage'],
    ['Declined', 'Non partecipano', 'Αρνήθηκαν', 'Abgesagt'],
    ['Delete', 'Elimina', 'Διαγραφή', 'Löschen'],
    ['Delete this guest? This cannot be undone.', 'Eliminare questo ospite? L’operazione non può essere annullata.', 'Διαγραφή αυτού του καλεσμένου; Δεν μπορεί να αναιρεθεί.', 'Diesen Gast löschen? Dies kann nicht rückgängig gemacht werden.'],
    ['Download all photos (ZIP)', 'Scarica tutte le foto (ZIP)', 'Λήψη όλων των φωτογραφιών (ZIP)', 'Alle Fotos herunterladen (ZIP)'],
    ['Download photo', 'Scarica foto', 'Λήψη φωτογραφίας', 'Foto herunterladen'],
    ['Download QR (PNG)', 'Scarica QR (PNG)', 'Λήψη QR (PNG)', 'QR herunterladen (PNG)'],
    ['Edit guest', 'Modifica ospite', 'Επεξεργασία καλεσμένου', 'Gast bearbeiten'],
    ['Each file must be at most :max kilobytes.', 'Ogni file deve essere al massimo :max kilobyte.', 'Κάθε αρχείο πρέπει να είναι το πολύ :max kilobyte.', 'Jede Datei darf höchstens :max Kilobyte groß sein.'],
    ['Email', 'Email', 'Email', 'E-Mail'],
    ['Email (optional)', 'Email (facoltativa)', 'Email (προαιρετικό)', 'E-Mail (optional)'],
    ['Enter how many people will attend (including you).', 'Indica quante persone parteciperanno (te incluso).', 'Εισαγάγετε πόσα άτομα θα παρευρεθούν (συμπεριλαμβανομένου εσάς).', 'Geben Sie an, wie viele Personen teilnehmen (Sie eingeschlossen).'],
    ['Filter', 'Filtro', 'Φίλτρο', 'Filter'],
    ['Filter by RSVP', 'Filtra per RSVP', 'Φιλτράρισμα κατά RSVP', 'Nach RSVP filtern'],
    ['First row must be the header. Columns: name (required), email and token (optional). Use comma or semicolon as separator.', 'La prima riga deve essere l’intestazione. Colonne: name (obbligatorio), email e token (opzionali). Usa virgola o punto e virgola come separatore.', 'Η πρώτη γραμμή πρέπει να είναι η κεφαλίδα. Στήλες: name (υποχρεωτικό), email και token (προαιρετικά). Χρησιμοποιήστε κόμμα ή ερωτηματικό ως διαχωριστικό.', 'Die erste Zeile muss die Kopfzeile sein. Spalten: name (Pflicht), email und token (optional). Trennzeichen: Komma oder Semikolon.'],
    ['Gallery', 'Galleria', 'Συλλογή', 'Galerie'],
    ['Guest', 'Ospite', 'Καλεσμένος', 'Gast'],
    ['Guest created.', 'Ospite creato.', 'Ο καλεσμένος δημιουργήθηκε.', 'Gast angelegt.'],
    ['Guest list', 'Lista ospiti', 'Λίστα καλεσμένων', 'Gästeliste'],
    ['Guest deleted.', 'Ospite eliminato.', 'Ο καλεσμένος διαγράφηκε.', 'Gast gelöscht.'],
    ['Guest updated.', 'Ospite aggiornato.', 'Ο καλεσμένος ενημερώθηκε.', 'Gast aktualisiert.'],
    ['Guests', 'Ospiti', 'Καλεσμένοι', 'Gäste'],
    ['Home', 'Inizio', 'Αρχική', 'Start'],
    ['Hello :name,', 'Ciao :name,', 'Γεια σου :name,', 'Hallo :name,'],
    ['Hello, :name', 'Ciao, :name', 'Γεια σου, :name', 'Hallo, :name'],
    ['Hours', 'Ore', 'Ώρες', 'Stunden'],
    ['If you already responded, you can ignore this message.', 'Se hai già risposto, puoi ignorare questo messaggio.', 'Αν έχετε ήδη απαντήσει, μπορείτε να αγνοήσετε αυτό το μήνυμα.', 'Wenn Sie bereits geantwortet haben, können Sie diese Nachricht ignorieren.'],
    ['Import', 'Importa', 'Εισαγωγή', 'Importieren'],
    ['Import from CSV', 'Importa da CSV', 'Εισαγωγή από CSV', 'Aus CSV importieren'],
    ['Import guests (CSV)', 'Importa ospiti (CSV)', 'Εισαγωγή καλεσμένων (CSV)', 'Gäste importieren (CSV)'],
    ['Invalid password.', 'Password non valida.', 'Μη έγκυρος κωδικός.', 'Ungültiges Passwort.'],
    ['Invalid upload.', 'Caricamento non valido.', 'Μη έγκυρη μεταφόρτωση.', 'Ungültiger Upload.'],
    ['Invitation QR code', 'QR code invito', 'QR κωδικός πρόσκλησης', 'Einladungs-QR-Code'],
    ['Invitation token', 'Token invito', 'Διακριτικό πρόσκλησης', 'Einladungstoken'],
    ['Invitation link', 'Link d’invito', 'Σύνδεσμος πρόσκλησης', 'Einladungslink'],
    ['Invitations', 'Inviti', 'Προσκλήσεις', 'Einladungen'],
    ['It will be a great joy for us and for our parents to have you with us at the beginning of our new life.', 'Sarà una grande gioia per noi e per i nostri genitori avervi con noi all\'inizio della nostra nuova vita.', 'Θα είναι μεγάλη χαρά για εμάς και τους γονείς μας να σας έχουμε κοντά μας στο ξεκίνημα της νέας μας ζωής.', 'Für uns und unsere Eltern wird es eine große Freude sein, euch am Anfang unseres neuen Lebens bei uns zu haben.'],
    ['Language', 'Lingua', 'Γλώσσα', 'Sprache'],
    ['Main navigation', 'Navigazione principale', 'Κύρια πλοήγηση', 'Hauptnavigation'],
    ['Map of the church', 'Mappa della chiesa', 'Χάρτης της εκκλησίας', 'Karte der Kirche'],
    ['Map of the reception', 'Mappa del ricevimento', 'Χάρτης του χώρου δεξίωσης', 'Karte des Empfangs'],
    ['Line :line: :message', 'Riga :line: :message', 'Γραμμή :line: :message', 'Zeile :line: :message'],
    ['Loading more…', 'Caricamento altre…', 'Φόρτωση περισσότερων…', 'Weitere werden geladen…'],
    ['Minutes', 'Minuti', 'Λεπτά', 'Minuten'],
    ['Moments from the day — scroll to load more.', 'Momenti della giornata — scorri per caricare altre foto.', 'Στιγμές της ημέρας — κάντε κύλιση για περισσότερες φωτογραφίες.', 'Momente des Tages — scrollen, um mehr zu laden.'],
    ['Name', 'Nome', 'Όνομα', 'Name'],
    ['Name cannot exceed :max characters.', 'Il nome non può superare :max caratteri.', 'Το όνομα δεν μπορεί να υπερβαίνει τους :max χαρακτήρες.', 'Der Name darf höchstens :max Zeichen haben.'],
    ['Name is required.', 'Il nome è obbligatorio.', 'Το όνομα είναι υποχρεωτικό.', 'Der Name ist erforderlich.'],
    ['Network error. Check your connection.', 'Errore di rete. Controlla la connessione.', 'Σφάλμα δικτύου. Ελέγξτε τη σύνδεσή σας.', 'Netzwerkfehler. Bitte Verbindung prüfen.'],
    ['New RSVP', 'Nuovo RSVP', 'Νέα απάντηση RSVP', 'Neue Zusage'],
    ['New RSVP — :name (:event)', 'Nuovo RSVP — :name (:event)', 'Νέα απάντηση RSVP — :name (:event)', 'Neue Zusage — :name (:event)'],
    ['No', 'No', 'Όχι', 'Nein'],
    ['No data rows after the header.', 'Nessuna riga dati dopo l’intestazione.', 'Δεν υπάρχουν γραμμές δεδομένων μετά την κεφαλίδα.', 'Keine Datenzeilen nach der Kopfzeile.'],
    ['No guests match this filter.', 'Nessun ospite corrisponde a questo filtro.', 'Κανένας καλεσμένος δεν ταιριάζει σε αυτό το φίλτρο.', 'Keine Gäste entsprechen diesem Filter.'],
    ['No login needed to browse — anyone with the link can view the album.', 'Nessun login per sfogliare — chi ha il link può vedere l’album.', 'Δεν απαιτείται σύνδεση για περιήγηση — όποιος έχει τον σύνδεσμο βλέπει το άλμπουμ.', 'Kein Login zum Ansehen — mit dem Link ist das Album sichtbar.'],
    ['No photo files found on disk.', 'Nessun file foto trovato sul disco.', 'Δεν βρέθηκαν αρχεία φωτογραφιών στο δίσκο.', 'Keine Fotodateien auf dem Server gefunden.'],
    ['No photos for this date.', 'Nessuna foto per questa data.', 'Δεν υπάρχουν φωτογραφίες για αυτή την ημερομηνία.', 'Keine Fotos für dieses Datum.'],
    ['No photos in this view.', 'Nessuna foto in questa vista.', 'Δεν υπάρχουν φωτογραφίες σε αυτή την προβολή.', 'Keine Fotos in dieser Ansicht.'],
    ['No photos to download.', 'Nessuna foto da scaricare.', 'Δεν υπάρχουν φωτογραφίες για λήψη.', 'Keine Fotos zum Herunterladen.'],
    ['No photos yet. Upload from the gallery page.', 'Ancora nessuna foto. Carica dalla pagina galleria.', 'Δεν υπάρχουν ακόμα φωτογραφίες. Μεταφορτώστε από τη σελίδα συλλογής.', 'Noch keine Fotos. Laden Sie auf der Galerieseite hoch.'],
    ['Notes', 'Note', 'Σημειώσεις', 'Notizen'],
    ['Notes cannot exceed :max characters.', 'Le note non possono superare :max caratteri.', 'Οι σημειώσεις δεν μπορούν να υπερβαίνουν τους :max χαρακτήρες.', 'Notizen dürfen höchstens :max Zeichen haben.'],
    ['Number of guests', 'Numero di ospiti', 'Αριθμός καλεσμένων', 'Anzahl der Gäste'],
    ['Number of guests (including you)', 'Numero di ospiti (te incluso)', 'Αριθμός καλεσμένων (συμπεριλαμβανομένου εσάς)', 'Anzahl der Gäste (Sie eingeschlossen)'],
    ['Open in Google Maps', 'Apri in Google Maps', 'Άνοιγμα στο Google Maps', 'In Google Maps öffnen'],
    ['Open invitation', 'Apri l\'invito', 'Άνοιγμα πρόσκλησης', 'Einladung öffnen'],
    ['Our wedding', 'Il nostro matrimonio', 'Ο γάμος μας', 'Unsere Hochzeit'],
    ['Open this page using your invitation link or QR code to be recognized.', 'Apri questa pagina con il link d’invito o il QR code per essere riconosciuto.', 'Ανοίξτε αυτή τη σελίδα με τον σύνδεσμο πρόσκλησης ή το QR για να αναγνωριστείτε.', 'Öffnen Sie diese Seite mit Ihrem Einladungslink oder QR-Code, um erkannt zu werden.'],
    ['optional', 'opzionale', 'προαιρετικό', 'optional'],
    ['optional; leave empty for a random link', 'opzionale; lascia vuoto per un link casuale', 'προαιρετικό· αφήστε κενό για τυχαίο σύνδεσμο', 'optional; leer lassen für zufälligen Link'],
    ['Overview of responses and expected attendance.', 'Panoramica delle risposte e dei partecipanti attesi.', 'Επισκόπηση απαντήσεων και αναμενόμενης προσέλευσης.', 'Überblick über Antworten und erwartete Teilnahme.'],
    ['Password', 'Password', 'Κωδικός πρόσβασης', 'Passwort'],
    ['Pause wedding music', 'Metti in pausa la musica', 'Παύση μουσικής γάμου', 'Hochzeitsmusik pausieren'],
    ['Pending', 'In attesa', 'Εκκρεμεί', 'Ausstehend'],
    ['People attending', 'Persone che partecipano', 'Άτομα που θα παρευρεθούν', 'Teilnehmende Personen'],
    ['Photo', 'Foto', 'Φωτογραφία', 'Foto'],
    ['Photo approved.', 'Foto approvata.', 'Η φωτογραφία εγκρίθηκε.', 'Foto freigegeben.'],
    ['Photo moderation', 'Moderazione foto', 'Εποπτεία φωτογραφιών', 'Foto-Moderation'],
    ['Public album', 'Album pubblico', 'Δημόσιο άλμπουμ', 'Öffentliches Album'],
    ['Photo removed.', 'Foto rimossa.', 'Η φωτογραφία αφαιρέθηκε.', 'Foto entfernt.'],
    ['Photos from', 'Foto del', 'Φωτογραφίες από', 'Fotos vom'],
    ['Photos uploaded. Thank you!', 'Foto caricate. Grazie!', 'Οι φωτογραφίες μεταφορτώθηκαν. Ευχαριστούμε!', 'Fotos hochgeladen. Vielen Dank!'],
    ['Play wedding music', 'Riproduci la musica del matrimonio', 'Αναπαραγωγή μουσικής γάμου', 'Hochzeitsmusik abspielen'],
    ['Please choose at least one photo.', 'Scegli almeno una foto.', 'Επιλέξτε τουλάχιστον μία φωτογραφία.', 'Bitte wählen Sie mindestens ein Foto.'],
    ['Please choose whether you will attend.', 'Indica se parteciperai.', 'Δηλώστε αν θα παρευρεθείτε.', 'Bitte geben Sie an, ob Sie teilnehmen.'],
    ['Please choose Yes or No.', 'Scegli Sì o No.', 'Επιλέξτε Ναι ή Όχι.', 'Bitte wählen Sie Ja oder Nein.'],
    ['Please enter a valid email address.', 'Inserisci un indirizzo email valido.', 'Εισαγάγετε έγκυρη διεύθυνση email.', 'Bitte geben Sie eine gültige E-Mail-Adresse ein.'],
    ['Please enter your name.', 'Inserisci il tuo nome.', 'Εισαγάγετε το όνομά σας.', 'Bitte geben Sie Ihren Namen ein.'],
    ['Please open your personal invitation link and let us know if you can join us:', 'Apri il tuo link d’invito personale e facci sapere se parteciperai:', 'Ανοίξτε τον προσωπικό σας σύνδεσμο πρόσκλησης και ενημερώστε μας αν μπορείτε να παρευρεθείτε:', 'Bitte öffnen Sie Ihren persönlichen Einladungslink und teilen Sie uns mit, ob Sie dabei sind:'],
    ['Replied', 'Hanno risposto', 'Έχουν απαντήσει', 'Geantwortet'],
    ['Reminder: RSVP for :event', 'Promemoria: RSVP per :event', 'Υπενθύμιση: RSVP για :event', 'Erinnerung: RSVP für :event'],
    ['Remove this photo permanently?', 'Rimuovere definitivamente questa foto?', 'Οριστική διαγραφή αυτής της φωτογραφίας;', 'Dieses Foto dauerhaft entfernen?'],
    ['Required if you answer Yes.', 'Obbligatorio se rispondi Sì.', 'Υποχρεωτικό αν απαντήσετε Ναι.', 'Erforderlich bei Ja.'],
    ['RSVP', 'RSVP', 'RSVP', 'RSVP'],
    ['RSVP confirmation', 'Conferma RSVP', 'Επιβεβαίωση RSVP', 'RSVP-Bestätigung'],
    ['RSVP dashboard', 'Dashboard RSVP', 'Πίνακας RSVP', 'RSVP-Übersicht'],
    ['RSVP notification', 'Notifica RSVP', 'Ειδοποίηση RSVP', 'RSVP-Benachrichtigung'],
    ['RSVP received — :event', 'RSVP ricevuto — :event', 'Ελήφθη RSVP — :event', 'RSVP erhalten — :event'],
    ['RSVP reminder', 'Promemoria RSVP', 'Υπενθύμιση RSVP', 'RSVP-Erinnerung'],
    ['RSVP updated', 'RSVP aggiornato', 'Το RSVP ενημερώθηκε', 'RSVP aktualisiert'],
    ['RSVP updated — :name (:event)', 'RSVP aggiornato — :name (:event)', 'RSVP ενημερώθηκε — :name (:event)', 'RSVP aktualisiert — :name (:event)'],
    ['26 JUNE 2027', '26 GIUGNO 2027', '26 ΙΟΥΝΙΟΥ 2027', '26 JUNI 2027'],
    ['26 june 2027', '26 giugno 2027', '26 ιουνίου 2027', '26 juni 2027'],
    ['Biagio & Eva — 26 june 2027', 'Biagio & Eva — 26 giugno 2027', 'Biagio & Eva — 26 ιουνίου 2027', 'Biagio & Eva — 26 juni 2027'],
    ['FRI', 'VEN', 'ΠΑΡ', 'Fr'],
    ['June', 'Giugno', 'Ιούνιος', 'Juni'],
    ['June 2027', 'Giugno 2027', 'Ιούνιος 2027', 'Juni 2027'],
    ['MON', 'LUN', 'ΔΕΥ', 'Mo'],
    ['SAT', 'SAB', 'ΣΑΒ', 'Sa'],
    ['SUN', 'DOM', 'ΚΥΡ', 'So'],
    ['THU', 'GIO', 'ΠΕΜ', 'Do'],
    ['TUE', 'MAR', 'ΤΡΙ', 'Di'],
    ['WED', 'MER', 'ΤΕΤ', 'Mi'],
    ['Save the date', 'Segna la data', 'Σώσε την ημερομηνία', 'Merke dir den Termin'],
    ['Save', 'Salva', 'Αποθήκευση', 'Speichern'],
    ['Seconds', 'Secondi', 'Δευτερόλεπτα', 'Sekunden'],
    ['Send response', 'Invia risposta', 'Αποστολή απάντησης', 'Antwort senden'],
    ['Share your photos from the day — quick upload from your phone.', 'Condividi le foto della giornata — caricamento rapido dal telefono.', 'Μοιραστείτε φωτογραφίες της ημέρας — γρήγορη μεταφόρτωση από το κινητό σας.', 'Teilen Sie Fotos vom Tag — schnell vom Handy hochladen.'],
    ['Shared photos', 'Foto condivise', 'Κοινόχρηστες φωτογραφίες', 'Geteilte Fotos'],
    ['Sign in', 'Accedi', 'Σύνδεση', 'Anmelden'],
    ['Sign out', 'Esci', 'Αποσύνδεση', 'Abmelden'],
    ['Some rows were skipped', 'Alcune righe sono state saltate', 'Ορισμένες γραμμές παραλείφθηκαν', 'Einige Zeilen wurden übersprungen'],
    ['Thank you!', 'Grazie!', 'Ευχαριστούμε!', 'Vielen Dank!'],
    ['Thank you — your response has been saved.', 'Grazie — la tua risposta è stata salvata.', 'Ευχαριστούμε — η απάντησή σας αποθηκεύτηκε.', 'Vielen Dank — Ihre Antwort wurde gespeichert.'],
    ['You have been invited to our wedding', 'Sei stato invitato al nostro matrimonio', 'Έχετε προσκληθεί στον γάμο μας', 'Du bist zu unserer Hochzeit eingeladen'],
    ['Tap to choose photos', 'Tocca per scegliere le foto', 'Πατήστε για επιλογή φωτογραφιών', 'Tippen, um Fotos zu wählen'],
    ['The celebration has started!', 'La festa è iniziata!', 'Η γιορτή ξεκίνησε!', 'Die Feier hat begonnen!'],
    ['The CSV must include a "name" column in the header row.', 'Il CSV deve includere una colonna "name" nella riga di intestazione.', 'Το CSV πρέπει να περιλαμβάνει στήλη "name" στην κεφαλίδα.', 'Die CSV muss eine Spalte „name“ in der Kopfzeile enthalten.'],
    ['The file is empty.', 'Il file è vuoto.', 'Το αρχείο είναι κενό.', 'Die Datei ist leer.'],
    ['The number of guests cannot exceed :max.', 'Il numero di ospiti non può superare :max.', 'Ο αριθμός καλεσμένων δεν μπορεί να υπερβαίνει το :max.', 'Die Gästeanzahl darf :max nicht überschreiten.'],
    ['The number of guests must be a whole number.', 'Il numero di ospiti deve essere un intero.', 'Ο αριθμός καλεσμένων πρέπει να είναι ακέραιος.', 'Die Gästeanzahl muss eine ganze Zahl sein.'],
    ['There must be at least one guest.', 'Deve esserci almeno un ospite.', 'Πρέπει να υπάρχει τουλάχιστον ένας καλεσμένος.', 'Es muss mindestens ein Gast sein.'],
    ['This email confirms that we have saved your RSVP for :event.', 'Questa email conferma che abbiamo salvato il tuo RSVP per :event.', 'Αυτό το email επιβεβαιώνει ότι αποθηκεύσαμε το RSVP σας για :event.', 'Diese E-Mail bestätigt, dass wir Ihre Zusage für :event gespeichert haben.'],
    ['This file type is not allowed.', 'Questo tipo di file non è consentito.', 'Αυτός ο τύπος αρχείου δεν επιτρέπεται.', 'Dieser Dateityp ist nicht erlaubt.'],
    ['This invitation link is not valid or is no longer active.', 'Questo link d’invito non è valido o non è più attivo.', 'Αυτός ο σύνδεσμος πρόσκλησης δεν είναι έγκυρος ή δεν είναι πλέον ενεργός.', 'Dieser Einladungslink ist ungültig oder nicht mehr aktiv.'],
    ['This token is already used in the file (line :line).', 'Questo token è già usato nel file (riga :line).', 'Αυτό το διακριτικό χρησιμοποιείται ήδη στο αρχείο (γραμμή :line).', 'Dieses Token wird in der Datei bereits verwendet (Zeile :line).'],
    ['Time until the event', 'Tempo all’evento', 'Χρόνος μέχρι την εκδήλωση', 'Zeit bis zur Veranstaltung'],
    ['Too many rows (maximum :max).', 'Troppe righe (massimo :max).', 'Πάρα πολλές γραμμές (μέγιστο :max).', 'Zu viele Zeilen (höchstens :max).'],
    ['Too many uploads. Wait a moment, then try again.', 'Troppi caricamenti. Attendi un momento e riprova.', 'Πάρα πολλές μεταφορτώσεις. Περιμένετε λίγο και δοκιμάστε ξανά.', 'Zu viele Uploads. Bitte kurz warten und erneut versuchen.'],
    ['Update response', 'Aggiorna risposta', 'Ενημέρωση απάντησης', 'Antwort aktualisieren'],
    ['Upload failed. Please try again.', 'Caricamento non riuscito. Riprova.', 'Η μεταφόρτωση απέτυχε. Δοκιμάστε ξανά.', 'Upload fehlgeschlagen. Bitte erneut versuchen.'],
    ['Upload photos', 'Carica foto', 'Μεταφόρτωση φωτογραφιών', 'Fotos hochladen'],
    ['Upload progress', 'Avanzamento caricamento', 'Πρόοδος μεταφόρτωσης', 'Upload-Fortschritt'],
    ['Uploading…', 'Caricamento…', 'Μεταφόρτωση…', 'Wird hochgeladen…'],
    ['Up to :count images at once — JPEG, PNG, WebP, GIF, HEIC — max :mb MB each. Progress is shown while uploading.', 'Fino a :count immagini alla volta — JPEG, PNG, WebP, GIF, HEIC — max :mb MB ciascuna. Avanzamento mostrato durante il caricamento.', 'Έως :count εικόνες ταυτόχρονα — JPEG, PNG, WebP, GIF, HEIC — έως :mb MB η καθεμία. Η πρόοδος εμφανίζεται κατά τη μεταφόρτωση.', 'Bis zu :count Bilder auf einmal — JPEG, PNG, WebP, GIF, HEIC — max. :mb MB pro Datei. Fortschritt wird beim Upload angezeigt.'],
    ['Use JPEG, PNG, WebP, GIF, or HEIC images only.', 'Usa solo immagini JPEG, PNG, WebP, GIF o HEIC.', 'Χρησιμοποιήστε μόνο εικόνες JPEG, PNG, WebP, GIF ή HEIC.', 'Nur JPEG-, PNG-, WebP-, GIF- oder HEIC-Bilder verwenden.'],
    ['View all photos', 'Vedi tutte le foto', 'Προβολή όλων των φωτογραφιών', 'Alle Fotos anzeigen'],
    ['View guest list with RSVP status', 'Vedi lista ospiti con stato RSVP', 'Προβολή λίστας καλεσμένων με κατάσταση RSVP', 'Gästeliste mit RSVP-Status anzeigen'],
    ['We also sent a confirmation to your email address.', 'Abbiamo anche inviato una conferma al tuo indirizzo email.', 'Στείλαμε επίσης επιβεβαίωση στη διεύθυνση email σας.', 'Wir haben auch eine Bestätigung an Ihre E-Mail-Adresse gesendet.'],
    ['We have not yet received your reply for :event.', 'Non abbiamo ancora ricevuto la tua risposta per :event.', 'Δεν έχουμε λάβει ακόμα την απάντησή σας για :event.', 'Wir haben Ihre Antwort für :event noch nicht erhalten.'],
    ['Wedding', 'Matrimonio', 'Γάμος', 'Hochzeit'],
    ['Wedding gallery', 'Galleria matrimonio', 'Συλλογή γάμου', 'Hochzeitsgalerie'],
    ['Welcome', 'Benvenuto', 'Καλώς ήρθατε', 'Willkommen'],
    ['When & where', 'Quando e dove', 'Πότε και πού', 'Wann und wo'],
    ['Wedding event datetime', ':weekday :day :month :year alle ore :time', ':weekday :day :month :year, ώρα :time', ':weekday, :day :month :year um :time Uhr'],
    ['Wedding church venue line', 'Chiesa di Sant\'Anna a Katerini.', 'Ιερό Ναό Αγίας Άννας στη Κατερίνη', 'Sankt-Anna-Kirche in Katerini.'],
    ['Reception', 'Ricevimento', 'Δεξίωση', 'Empfang'],
    ['Reception venue', 'Sala ricevimenti', 'Αίθουσα δεξιώσεων', 'Empfangssaal'],
    ['Wedding reception venue line', 'Dopo la cerimonia, seguirà il ricevimento nella Sala Ricevimenti "Aria".', 'Μετά το μυστήριο θα ακολουθήσει δεξίωση στην Αίθουσα Δεξιώσεων "Aria".', 'Nach der Zeremonie folgt der Empfang in der Empfangshalle „Aria“.'],
    ['Who has responded and RSVP status per invitation.', 'Chi ha risposto e stato RSVP per invito.', 'Ποιοι απάντησαν και κατάσταση RSVP ανά πρόσκληση.', 'Wer geantwortet hat und RSVP-Status pro Einladung.'],
    ['Will you attend?', 'Parteciperai?', 'Θα παρευρεθείτε;', 'Nehmen Sie teil?'],
    ['Yes', 'Sì', 'Ναι', 'Ja'],
    ['You already sent a response. Update the form below if something changed.', 'Hai già inviato una risposta. Aggiorna il modulo qui sotto se qualcosa è cambiato.', 'Έχετε ήδη στείλει απάντηση. Ενημερώστε τη φόρμα παρακάτω αν κάτι άλλαξε.', 'Sie haben bereits geantwortet. Aktualisieren Sie das Formular unten, wenn sich etwas geändert hat.'],
    ['You can upload at most :max photos at once.', 'Puoi caricare al massimo :max foto alla volta.', 'Μπορείτε να μεταφορτώσετε το πολύ :max φωτογραφίες ταυτόχρονα.', 'Sie können höchstens :max Fotos auf einmal hochladen.'],
    ['You opened this page with your personal link. You can change your answers anytime — only the latest save is kept.', 'Hai aperto questa pagina con il tuo link personale. Puoi modificare le risposte in qualsiasi momento — viene conservato solo l’ultimo salvataggio.', 'Ανοίξατε αυτή τη σελίδα με τον προσωπικό σας σύνδεσμο. Μπορείτε να αλλάξετε τις απαντήσεις οποτεδήποτε — διατηρείται μόνο η τελευταία αποθήκευση.', 'Sie haben diese Seite mit Ihrem persönlichen Link geöffnet. Sie können Ihre Antworten jederzeit ändern — es gilt nur die letzte Speicherung.'],
    ['Your name', 'Il tuo nome', 'Το όνομά σας', 'Ihr Name'],
    ['Your notes', 'Le tue note', 'Οι σημειώσεις σας', 'Ihre Notizen'],
    ['Your RSVP has been updated.', 'Il tuo RSVP è stato aggiornato.', 'Το RSVP σας ενημερώθηκε.', 'Ihre Zusage wurde aktualisiert.'],
    ['Add item', 'Aggiungi voce', 'Προσθήκη στοιχείου', 'Eintrag hinzufügen'],
    ['Available', 'Disponibile', 'Διαθέσιμο', 'Verfügbar'],
    ['Cancel', 'Annulla', 'Ακύρωση', 'Abbrechen'],
    ['Choose an available gift. Once reserved, it disappears from this list for other guests.', 'Scegli un regalo tra quelli disponibili. Dopo la prenotazione scompare dall’elenco per tutti gli altri invitati.', 'Επιλέξτε ένα διαθέσιμο δώρο. Μόλις κρατηθεί, εξαφανίζεται από τη λίστα για τους υπόλοιπους καλεσμένους.', 'Wählen Sie ein verfügbares Geschenk. Nach der Reservierung verschwindet es für andere Gäste aus dieser Liste.'],
    ['Clear reservation', 'Azzera prenotazione', 'Διαγραφή κράτησης', 'Reservierung löschen'],
    ['Delete this item?', 'Eliminare questa voce?', 'Διαγραφή αυτού του στοιχείου;', 'Diesen Eintrag löschen?'],
    ['Description (optional)', 'Descrizione (facoltativa)', 'Περιγραφή (προαιρετικά)', 'Beschreibung (optional)'],
    ['Edit', 'Modifica', 'Επεξεργασία', 'Bearbeiten'],
    ['Edit gift item', 'Modifica voce lista nozze', 'Επεξεργασία δώρου', 'Geschenk bearbeiten'],
    ['Gift list', 'Lista nozze', 'Λίστα δώρων', 'Geschenkliste'],
    ['Hidden', 'Nascosto', 'Κρυφό', 'Ausgeblendet'],
    ['I will bring this gift', 'Porto io questo regalo', 'Θα φέρω αυτό το δώρο', 'Ich bringe dieses Geschenk mit'],
    ['Item added.', 'Voce aggiunta.', 'Το στοιχείο προστέθηκε.', 'Eintrag hinzugefügt.'],
    ['Item deleted.', 'Voce eliminata.', 'Το στοιχείο διαγράφηκε.', 'Eintrag gelöscht.'],
    ['Item title', 'Titolo', 'Τίτλος', 'Titel'],
    ['Item updated.', 'Voce aggiornata.', 'Το στοιχείο ενημερώθηκε.', 'Eintrag aktualisiert.'],
    ['Items', 'Voci', 'Στοιχεία', 'Einträge'],
    ['Link (optional)', 'Link (facoltativo)', 'Σύνδεσμος (προαιρετικά)', 'Link (optional)'],
    ['Manage the gift list shown to guests.', 'Gestisci la lista nozze visibile agli invitati.', 'Διαχειριστείτε τη λίστα δώρων που βλέπουν οι καλεσμένοι.', 'Verwalten Sie die Geschenkliste für Gäste.'],
    ['Nobody yet', 'Nessuno', 'Κανένας ακόμα', 'Noch niemand'],
    ['No items in the gift list yet.', 'Non ci sono ancora voci nella lista nozze.', 'Δεν υπάρχουν ακόμα στοιχεία στη λίστα δώρων.', 'Noch keine Einträge in der Geschenkliste.'],
    ['Reserved', 'Prenotato', 'Κεκρατημένο', 'Reserviert'],
    ['Reserved by', 'Prenotato da', 'Κράτηση από', 'Reserviert von'],
    ['Sign in with your invite', 'Accedi con il tuo invito', 'Συνδεθείτε με την πρόσκλησή σας', 'Mit Ihrer Einladung anmelden'],
    ['Sort order', 'Ordine', 'Σειρά ταξινόμησης', 'Sortierung'],
    ['Status', 'Stato', 'Κατάσταση', 'Status'],
    ['This gift is already reserved by another guest.', 'Questo regalo è già stato prenotato da un altro invitato.', 'Αυτό το δώρο έχει ήδη κρατηθεί από άλλον καλεσμένο.', 'Dieses Geschenk ist bereits von einem anderen Gast reserviert.'],
    ['Title', 'Titolo', 'Τίτλος', 'Titel'],
    ['Update details or clear a reservation.', 'Aggiorna i dettagli o azzera una prenotazione.', 'Ενημερώστε λεπτομέρειες ή διαγράψτε μια κράτηση.', 'Details aktualisieren oder Reservierung löschen.'],
    ['Update invitation details and RSVP.', 'Aggiorna dati invito e RSVP.', 'Ενημερώστε τα στοιχεία πρόσκλησης και το RSVP.', 'Einladungsdaten und RSVP aktualisieren.'],
    ['Use your invitation link to reserve gifts.', 'Usa il link del tuo invito per prenotare i regali.', 'Χρησιμοποιήστε τον σύνδεσμο της πρόσκλησής σας για κράτηση δώρων.', 'Nutzen Sie Ihren Einladungslink, um Geschenke zu reservieren.'],
    ['Visible', 'Visibile', 'Ορατό', 'Sichtbar'],
    ['View QR', 'Vedi QR', 'Προβολή QR', 'QR anzeigen'],
    ['Visible on public page', 'Visibile sul sito', 'Ορατό στη δημόσια σελίδα', 'Auf der öffentlichen Seite sichtbar'],
    ['Your gift selection has been saved.', 'La tua scelta è stata salvata.', 'Η επιλογή σας αποθηκεύτηκε.', 'Ihre Auswahl wurde gespeichert.'],
    ['Your name for this gift', 'Il tuo nome per questo regalo', 'Το όνομά σας για αυτό το δώρο', 'Ihr Name für dieses Geschenk'],
    ['Anonymous (browser)', 'Anonimo (browser)', 'Ανώνυμο (πρόγραμμα περιήγησης)', 'Anonym (Browser)'],
    ['Reservations without an invitation link are tied to the visitor\'s browser. To remove a gift chosen by mistake, open Edit and use Clear reservation.', 'Le prenotazioni senza link d’invito restano legate al browser del visitatore. Per rimuovere un regalo scelto per errore, apri Modifica e spunta «Azzera prenotazione».', 'Οι κρατήσεις χωρίς σύνδεσμο πρόσκλησης συνδέονται με το πρόγραμμα περιήγησης. Για να αφαιρέσετε λάθος επιλογή, ανοίξτε Επεξεργασία και χρησιμοποιήστε «Διαγραφή κράτησης».', 'Reservierungen ohne Einladungslink sind an den Browser des Besuchers gebunden. Um ein versehentlich gewähltes Geschenk zu entfernen, öffnen Sie Bearbeiten und aktivieren Sie „Reservierung löschen“.'],
    ['No gifts are available right now. Everything on the list has already been chosen.', 'Al momento non ci sono regali disponibili: tutte le voci della lista sono già state scelte.', 'Αυτή τη στιγμή δεν υπάρχουν διαθέσιμα δώρα: όλα τα στοιχεία της λίστας έχουν ήδη επιλεγεί.', 'Derzeit sind keine Geschenke verfügbar: Alle Einträge der Liste wurden bereits gewählt.'],
];

// Unicode apostrophe in "You've" (must match __('…') source key)
$rows[] = ['You’ve reached the end.', 'Hai raggiunto la fine.', 'Φτάσατε στο τέλος.', 'Sie sind am Ende angekommen.'];

// Feature strings added later (keep here to survive regeneration).
$rows[] = ['Add to calendar', 'Aggiungi al calendario', 'Προσθήκη στο ημερολόγιο', 'Zum Kalender hinzufügen'];
$rows[] = ['How to get there', 'Come arrivare', 'Πώς θα φτάσετε', 'So kommen Sie hin'];
$rows[] = ['Frequently asked questions', 'Domande frequenti', 'Συχνές ερωτήσεις', 'Häufige Fragen'];

// RSVP companion names
$rows[] = ['Names of people coming with you', 'Nomi delle persone che vengono con te', 'Ονόματα των ατόμων που έρχονται μαζί σας', 'Namen der Personen, die mitkommen'];
$rows[] = ['One name per line', 'Un nome per riga', 'Ένα όνομα ανά γραμμή', 'Ein Name pro Zeile'];
$rows[] = ['Optional — helps us prepare place cards and seating.', 'Opzionale — ci aiuta a preparare i segnaposto e la disposizione dei tavoli.', 'Προαιρετικό — μας βοηθά να προετοιμάσουμε τις καρτέλες θέσεων και τη διάταξη.', 'Optional — hilft uns bei Platzkarten und Sitzordnung.'];
$rows[] = ['Each companion name cannot exceed :max characters.', 'Ogni nome di accompagnatore non può superare :max caratteri.', 'Κάθε όνομα συνοδού δεν μπορεί να υπερβαίνει τους :max χαρακτήρες.', 'Jeder Begleitername darf höchstens :max Zeichen lang sein.'];
$rows[] = ['Companion names are only used when attending.', 'I nomi degli accompagnatori servono solo se parteciperai.', 'Τα ονόματα συνοδών χρησιμοποιούνται μόνο αν θα παρευρεθείτε.', 'Begleiternamen werden nur bei Zusage berücksichtigt.'];
$rows[] = ['You can list at most :max companion name(s) for :count attendees.', 'Puoi indicare al massimo :max nomi di accompagnatori per :count partecipanti.', 'Μπορείτε να δηλώσετε το πολύ :max ονόματα συνοδών για :count άτομα.', 'Sie können höchstens :max Begleiternamen für :count Personen angeben.'];
$rows[] = ['Companion names', 'Nomi degli accompagnatori', 'Ονόματα συνοδών', 'Begleiternamen'];
$rows[] = ['One name per line. Used for place cards and seating.', 'Un nome per riga. Usati per i segnaposto e la disposizione dei tavoli.', 'Ένα όνομα ανά γραμμή. Χρησιμοποιούνται για καρτέλες θέσεων και διάταξη.', 'Ein Name pro Zeile. Wird für Platzkarten und Sitzordnung verwendet.'];

// Admin: CSV export
$rows[] = ['Export RSVP as CSV', 'Esporta RSVP in CSV', 'Εξαγωγή RSVP σε CSV', 'RSVP als CSV exportieren'];

// Admin: Seating chart
$rows[] = ['Seating chart', 'Disposizione tavoli', 'Διάταξη τραπεζιών', 'Sitzordnung'];
$rows[] = ['Create tables and assign guests. Capacity counts main guest + their companions.', 'Crea i tavoli e assegna gli ospiti. La capacità conta l’invitato principale e i suoi accompagnatori.', 'Δημιουργήστε τραπέζια και αναθέστε καλεσμένους. Η χωρητικότητα μετρά τον κύριο καλεσμένο και τους συνοδούς του.', 'Tische anlegen und Gäste zuweisen. Die Kapazität zählt Hauptgast und Begleiter.'];
$rows[] = ['Add table', 'Aggiungi tavolo', 'Προσθήκη τραπεζιού', 'Tisch hinzufügen'];
$rows[] = ['Label', 'Etichetta', 'Ετικέτα', 'Bezeichnung'];
$rows[] = ['Capacity', 'Capacità', 'Χωρητικότητα', 'Kapazität'];
$rows[] = ['Table created.', 'Tavolo creato.', 'Το τραπέζι δημιουργήθηκε.', 'Tisch angelegt.'];
$rows[] = ['Table updated.', 'Tavolo aggiornato.', 'Το τραπέζι ενημερώθηκε.', 'Tisch aktualisiert.'];
$rows[] = ['Table deleted.', 'Tavolo eliminato.', 'Το τραπέζι διαγράφηκε.', 'Tisch gelöscht.'];
$rows[] = ['Guest assigned to table.', 'Ospite assegnato al tavolo.', 'Ο καλεσμένος ανατέθηκε στο τραπέζι.', 'Gast dem Tisch zugewiesen.'];
$rows[] = ['Guest removed from table.', 'Ospite rimosso dal tavolo.', 'Ο καλεσμένος αφαιρέθηκε από το τραπέζι.', 'Gast vom Tisch entfernt.'];
$rows[] = [':occupied / :cap seats', ':occupied / :cap posti', ':occupied / :cap θέσεις', ':occupied / :cap Plätze'];
$rows[] = [':occupied seats', ':occupied posti', ':occupied θέσεις', ':occupied Plätze'];
$rows[] = [':count seats', ':count posti', ':count θέσεις', ':count Plätze'];
$rows[] = ['No tables yet. Add the first one above.', 'Ancora nessun tavolo. Aggiungi il primo qui sopra.', 'Δεν υπάρχουν ακόμη τραπέζια. Προσθέστε το πρώτο παραπάνω.', 'Noch keine Tische. Fügen Sie oben den ersten hinzu.'];
$rows[] = ['No guests assigned yet.', 'Nessun ospite assegnato.', 'Δεν έχουν ανατεθεί ακόμη καλεσμένοι.', 'Noch keine Gäste zugewiesen.'];
$rows[] = ['— pick a guest —', '— scegli un ospite —', '— επιλέξτε καλεσμένο —', '— Gast auswählen —'];
$rows[] = ['Assign', 'Assegna', 'Ανάθεση', 'Zuweisen'];
$rows[] = ['Assign guest', 'Assegna ospite', 'Ανάθεση καλεσμένου', 'Gast zuweisen'];
$rows[] = ['Remove from table', 'Rimuovi dal tavolo', 'Αφαίρεση από το τραπέζι', 'Vom Tisch entfernen'];
$rows[] = ['Delete this table? Guests will become unassigned.', 'Eliminare questo tavolo? Gli ospiti torneranno non assegnati.', 'Διαγραφή αυτού του τραπεζιού; Οι καλεσμένοι θα γίνουν μη ανατεθειμένοι.', 'Diesen Tisch löschen? Gäste werden wieder nicht zugewiesen.'];
$rows[] = ['Unassigned guests', 'Ospiti non assegnati', 'Μη ανατεθειμένοι καλεσμένοι', 'Nicht zugewiesene Gäste'];
$rows[] = ['Everyone has a table.', 'Tutti hanno un tavolo.', 'Όλοι έχουν τραπέζι.', 'Alle haben einen Tisch.'];
$rows[] = ['Edit table', 'Modifica tavolo', 'Επεξεργασία τραπεζιού', 'Tisch bearbeiten'];
$rows[] = ['Back to seating chart', 'Torna alla disposizione tavoli', 'Πίσω στη διάταξη τραπεζιών', 'Zurück zur Sitzordnung'];

// Admin: Audit log
$rows[] = ['Audit log', 'Registro attività', 'Ιστορικό ενεργειών', 'Aktionsprotokoll'];
$rows[] = ['Recent administrative actions on this site.', 'Azioni recenti eseguite dagli amministratori.', 'Πρόσφατες ενέργειες διαχειριστή στον ιστότοπο.', 'Letzte Administrator-Aktionen auf dieser Seite.'];
$rows[] = ['Filter by action', 'Filtra per azione', 'Φιλτράρισμα κατά ενέργεια', 'Nach Aktion filtern'];
$rows[] = ['All actions', 'Tutte le azioni', 'Όλες οι ενέργειες', 'Alle Aktionen'];
$rows[] = ['Clear filter', 'Rimuovi filtro', 'Καθαρισμός φίλτρου', 'Filter entfernen'];
$rows[] = ['No audit entries recorded yet.', 'Nessuna voce ancora registrata.', 'Δεν έχουν καταγραφεί ακόμη καταχωρήσεις.', 'Noch keine Einträge erfasst.'];
$rows[] = ['When', 'Quando', 'Πότε', 'Wann'];
$rows[] = ['Action', 'Azione', 'Ενέργεια', 'Aktion'];
$rows[] = ['Subject', 'Oggetto', 'Αντικείμενο', 'Objekt'];
$rows[] = ['IP', 'IP', 'IP', 'IP'];
$rows[] = ['Details', 'Dettagli', 'Λεπτομέρειες', 'Details'];

$it = [];
$el = [];
$de = [];
foreach ($rows as $r) {
    if (count($r) !== 4) {
        throw new InvalidArgumentException('Each row must have [en, it, el, de]');
    }
    [$en, $itVal, $elVal, $deVal] = $r;
    if (array_key_exists($en, $it)) {
        throw new InvalidArgumentException('Duplicate English key: '.$en);
    }
    $it[$en] = $itVal;
    $el[$en] = $elVal;
    $de[$en] = $deVal;
}

$dir = __DIR__;
file_put_contents($dir.'/it.json', json_encode($it, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");
file_put_contents($dir.'/el.json', json_encode($el, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");
file_put_contents($dir.'/de.json', json_encode($de, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");

echo 'Wrote '.count($it).' keys to it.json, el.json, and de.json'."\n";
