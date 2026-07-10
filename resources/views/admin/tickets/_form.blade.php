<div class="form-group">
    <label for="title">Judul Tiket</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" value="{{ old('title', $ticket->title ?? '') }}" required>
    @error('title')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="description">Deskripsi</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="5" required>{{ old('description', $ticket->description ?? '') }}</textarea>
    @error('description')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $ticket->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="priority_id">Prioritas</label>
            <select name="priority_id" id="priority_id" class="form-control @error('priority_id') is-invalid @enderror" required>
                <option value="">Pilih Prioritas</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->id }}" {{ old('priority_id', $ticket->priority_id ?? '') == $priority->id ? 'selected' : '' }}>
                        {{ $priority->priority_name }}
                    </option>
                @endforeach
            </select>
            @error('priority_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="due_date">Tenggat Waktu (opsional)</label>
    <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" id="due_date"
        value="{{ old('due_date', isset($ticket) && $ticket->due_date ? $ticket->due_date->format('Y-m-d\TH:i') : '') }}">
    @error('due_date')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
