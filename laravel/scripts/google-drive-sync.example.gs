/**
 * Template — copy to google-drive-sync.gs and fill SECRET + FOLDER_ID.
 * google-drive-sync.gs is gitignored (contains secrets).
 */

const SECRET = 'change-me-to-a-long-random-string';
const FOLDER_ID = 'your-google-drive-folder-id';

function doGet() {
  return jsonResponse({
    ok: true,
    message: 'Drive sync ready. Uploads arrive via POST after admin approval.',
  });
}

function authorizeDriveOnce() {
  return DriveApp.getFolderById(FOLDER_ID).getName();
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
