{{-- resources/views/ghl/credentials-form.blade.php --}}
<h2>Set Up Your Payment Gateway</h2>

<form method="POST" action="{{ route('ghl.credentials.form', $locationId) }}">
    @csrf
    <label>API Key:</label>
    <input type="text" name="api_key" required>
    
    <label>Secret Key:</label>
    <input type="text" name="secret_key" required>

    <button type="submit">Save</button>
</form>
