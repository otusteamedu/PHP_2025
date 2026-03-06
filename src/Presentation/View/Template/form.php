<?php
$oldEmail = $old['email'] ?? '';
$oldFrom = $old['date_from'] ?? '';
$oldTo = $old['date_to'] ?? '';
?>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars((string) $error) ?></div>
<?php endif; ?>

<form method="post" action="/statements">
    <div class="row">
        <label>Email</label>
        <input name="email" type="email" required value="<?= htmlspecialchars((string) $oldEmail) ?>">
        <small>Statement will be sent to this email</small>
    </div>

    <div class="row">
        <label>Date from</label>
        <input name="date_from" type="date" required value="<?= htmlspecialchars((string) $oldFrom) ?>">
    </div>

    <div class="row">
        <label>Date to</label>
        <input name="date_to" type="date" required value="<?= htmlspecialchars((string) $oldTo) ?>">
    </div>

    <div class="row">
        <button type="submit">Request statement</button>
    </div>
</form>
