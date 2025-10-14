<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Agenda Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <div class="container mt-4">
        <h2 class="mb-3">Tambah Agenda Baru</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('agenda.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="agenda_name">Nama Agenda <span style="color:red">*</span></label>
                <input type="text" id="agenda_name" name="agenda_name" class="form-control" value="{{ old('agenda_name') }}" required>
                @error('agenda_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                @error('description') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label for="date">Tanggal <span style="color:red">*</span></label>
                <input type="date" id="date" name="date" class="form-control" value="{{ old('date') }}" required>
                @error('date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label for="location">Lokasi</label>
                <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}">
                @error('location') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>

</body>
</html>
