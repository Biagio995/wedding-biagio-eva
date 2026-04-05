# Wedding Invitation Web App (Laravel)

## Overview
Questo progetto consiste nello sviluppo di una web app per la gestione di un matrimonio, accessibile tramite QR code, che consente agli invitati di:
- Confermare la presenza (RSVP)
- Caricare fotografie scattate durante l'evento

L'applicazione sarà sviluppata utilizzando Laravel come backend framework.

---

## Obiettivi del Progetto
- Creare una landing page accessibile tramite QR code
- Permettere RSVP semplici e veloci
- Consentire upload foto da dispositivi mobili
- Gestire gli invitati lato admin
- Offrire una galleria condivisa delle foto

---

## Tipologie di Utenti

### 1. Invitato
- Accede tramite QR code
- Può confermare presenza
- Può caricare foto
- Può visualizzare foto (opzionale)

### 2. Admin (Sposi o organizzatori)
- Gestisce invitati
- Visualizza RSVP
- Modera foto
- Scarica contenuti

---

## User Stories (English Version)

### Access and Identification

**US-01 - Access via QR Code (Gallery Section)**
- As a guest
- I want to scan a QR code
- So that I can directly open the gallery section and upload photos

**Acceptance Criteria:**
- QR code points to the gallery route of the web app (e.g. /gallery or /gallery/upload)
- If a token is present, the guest is recognized automatically
- The gallery view allows immediate photo upload
- Mobile-friendly interface optimized for quick uploads
- Optional fallback: direct upload to cloud storage via pre-signed URL

---

**US-02 - Separate access to wedding web app**
- As a guest
- I want to access the wedding web app via a dedicated link
- So that I can RSVP and view event details

**Acceptance Criteria:**
- URL separate from QR code
- Can include tokenized access

---

**US-03 - Personalized access via token**
- As a guest
- I want to access via a unique link
- So that the system can recognize me automatically

**Acceptance Criteria:**
- Unique token per guest
- RSVP fields pre-filled

---

### Event Landing Page

**US-03 - View event information**
- As a guest
- I want to see date, location, and details
- So that I know when and where to go

**Acceptance Criteria:**
- Event date and time
- Location with Google Maps link
- Additional notes

---

**US-04 - Event countdown**
- As a guest
- I want to see a countdown
- So that I know how much time is left

**Acceptance Criteria:**
- Dynamic timer
- Real-time update

---

### RSVP Management

**US-05 - Submit RSVP**
- As a guest
- I want to confirm my attendance
- So that the couple can organize the event

**Acceptance Criteria:**
- Yes/No selection
- Number of attendees
- Field validation

---

**US-06 - Add RSVP details**
- As a guest
- I want to add notes
- So that I can inform about allergies or requests

**Acceptance Criteria:**
- Free text field
- Persistent storage

---

**US-07 - Edit RSVP**
- As a guest
- I want to modify my response
- So that I can update changes

**Acceptance Criteria:**
- Access via token
- Update saved data

---

**US-08 - RSVP confirmation feedback**
- As a guest
- I want to see a confirmation message
- So that I know my response was submitted

**Acceptance Criteria:**
- Success message
- Optional confirmation email

---

### Photo Upload

**US-09 - Upload single photo**
- As a guest
- I want to upload a photo
- So that I can share it

**Acceptance Criteria:**
- Immediate upload
- File validation

---

**US-10 - Upload multiple photos**
- As a guest
- I want to upload multiple photos at once
- So that I can save time

**Acceptance Criteria:**
- Multi-select support
- Progress bar

---

**US-11 - Automatic image compression**
- As a system
- I want to compress images
- So that storage usage is optimized

**Acceptance Criteria:**
- File size reduction
- Acceptable quality preserved

---

**US-12 - Associate photo with guest**
- As a system
- I want to link photos to the uploader
- So that uploads are traceable

**Acceptance Criteria:**
- guest_id stored

---

### Photo Gallery

**US-13 - View public gallery**
- As a guest
- I want to see all uploaded photos
- So that I can relive the event

**Acceptance Criteria:**
- Responsive grid
- Infinite scroll

---

**US-14 - Filter photos**
- As a guest
- I want to filter photos
- So that I can find content easily

**Acceptance Criteria:**
- Filter by date

---

**US-15 - Download photos**
- As a guest
- I want to download photos
- So that I can keep them

**Acceptance Criteria:**
- Single photo download

---

### Admin - Guest Management

**US-16 - Create guests**
- As an admin
- I want to create guest records
- So that I can manage invitations

---

**US-17 - Generate QR codes**
- As an admin
- I want to generate unique QR codes
- So that I can distribute them

---

**US-18 - Import guests via CSV**
- As an admin
- I want to import a guest list
- So that I can save time

---

### Admin - RSVP

**US-19 - RSVP dashboard**
- As an admin
- I want to see statistics
- So that I can monitor attendance

---

**US-20 - Guest list with RSVP status**
- As an admin
- I want to see who responded

---

### Admin - Photos

**US-21 - Moderate photos**
- As an admin
- I want to approve photos

---

**US-22 - Delete content**
- As an admin
- I want to remove inappropriate photos

---

**US-23 - Bulk download photos**
- As an admin
- I want to download all photos

---

### Notifications

**US-24 - RSVP notification**
- As an admin
- I want to receive a notification

---

**US-25 - RSVP reminder**
- As a system
- I want to send reminders

---

### Security and Controls

**US-26 - Secure uploads**
- As a system
- I want to validate files

---

**US-27 - Rate limiting uploads**
- As a system
- I want to limit requests

---

### User Experience

**US-28 - Mobile-first UX**
- As a guest
- I want an optimized mobile experience

---

**US-29 - Fast access without login**
- As a guest
- I want to avoid registration

---

**US-30 - High performance**
- As a user
- I want fast loading times

---

## Requisiti Funzionali
- Generazione QR code
- Sistema RSVP
- Upload immagini
- Dashboard admin

## Requisiti Non Funzionali
- Mobile-first
- Performance ottimizzata
- Sicurezza upload (validazione file)
- Scalabilità storage (es. S3)

---

## Architettura Tecnica

### Backend
- Laravel
- API REST

### Frontend
- Blade o Vue.js
- Responsive design

### Storage
- Local o AWS S3

### Database
- MySQL / PostgreSQL

---

## Schema Database (Bozza)

### Guests
- id
- name
- email
- token
- rsvp_status
- guests_count
- notes

### Photos
- id
- guest_id
- file_path
- approved
- created_at

---

## API Endpoints (Esempio)

### RSVP
- POST /api/rsvp
- PUT /api/rsvp/{token}

### Photos
- POST /api/photos
- GET /api/photos

---

## Sicurezza
- Validazione file upload
- Protezione CSRF
- Rate limiting upload

---

## Extra Features (Future)
- Notifiche email
- Slideshow live durante evento
- Download zip foto

---

## Setup Laravel (High Level)
1. Install Laravel
2. Configurare DB
3. Creare modelli (Guest, Photo)
4. Creare controller
5. Creare routes
6. Setup storage

---

## Conclusione
Questa base di progetto consente di costruire una piattaforma completa e scalabile per la gestione digitale di un matrimonio.

