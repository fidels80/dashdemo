<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; font-size: 13px; color: #333; margin:0; padding:20px;">

<table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
    <tr>
        <td style="background-color:#dc3545; color:#fff; padding:12px 16px; font-size:16px; font-weight:bold;">
            &#9888; Segnalazione Anomalia - Dashboard Ufficio 2000
        </td>
    </tr>
</table>

<!-- DESCRIZIONE -->
<table style="width:100%; border-collapse:collapse; border:2px solid #dc3545; margin-bottom:20px;">
    <tr>
        <td style="background-color:#dc3545; color:#fff; padding:8px 12px; font-weight:bold;">
            Descrizione dell'anomalia
        </td>
    </tr>
    <tr>
        <td style="padding:12px; border:1px solid #dee2e6; line-height:1.6; font-size:14px;">
            <?= nl2br(htmlspecialchars($data['descrizione'])) ?>
        </td>
    </tr>
</table>

<!-- DATI UTENTE -->
<table style="width:100%; border-collapse:collapse; border:1px solid #dee2e6; margin-bottom:20px;">
    <tr>
        <td colspan="2" style="background-color:#0d6efd; color:#fff; padding:6px 12px; font-weight:bold; font-size:13px;">
            Dati Utente
        </td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; width:180px; border:1px solid #dee2e6; background:#f8f9fa;">Username</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_username'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">ID Utente</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_id'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Email</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_email'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Livello (Level)</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_level'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Gruppo</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_gruppo'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Ultimo Login</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['user_lastlogin'] ?? 'N/A') ?></td>
    </tr>
</table>

<!-- CONTESTO PAGINA -->
<table style="width:100%; border-collapse:collapse; border:1px solid #dee2e6; margin-bottom:20px;">
    <tr>
        <td colspan="2" style="background-color:#6c757d; color:#fff; padding:6px 12px; font-weight:bold; font-size:13px;">
            Contesto Pagina
        </td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; width:180px; border:1px solid #dee2e6; background:#f8f9fa;">Data/Ora</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= $data['timestamp'] ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Pagina / Procedura</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['pagina']) ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">URL</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6; word-break:break-all;"><?= htmlspecialchars($data['url']) ?></td>
    </tr>
    <?php if (!empty($data['record_id'])): ?>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Record ID</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['record_id']) ?></td>
    </tr>
    <?php endif; ?>
    <?php if (!empty($data['dati_extra'])): ?>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa; vertical-align:top;">Dati Aggiuntivi</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= nl2br(htmlspecialchars($data['dati_extra'])) ?></td>
    </tr>
    <?php endif; ?>
</table>

<!-- SESSIONE E BROWSER -->
<table style="width:100%; border-collapse:collapse; border:1px solid #dee2e6; margin-bottom:20px;">
    <tr>
        <td colspan="2" style="background-color:#198754; color:#fff; padding:6px 12px; font-weight:bold; font-size:13px;">
            Sessione e Browser
        </td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; width:180px; border:1px solid #dee2e6; background:#f8f9fa;">Session ID</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6; font-family:monospace; font-size:11px;"><?= htmlspecialchars($data['session_id'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">IP Client</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['ip_client'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Connessione</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['is_secure'] ?? 'N/A') ?> (<?= htmlspecialchars($data['request_method'] ?? 'N/A') ?>)</td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Browser</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6; word-break:break-all;"><?= htmlspecialchars($data['browser']) ?></td>
    </tr>
</table>

<!-- SERVER -->
<table style="width:100%; border-collapse:collapse; border:1px solid #dee2e6; margin-bottom:20px;">
    <tr>
        <td colspan="2" style="background-color:#ffc107; color:#333; padding:6px 12px; font-weight:bold; font-size:13px;">
            Server
        </td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; width:180px; border:1px solid #dee2e6; background:#f8f9fa;">IP Server</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['ip_server'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Software Server</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['server_software'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">PHP Version</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['php_version'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 12px; font-weight:bold; border:1px solid #dee2e6; background:#f8f9fa;">Ambiente</td>
        <td style="padding:6px 12px; border:1px solid #dee2e6;"><?= htmlspecialchars($data['app_env'] ?? 'N/A') ?></td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="padding:8px 12px; background-color:#f8f9fa; font-size:11px; color:#6c757d; border:1px solid #dee2e6;">
            Email generata automaticamente dalla Dashboard Ufficio 2000.
        </td>
    </tr>
</table>

</body>
</html>
