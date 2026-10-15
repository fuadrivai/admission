<form method="POST" action="{{ route('microsite.verify-code', $event->slug) }}">
    @csrf
    <input type="hidden" name="destination" value="{{ $destination }}">
    <label for="family-code" class="form-label">Family invitation code</label>
    <div class="d-flex flex-wrap gap-2">
        <input class="form-control" id="family-code" name="code" maxlength="5" minlength="5"
            pattern="[A-HJ-NP-Z2-9a-hj-np-z]{5}" autocomplete="one-time-code" required
            style="max-width: 14rem; text-transform: uppercase;" aria-describedby="family-code-help">
        <button class="btn btn-primary" type="submit">Continue</button>
    </div>
    <div class="form-text" id="family-code-help">Enter the five-character code from your invitation email.</div>
    @error('code')
        <div class="text-danger mt-2">{{ $message }}</div>
    @enderror
</form>
