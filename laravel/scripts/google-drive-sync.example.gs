/**
 * Template — identical to google-drive-sync.gs (kept for reference).
 */

function expectedSecret() {
  const secret = PropertiesService.getScriptProperties().getProperty('SECRET');
  if (!secret) {
    throw new Error('Missing Script property SECRET (copy GOOGLE_DRIVE_APPS_SCRIPT_SECRET from .env)');
  }
  return secret;
}

function doGet() {
  return jsonResponse({
    ok: true,
    message: 'Drive sync ready. Uploads arrive via POST after admin approval.',
  });
}

function authorizeDriveOnce() {
  const folderId = PropertiesService.getScriptProperties().getProperty('FOLDER_ID');
  if (!folderId) {
    throw new Error('Add Script property FOLDER_ID (same as GOOGLE_DRIVE_FOLDER_ID in .env) before authorizing.');
  }
  return DriveApp.getFolderById(folderId).getName();
}

function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return jsonResponse({ ok: false, error: 'missing body' });
    }

    const payload = JSON.parse(e.postData.contents);

    if (payload.secret !== expectedSecret()) {
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

  const folderId = payload.folderId;
  if (!folderId) {
    return jsonResponse({ ok: false, error: 'missing folderId' });
  }

  const folder = DriveApp.getFolderById(folderId);
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
