<div class="form-group">
    <label for="label_name">Nama Label</label>
    <input type="text" name="label_name" class="form-control @error('label_name') is-invalid @enderror" id="label_name" value="{{ old('label_name', $label->label_name ?? '') }}" required>
    @error('label_name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
