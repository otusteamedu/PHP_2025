const form = document.querySelector('form[name="form"]')

if (form.attachEvent) {
    form.attachEvent("submit", validateEmails);
} else {
    form.addEventListener("submit", validateEmails);
}

async function validateEmails(e) {
    e.preventDefault();

    const emailsText = new FormData(form);
    const emails = emailsText.get('emails').split(/;\n|;/);

    if (emails.length === 0) {
        alert('Please enter at least one email address');
        return;
    }

    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ emails: emails })
        });

        const data = await response.json();
        displayResults(data);
    } catch (error) {
        console.error('Error:', error);
        showError('Error validating emails. Please try again.');
    }
}

function displayResults(data) {
    const resultsDiv = document.getElementById('results');

    if (data.error) {
        resultsDiv.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
        resultsDiv.style.display = 'block';
        return;
    }

    let html = '<h2>Validation Results</h2>';

    for (const [email, result] of Object.entries(data.data)) {
        const cssClass = result.valid ? 'valid' : 'invalid';
        const resultClass = result.valid ? 'valid-result' : 'invalid-result';
        const status = result.valid ? 'Valid' : 'Invalid';

        html += `
                <div class="result-item ${resultClass}">
                    <strong>${email}</strong>: <span class="${cssClass}">${status}</span>
                    <div style="margin-top: 5px; font-size: 0.9em; color: #666;">
                        Regex check: ${result.regex_check ? '✓' : '✗'} 
                        ${result.mx_check !== null ? `| MX check: ${result.mx_check ? '✓' : '✗'}` : ''}
                        ${result.dns_check !== null ? `| DNS check: ${result.dns_check ? '✓' : '✗'}` : ''}
                        ${result.reason ? `| ${result.reason}` : ''}
                    </div>
                </div>`;
    }

    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

function showError(message) {
    document.getElementById('results').innerHTML = '<p style="color: red;">'+message+'</p>';
    document.getElementById('results').style.display = 'block';
}