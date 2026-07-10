<div class="form-group">
    <label for="priority_name">Nama Prioritas</label>
    <input type="text" name="priority_name" class="form-control @error('priority_name') is-invalid @enderror" id="priority_name" value="{{ old('priority_name', $priority->priority_name ?? '') }}" required>
    @error('priority_name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
