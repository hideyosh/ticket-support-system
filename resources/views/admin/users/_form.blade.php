<div class="form-group">
    <label for="name">Nama</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name ?? '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email ?? '') }}" required>
    @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="password">Password {{ isset($user) ? '(Kosongkan jika tidak ingin mengubah)' : '' }}</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" {{ isset($user) ? '' : 'required' }}>
    @error('password')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="role_id">Role</label>
    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
        <option value="">Pilih Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
    </select>
    @error('role_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
