/**
 * Wedding gallery → Google Drive (after admin approval only).
 *
 * Setup:
 * 1. Create a folder in Google Drive and copy its ID from the URL.
 * 2. Replace SECRET and FOLDER_ID below (use a long random secret).
 * 3. Extensions → Google Apps Script → paste this file → Save.
 * 4. Deploy → New deployment → Type: Web app
 *    - Execute as: Me
 *    - Who has access: Anyone
 * 5. Copy the Web app URL into Laravel:
 *    GOOGLE_DRIVE_APPS_SCRIPT_URL=https://script.google.com/macros/s/.../exec
 *    GOOGLE_DRIVE_APPS_SCRIPT_SECRET=same-as-SECRET-below
 *    GOOGLE_DRIVE_FOLDER_ID=same-as-FOLDER_ID-below
 */

const SECRET = 'change-me-to-a-long-random-string';
const FOLDER_ID = 'your-google-drive-folder-id';

/** Apri l'URL /exec nel browser: se vedi ok:true, il deploy web è attivo. */
function doGet() {
  return jsonResponse({
    ok: true,
    message: 'Drive sync ready. Uploads arrive via POST after admin approval.',
  });
}

/** Esegui una volta dall'editor (▶) per autorizzare l'accesso a Drive. */
function authorizeDriveOnce() {
  const folder = DriveApp.getFolderById(FOLDER_ID);
  return folder.getName();
}

function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return jsonResponse({ ok: false, error: 'missing body' });
    }

    const payload = JSON.parse(e.postData.contents);

    if (payload.secret !== SECRET) {
      return jsonResponse({ ok: false, error: 'unauthorized' });
    }

    if (payload.action === 'delete') {
      return deleteDriveFile(payload.fileId);
    }

    return uploadDriveFile(payload);
  } catch (err) {
    return jsonResponse({ ok: false, error: String(err) });
  }
}

function deleteDriveFile(fileId) {
  if (!fileId) {
    return jsonResponse({ ok: false, error: 'missing fileId' });
  }

  DriveApp.getFileById(fileId).setTrashed(true);

  return jsonResponse({ ok: true });
}

function uploadDriveFile(payload) {
  if (!payload.fileBase64 || !payload.filename) {
    return jsonResponse({ ok: false, error: 'missing file' });
  }

  const folder = DriveApp.getFolderById(FOLDER_ID);
  const bytes = Utilities.base64Decode(payload.fileBase64);
  const mime = payload.mimeType || 'image/jpeg';
  const blob = Utilities.newBlob(bytes, mime, payload.filename);
  const file = folder.createFile(blob);

  return jsonResponse({ ok: true, id: file.getId() });
}

function jsonResponse(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(
    ContentService.MimeType.JSON
  );
}
