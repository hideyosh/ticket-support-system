<div class="form-group">
    <label for="priority_id">Prioritas</label>
    <select name="priority_id" id="priority_id" class="form-control @error('priority_id') is-invalid @enderror" required>
        <option value="">Pilih Prioritas</option>
        @foreach($priorities as $priority)
            <option value="{{ $priority->id }}" {{ old('priority_id', $slaRule->priority_id ?? '') == $priority->id ? 'selected' : '' }}>
                {{ $priority->priority_name }}
            </option>
        @endforeach
    </select>
    @error('priority_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="response_time">Response Time (Jam)</label>
    <input type="number" name="response_time" class="form-control @error('response_time') is-invalid @enderror" id="response_time" value="{{ old('response_time', $slaRule->response_time ?? '') }}" min="1" required>
    @error('response_time')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="resolution_time">Resolution Time (Jam)</label>
    <input type="number" name="resolution_time" class="form-control @error('resolution_time') is-invalid @enderror" id="resolution_time" value="{{ old('resolution_time', $slaRule->resolution_time ?? '') }}" min="1" required>
    @error('resolution_time')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
