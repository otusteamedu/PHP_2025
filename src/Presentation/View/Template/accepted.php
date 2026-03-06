<p><b>Request accepted for processing.</b></p>

<ul>
    <li>Request ID: <?= htmlspecialchars((string) $requestId) ?></li>
    <li>Period: <?= htmlspecialchars((string) $dateFrom) ?> - <?= htmlspecialchars((string) $dateTo) ?></li>
    <li>Email: <?= htmlspecialchars((string) $email) ?></li>
</ul>

<p>As soon as the statement is generated, we will send it to the specified email address</p>

<p><a href="/">Create new request</a></p>
